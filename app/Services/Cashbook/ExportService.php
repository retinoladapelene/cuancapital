<?php

namespace App\Services\Cashbook;

use App\Models\CashbookAccount;
use App\Models\CashbookCategory;
use App\Models\CashbookTransaction;
use App\Models\CashbookMonthlySummary;
use App\Models\CashbookDisciplineLog;
use App\Models\CashbookReflection;
use App\Models\CashbookExportLog;
use App\Models\CashbookBudget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ExportService
{
    /**
     * Generate a verifiable JSON payload for full data export.
     */
    public function generateFullJSONExport(int $userId): array
    {
        // 1. Gather all data
        $data = [
            'metadata' => [
                'export_date' => now()->toDateTimeString(),
                'user_id' => $userId,
                'version' => '2.0-fintech-grade',
            ],
            'accounts' => CashbookAccount::where('user_id', $userId)->get()->toArray(),
            'categories' => CashbookCategory::where('user_id', $userId)->get()->toArray(),
            'budgets' => CashbookBudget::where('user_id', $userId)->get()->toArray(),
            'transactions' => CashbookTransaction::where('user_id', $userId)->withTrashed()->get()->toArray(),
            'summaries' => CashbookMonthlySummary::where('user_id', $userId)->get()->toArray(),
            'discipline_logs' => CashbookDisciplineLog::where('user_id', $userId)->get()->toArray(),
            'reflections' => CashbookReflection::where('user_id', $userId)->get()->toArray(),
        ];

        // 2. Hash the data array to create a tamper-evident seal
        $jsonString = json_encode($data);
        $fileHash = hash('sha256', $jsonString);

        // 3. Attach the security seal
        $data['security_seal'] = [
            'algorithm' => 'SHA-256',
            'hash' => $fileHash,
        ];

        // 4. Log the export for audit tracking
        CashbookExportLog::create([
            'user_id' => $userId,
            'export_type' => 'json',
            'file_hash' => $fileHash,
            'ip_address' => Request::ip() ?? '127.0.0.1',
        ]);

        return $data;
    }

    /**
     * Process an imported JSON file and restore user data.
     */
    public function processJSONImport(int $userId, array $data): bool
    {
        // Basic validation
        if (!isset($data['metadata']['user_id']) || !isset($data['accounts'])) {
            throw new \Exception("Invalid JSON format.");
        }

        // Helper to convert ISO8601 dates to MySQL format
        $formatDate = function ($dateStr) {
            if (!$dateStr) return null;
            return date('Y-m-d H:i:s', strtotime($dateStr));
        };

        DB::beginTransaction();
        try {
            // 1. Wipe existing data for this user
            CashbookReflection::where('user_id', $userId)->delete();
            CashbookDisciplineLog::where('user_id', $userId)->delete();
            CashbookMonthlySummary::where('user_id', $userId)->delete();
            CashbookTransaction::where('user_id', $userId)->forceDelete(); // force delete to clean up everything
            CashbookBudget::where('user_id', $userId)->delete();
            CashbookCategory::where('user_id', $userId)->delete();
            CashbookAccount::where('user_id', $userId)->delete();

            // 2. Insert Accounts (maintain IDs if possible by mapping, or just force insert)
            // To ensure referential integrity, we map old IDs to new IDs
            $accountMap = [];
            if (!empty($data['accounts'])) {
                foreach ($data['accounts'] as $acc) {
                    $oldId = $acc['id'];
                    unset($acc['id']);
                    $acc['user_id'] = $userId;
                    $acc['created_at'] = $formatDate($acc['created_at'] ?? now());
                    $acc['updated_at'] = $formatDate($acc['updated_at'] ?? now());
                    $newId = CashbookAccount::insertGetId($acc);
                    $accountMap[$oldId] = $newId;
                }
            }

            // 3. Insert Categories
            $categoryMap = [];
            if (!empty($data['categories'])) {
                foreach ($data['categories'] as $cat) {
                    $oldId = $cat['id'];
                    unset($cat['id']);
                    $cat['user_id'] = $userId;
                    $cat['created_at'] = $formatDate($cat['created_at'] ?? now());
                    $cat['updated_at'] = $formatDate($cat['updated_at'] ?? now());
                    $newId = CashbookCategory::insertGetId($cat);
                    $categoryMap[$oldId] = $newId;
                }
            }

            // 3.5 Insert Budgets
            if (!empty($data['budgets'])) {
                $bgtBatch = [];
                foreach ($data['budgets'] as $bgt) {
                    unset($bgt['id']);
                    $bgt['user_id'] = $userId;
                    if (isset($bgt['category_id']) && $bgt['category_id']) {
                        $bgt['category_id'] = $categoryMap[$bgt['category_id']] ?? $bgt['category_id'];
                    }
                    $bgt['created_at'] = $formatDate($bgt['created_at'] ?? now());
                    $bgt['updated_at'] = $formatDate($bgt['updated_at'] ?? now());
                    $bgtBatch[] = $bgt;

                    if (count($bgtBatch) >= 500) {
                        CashbookBudget::insert($bgtBatch);
                        $bgtBatch = [];
                    }
                }
                if (count($bgtBatch) > 0) {
                    CashbookBudget::insert($bgtBatch);
                }
            }

            // 4. Insert Transactions
            // We use chunking to prevent memory exhaustion, and map the IDs
            if (!empty($data['transactions'])) {
                $txBatch = [];
                foreach ($data['transactions'] as $tx) {
                    unset($tx['id']);
                    $tx['user_id'] = $userId;
                    $tx['account_id'] = $accountMap[$tx['account_id']] ?? $tx['account_id'];
                    if (isset($tx['to_account_id']) && $tx['to_account_id']) {
                        $tx['to_account_id'] = $accountMap[$tx['to_account_id']] ?? $tx['to_account_id'];
                    }
                    if (isset($tx['category_id']) && $tx['category_id']) {
                        $tx['category_id'] = $categoryMap[$tx['category_id']] ?? $tx['category_id'];
                    }
                    if (isset($tx['transaction_date'])) {
                        $tx['transaction_date'] = $formatDate($tx['transaction_date']);
                    }
                    if (isset($tx['deleted_at'])) {
                        $tx['deleted_at'] = $formatDate($tx['deleted_at']);
                    }
                    $tx['created_at'] = $formatDate($tx['created_at'] ?? now());
                    $tx['updated_at'] = $formatDate($tx['updated_at'] ?? now());
                    $txBatch[] = $tx;

                    if (count($txBatch) >= 500) {
                        CashbookTransaction::insert($txBatch);
                        $txBatch = [];
                    }
                }
                if (count($txBatch) > 0) {
                    CashbookTransaction::insert($txBatch);
                }
            }

            // 5. Insert Summaries, Logs, Reflections
            $skipMapTables = [
                'summaries' => CashbookMonthlySummary::class,
                'discipline_logs' => CashbookDisciplineLog::class,
                'reflections' => CashbookReflection::class,
            ];

            foreach ($skipMapTables as $key => $modelClass) {
                if (!empty($data[$key])) {
                    $batch = [];
                    foreach ($data[$key] as $row) {
                        unset($row['id']);
                        $row['user_id'] = $userId;
                        if (isset($row['created_at'])) $row['created_at'] = $formatDate($row['created_at']);
                        if (isset($row['updated_at'])) $row['updated_at'] = $formatDate($row['updated_at']);
                        if (isset($row['deleted_at'])) $row['deleted_at'] = $formatDate($row['deleted_at']);
                        if (isset($row['date'])) $row['date'] = $formatDate($row['date']);
                        if (isset($row['log_date'])) $row['log_date'] = $formatDate($row['log_date']);
                        if (isset($row['month_year'])) $row['month_year'] = $formatDate($row['month_year']);
                        $batch[] = $row;
                        if (count($batch) >= 500) {
                            $modelClass::insert($batch);
                            $batch = [];
                        }
                    }
                    if (count($batch) > 0) {
                        $modelClass::insert($batch);
                    }
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
