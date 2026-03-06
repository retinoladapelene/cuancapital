<?php

namespace App\Http\Controllers\Cashbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Cashbook\ExportService;

class ExportController extends Controller
{
    protected ExportService $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function download(Request $request)
    {
        $payload = $this->exportService->generateFullJSONExport($request->user()->id);

        return response()->json($payload)
            ->header('Content-Disposition', 'attachment; filename="cuan_cashbook_export_' . date('Y_m_d_His') . '.json"');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimetypes:application/json,text/plain'
        ]);

        $fileContent = file_get_contents($request->file('file')->getRealPath());
        $data = json_decode($fileContent, true);

        if (!$data || !is_array($data)) {
            return response()->json(['message' => 'Format file tidak valid atau rusak'], 400);
        }

        try {
            $this->exportService->processJSONImport($request->user()->id, $data);
            return response()->json(['message' => 'Data berhasil dipulihkan (Restore Sukses)']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memulihkan data: ' . $e->getMessage()], 500);
        }
    }
}
