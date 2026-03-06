<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReverseGoalPlannerService
{
    /**
     * Module 1: Input Controller & Benchmark Assignment
     * 
     * Validates input and merges with server-side benchmarks.
     * 
     * @param array $input
     * @return array
     */
    public function process(array $input)
    {
        // 1. Validate Input (Basic validation should be done in Controller)
        // Here we ensure data types and fallback values
        
        $businessModel = $input['business_model'] ?? 'dropship';
        
        // 2. Fetch Benchmarks (Assumption Lock)
        // If DB is empty (during dev), use hardcoded fallbacks
        $benchmark = DB::table('benchmark_assumptions')
            ->where('business_model', $businessModel)
            ->first();

        // Fallback if no benchmark found in DB
        if (!$benchmark) {
            $benchmark = (object) [
                'avg_margin' => 20,
                'avg_conversion' => 1.5,
                'avg_cpc' => 1500,
                'traffic_capacity_per_hour' => 100,
                'difficulty_index' => 1.0
            ];
            // Hardcoded fallbacks for other models
            if ($businessModel === 'digital') {
                $benchmark->avg_margin = 80;
                $benchmark->avg_conversion = 3.0;
                $benchmark->avg_cpc = 2000;
            }
        }

        // 3. Merge Input with Benchmarks
        // User inputs: correct logic ensures we use benchmarks for margin/cvr/cpc
        // But we might allow user overrides if they are "advanced" - for now, strict mode
        
        $data = [
            'target_profit' => (float) $input['target_profit'],
            'timeline_days' => (int) $input['timeline_days'],
            'capital_available' => (float) $input['capital_available'],
            'hours_per_day' => (int) $input['hours_per_day'],
            'business_model' => $businessModel,
            'traffic_strategy' => $input['traffic_strategy'] ?? 'ads',
            'selling_price' => (float) ($input['selling_price'] ?? 0), 

            // V3 specific inputs
            'fixed_costs' => (float) ($input['fixed_costs'] ?? 0),
            'return_rate' => (float) ($input['return_rate'] ?? 0), // percentage
            'warehouse_cost' => (float) ($input['warehouse_cost'] ?? 0),
            'affiliate_rate' => (float) ($input['affiliate_rate'] ?? 0), // percentage
            'capacity' => (int) ($input['capacity'] ?? 10),

            // Locked Assumptions (allow override)
            'assumed_margin' => (!empty($input['custom_margin']) && $input['custom_margin'] > 0) 
                                ? (float) $input['custom_margin'] 
                                : $benchmark->avg_margin,
            
            'assumed_conversion' => $benchmark->avg_conversion,
            'assumed_cpc' => $benchmark->avg_cpc,
            'traffic_capacity' => $benchmark->traffic_capacity_per_hour,
        ];


        // 4. Run Calculation Engine (Module 2)
        $calculations = $this->runCalculations($data);
        
        if (isset($calculations['feasible']) && $calculations['feasible'] === false) {
             return [
                'input' => $data,
                'output' => $calculations, // Contains reason
                'scores' => [
                    'goal_status' => 'Model Tidak Layak',
                    'status_color' => 'red',
                    'constraint_message' => $calculations['reason'],
                    'recommendations' => [],
                    'ofs' => 0
                ],
                'logic_version' => 'v2.0_infeasible_guard'
            ];
        }

        // 5. Run Scoring Engine (Module 3)
        $scores = $this->calculateScores($data, $calculations);

        // 6. Run Adjustment Engine (Module 4) - only if Needed or requested
        // For now, we return scores and calculations
        
        return [
            'input' => $data,
            'output' => $calculations,
            'scores' => $scores,
            'logic_version' => 'v2.0_server_beta'
        ];
    }

    /**
     * Module 2: Calculation Engine
     * 
     * Performs core math for Reverse Engineering:
     * - Required Units
     * - Required Traffic
     * - Required Budget
     * - Workload
     */
    private function runCalculations(array $data)
    {
        // ─── A. UNIT ECONOMICS ────────────────────────────────────────────────
        $sellingPrice = $data['selling_price'] ?? 150000;
        $marginPercent = $data['assumed_margin'] / 100;
        $businessModel = $data['business_model'];
        
        // Base Unit Cost (COGS)
        $cogs = $sellingPrice * (1 - $marginPercent);
        
        // --- MODEL SPECIFIC UNIT ECONOMICS ---
        $affiliateCostPerUnit = 0;
        $lossPerReturn = 0;
        $effectiveReturnRate = 0;
        
        if ($businessModel === 'digital') {
            // Affiliate commission cuts into the margin per unit
            $affiliateRate = $data['affiliate_rate'] / 100;
            $affiliateCostPerUnit = $sellingPrice * $affiliateRate;
        }

        if ($businessModel === 'dropship') {
            // RTS (Return to Sender) reduces effective margin because of wasted shipping/COD fees
            $effectiveReturnRate = $data['return_rate'] / 100;
            // Simplified: Assume a flat $1.5 loss (or Rp 20,000) per returned item for shipping fees
            // We'll calculate total loss later based on traffic.
            $lossPerReturn = 20000; 
        }
        
        $grossProfitPerUnit = $sellingPrice - $cogs - $affiliateCostPerUnit;

        // Calculate CAC/CPA (Cost Per Acquisition)
        $isOrganic = $data['traffic_strategy'] === 'organic';
        $cpc = $data['assumed_cpc'];
        $conversionRate = $data['assumed_conversion'] / 100;
        
        $cpa = ($conversionRate > 0) ? ($cpc / $conversionRate) : 0;
        if ($isOrganic) {
            $cpa = 0;
        }

        // Net Margin per Unit (Contribution Margin before fixed costs)
        // If dropship, CPA effectively increases because we pay for ads on returned items too
        // Effective CPA = CPA / (1 - Return Rate)
        if ($businessModel === 'dropship' && $effectiveReturnRate < 1) {
             $cpa = $cpa / (1 - $effectiveReturnRate);
        }

        $netMarginPerUnit = $grossProfitPerUnit - $cpa;

        // ─── B. VOLUME & TARGET REQUIREMENTS ──────────────────────────────────
        
        // In V3, target profit must also cover fixed costs (Gaji Tim, Warehouse)
        $totalFixedCosts = $data['fixed_costs'];
        if ($businessModel === 'stock') {
            $totalFixedCosts += $data['warehouse_cost'];
        }

        $requiredGrossProfit = $data['target_profit'] + $totalFixedCosts;
        
        // If Model is dropship, we add expected return loss to the required gross profit
        // This is a circular calculation, so we approximate:
        // Required Gross = Target + Fixed + (RequiredUnits * ReturnRate * LossPerReturn)
        
        if ($netMarginPerUnit <= 0) {
            return [
                'feasible' => false,
                'reason' => 'Biaya akuisisi (CPA) atau Komisi Afiliasi terlalu besar sehingga Margin per Sales Negatif (Boncos).',
                'debug' => [
                    'selling_price' => $sellingPrice,
                    'gross_profit' => $grossProfitPerUnit,
                    'cpa' => $cpa,
                    'net_margin' => $netMarginPerUnit,
                    'breakdown' => [
                        'Harga Jual' => number_format($sellingPrice, 0, ',', '.'),
                        'HPP (COGS)' => number_format($cogs, 0, ',', '.'),
                        'Komisi Afiliasi' => number_format($affiliateCostPerUnit, 0, ',', '.'),
                        'Biaya Iklan per Sales (CPA)' => number_format($cpa, 0, ',', '.'),
                        'Net Rugi per Unit' => number_format($netMarginPerUnit, 0, ',', '.')
                    ]
                ]
            ];
        } 
        
        // Calculate Required Units
        if ($businessModel === 'dropship') {
             // Units = (Target + Fixed) / (NetMargin - (ReturnRate * LossPerReturn))
             $adjustedNetMargin = $netMarginPerUnit - ($effectiveReturnRate * $lossPerReturn);
             if ($adjustedNetMargin <= 0) {
                 return [
                    'feasible' => false,
                    'reason' => 'Tingkat Return (RTS) terlalu tinggi, menghabiskan seluruh sisa margin profit Anda.',
                    'debug' => [
                        'selling_price' => $sellingPrice,
                        'net_margin_before_rts' => $netMarginPerUnit,
                        'breakdown' => [
                            'Net Margin Awal' => number_format($netMarginPerUnit, 0, ',', '.'),
                            'Estimasi Retur' => ($effectiveReturnRate*100) . '%',
                            'Rugi Retur per Sukses' => number_format($effectiveReturnRate * $lossPerReturn, 0, ',', '.'),
                            'Net Rugi Akhir' => number_format($adjustedNetMargin, 0, ',', '.')
                        ]
                    ]
                ];
             }
             $requiredUnits = ceil($requiredGrossProfit / $adjustedNetMargin);
             
             // The actual units shipped will be higher to account for returns
             $totalUnitsShipped = ceil($requiredUnits / (1 - $effectiveReturnRate));
        } else {
             $requiredUnits = ceil($requiredGrossProfit / $netMarginPerUnit);
             $totalUnitsShipped = $requiredUnits;
        }

        // ─── C. CAPACITY & FEASIBILITY CHECKS ────────────────────────────────
        
        if ($businessModel === 'service') {
             $months = ceil($data['timeline_days'] / 30);
             $maxCapacityOverTimeline = $data['capacity'] * $months;
             
             if ($requiredUnits > $maxCapacityOverTimeline) {
                 return [
                    'feasible' => false,
                    'reason' => 'Target klien (' . $requiredUnits . ') melebihi kapasitas layanan maksimal Anda (' . $maxCapacityOverTimeline . ' Klien).',
                    'debug' => [
                        'required_clients' => $requiredUnits,
                        'max_capacity' => $maxCapacityOverTimeline,
                        'breakdown' => [
                            'Target Profit' => number_format($data['target_profit'], 0, ',', '.'),
                            'Total Klien Dibutuhkan' => $requiredUnits,
                            'Kapasitas Maksimal' => $maxCapacityOverTimeline,
                            'Kekurangan Kapasitas' => ($requiredUnits - $maxCapacityOverTimeline) . ' Klien'
                        ]
                    ]
                ];
             }
        }

        // ─── D. TRAFFIC REQUIREMENTS ──────────────────────────────────────────
        // For dropship, traffic must generate the *Total* units (including those that will return)
        $requiredTraffic = ($conversionRate > 0) ? ceil($totalUnitsShipped / $conversionRate) : 0;
        
        // ─── E. BUDGET REQUIREMENTS ───────────────────────────────────────────
        $adSpend = $isOrganic ? 0 : ($requiredTraffic * $cpc);
        
        // Total Capital Needed = Ad Spend + Fixed Costs
        // If stock/retail, add initial COGS for required units (Inventory buffer)
        $capitalRequired = $adSpend + $totalFixedCosts;
        $stockCapital = 0;
        
        if ($businessModel === 'stock') {
            // Need to buy stock upfront. Let's assume they buy 50% of targeted sales upfront.
            $stockCapital = ($requiredUnits * 0.5) * $cogs;
            $capitalRequired += $stockCapital;
        }

        // ─── F. WORKLOAD / EXECUTION ──────────────────────────────────────────
        // Service models take much more time per unit (client) than physical products
        $hoursPerUnit = 0.16; // ~10 mins for e-com/dropship
        if ($businessModel === 'service') {
            $hoursPerUnit = 10; // 10 hours per client project
        } elseif ($businessModel === 'digital') {
            $hoursPerUnit = 0.05; // 3 mins (mostly automated)
        }
        
        $totalOperationalHours = $requiredUnits * $hoursPerUnit;
        
        $days = $data['timeline_days'];
        $dailyOperationalHours = ($days > 0) ? ($totalOperationalHours / $days) : 999;
        
        $marketingHoursDaily = $isOrganic ? 2 : 1;
        
        $totalDailyHoursNeeded = $dailyOperationalHours + $marketingHoursDaily;
        $executionGap = $totalDailyHoursNeeded - $data['hours_per_day'];
        
        // Execution Load Ratio (Required / Available)
        $executionLoadRatio = ($data['hours_per_day'] > 0) ? ($totalDailyHoursNeeded / $data['hours_per_day']) : 999;

        // Gap calculation
        $capitalGap = $capitalRequired - $data['capital_available'];

        // Gross Revenue
        $grossRevenue = $requiredUnits * $sellingPrice;
        
        // Net Profit (after all deductions)
        $netProfit = $data['target_profit']; // by design, this is what remains
        
        // Profit/month ratio
        $months = max(1, ceil($data['timeline_days'] / 30));
        $netProfitPerMonth = $netProfit / $months;

        return [
            'selling_price' => $sellingPrice,
            'unit_profit' => $netMarginPerUnit,
            'required_units' => $requiredUnits,
            'required_traffic' => $requiredTraffic,
            'total_ad_spend' => $adSpend,
            'total_capital_needed' => $capitalRequired,
            'stock_capital' => $stockCapital,
            'fixed_costs' => $totalFixedCosts,
            'daily_hours_needed' => $totalDailyHoursNeeded,
            'execution_load_ratio' => $executionLoadRatio,
            'capital_gap' => $capitalGap,
            'cpa' => $cpa,
            'gross_revenue' => $grossRevenue,
            'net_profit' => $netProfit,
            'net_profit_per_month' => $netProfitPerMonth,
            'cogs_per_unit' => $cogs,
            'affiliate_cost_per_unit' => $affiliateCostPerUnit,
        ];
    }
    
    /**
     * Module 3: Scoring Engine
     * 
     * Calculates FFS, CAS, EFS, OFS using non-linear logic.
     */
    private function calculateScores($data, $calc)
    {
        // 1. Financial Feasibility Score (FFS)
        // Based on Margin and Ticket Size (simulated)
        // Actually, FFS in V1 was about "Is the goal profit realistic given the capital?"
        // In V2, we have CAS for Capital.
        // FFS can be about "Is the unit economics sound?"
        // If Margin < 10%, FFS low. If Margin > 30%, FFS high.
        
        $marginScore = min(100, ($data['assumed_margin'] / 30) * 100); 
        // If margin is 30%, score 100. If 15%, score 50.
        
        // 2. Capital Adequacy Score (CAS)
        // Based on Capital Gap.
        // If Gap <= 0, CAS = 100.
        // If AdSpend is 0 (organic) AND no fixed costs/stock, CAS = 100.
        
        $totalCapitalNeeded = $calc['total_capital_needed'] ?? $calc['total_ad_spend'];
        
        if ($totalCapitalNeeded <= 0) {
            $cas = 100;
        } else {
            $coverage = $data['capital_available'] / $totalCapitalNeeded;
            // Non-linear: sqrt(coverage) * 100
            // If coverage is 0.5 (50% of budget), sqrt(0.5) = 0.7. Score 70.
            // If coverage is 0.1, sqrt(0.1) = 0.31. Score 31.
            $cas = min(100, sqrt($coverage) * 100);
        }
        
        // 3. Execution Feasibility Score (EFS)
        // Based on Load Ratio.
        // If Ratio <= 1, EFS = 100.
        // If Ratio > 1, EFS drops.
        
        if ($calc['execution_load_ratio'] <= 1) {
            $efs = 100;
        } else {
            // If needs 2x hours, score should be low.
            // Formula: 100 / ratio ^ 1.5
            // Ratio 2 -> 100 / 2.8 = 35.
            $efs = 100 / pow($calc['execution_load_ratio'], 1.5);
        }
        
        // 4. Overall Feasibility Score (OFS)
        // Weighted average? Or lowest bucket?
        // Let's use weighted: CAS (40%), EFS (40%), FFS (20%)
        // BUT, if any score is very low (< 30), tank the OFS.
        
        // 4. Overall Feasibility Score (OFS)
        $ofs = ($cas * 0.4) + ($efs * 0.4) + ($marginScore * 0.2);
        
        // --- NEW LOGIC: Mentally Safe Output ---
        
        // A. Goal Status (Simple 3 Levels)
        $threshold = ($data['business_model'] === 'dropship') ? 75 : 65;
        
        if ($ofs >= $threshold + 10) {
            $goalStatus = 'Siap Dieksekusi'; // Stable
            $statusColor = 'green';
        } elseif ($ofs >= $threshold - 10) {
            $goalStatus = 'Perlu Penyesuaian'; // Adjustable
            $statusColor = 'yellow';
        } else {
            $goalStatus = 'Terlalu Berat Saat Ini'; // Heavy
            $statusColor = 'red';
        }

        // B. Constraint Prioritization (Identify Primary Constraint)
        $constraints = [];

        // 1. Capital Constraint (Severe if Cover < 50%)
        if ($calc['capital_gap'] > 0) {
            $severity = ($calc['capital_gap'] / $totalCapitalNeeded) * 100; // % gap
            $constraints['capital'] = [
                'name' => 'Keterbatasan Modal Awal',
                'severity' => $severity,
                'msg' => 'Modal saat ini tidak cukup untuk belanja stok, biaya operasi, atau iklan di fase awal.'
            ];
        }

        // 2. Execution Constraint (Severe if Load > 1.5x)
        if ($calc['execution_load_ratio'] > 1) {
            $severity = ($calc['execution_load_ratio'] - 1) * 100; // % overload
            // Weight execution lower than money? or higher?
            // Capital is usually harder to solve instantly. Execution can be solved by "Working harder" (up to a point).
            // Let's weight capital 1.5x
            $constraints['execution'] = [
                'name' => 'Beban Kerja Harian',
                'severity' => $severity * 0.8, 
                'msg' => 'Target ini membutuhkan waktu kerja melebihi ketersediaan waktu Anda.'
            ];
        }

        // 3. Margin Constraint (Severe if < 15%)
        if ($data['assumed_margin'] < 20 && $data['business_model'] !== 'dropship') {
             $constraints['margin'] = [
                'name' => 'Margin Profit Tipis',
                'severity' => (20 - $data['assumed_margin']) * 5, 
                'msg' => 'Model bisnis ini memiliki margin tipis, membutuhkan volume penjualan sangat tinggi.'
            ];
        }

        // Sort by severity
        uasort($constraints, function($a, $b) {
            return $b['severity'] <=> $a['severity'];
        });

        $primary = reset($constraints);
        if (!$primary) {
            $primary = [
                'name' => 'Konsistensi',
                'severity' => 0,
                'msg' => 'Tantangan utama Anda hanyalah menjaga konsistensi eksekusi.'
            ];
        }

        // C. Safe Recommendations
        $recommendations = [];
        
        // Logic to generate 2 safe options
        if ($goalStatus === 'Terlalu Berat Saat Ini' || $goalStatus === 'Perlu Penyesuaian') {
            // Option A: Extend Timeline
            $newTimeline = ceil($data['timeline_days'] * 1.5); // +50% time
            $recommendations[] = [
                'type' => 'timeline',
                'label' => "Perpanjang waktu menjadi {$newTimeline} hari",
                'value' => $newTimeline
            ];

            // Option B: Adjust Target (only if Capital is the issue)
            if (isset($constraints['capital'])) {
                // Reduce target to match capital
                // New Target = Old Target * (Available / Needed)
                $ratio = $data['capital_available'] / ($calc['total_ad_spend'] ?: 1); // avoid div 0
                $newTarget = floor($data['target_profit'] * $ratio * 0.9); // 90% of max possible
                // Round to millions
                $newTarget = round($newTarget / 1000000) * 1000000;
                if ($newTarget > 0) {
                     $recommendations[] = [
                        'type' => 'target',
                        'label' => "Sesuaikan target profit menjadi " . number_format($newTarget,0,',','.') . " (Sesuai Modal)",
                        'value' => $newTarget
                    ];
                }
            }
        }
        
        // Limit to 2
        $recommendations = array_slice($recommendations, 0, 2);


        // D. Learning Moment (V3 Enhanced - Model Specific + V3 Awareness)
        $learningMoment = "";
        $businessModel = $data['business_model'];
        
        if ($businessModel === 'dropship') {
            $rts = $data['return_rate'] ?? 0;
            if ($rts > 15) {
                $learningMoment = "⚠️ Return Rate {$rts}% cukup tinggi! Pastikan deskripsi produk jujur dan ekspedisi reliable untuk menekan angka ini.";
            } else {
                $learningMoment = "💡 Dropshipping mengandalkan volume. Margin 20-30% standar industri, fokuslah pada Traffic dan minimalisir RTS.";
            }
        } elseif ($businessModel === 'digital') {
            $affRate = $data['affiliate_rate'] ?? 0;
            if ($affRate > 40) {
                $learningMoment = "💡 Komisi afiliasi {$affRate}% menarik para reseller, tapi memakan margin besar. Pastikan harga jual sudah memperhitungkannya.";
            } else {
                $learningMoment = "💡 Produk digital margin tinggi, nyaris tanpa COGS. Fokuslah pada funneling dan membangun email list.";
            }
        } elseif ($businessModel === 'service') {
            $cap = $data['capacity'] ?? 10;
            $learningMoment = "💡 Dengan kapasitas {$cap} klien/bulan, naikkan harga jual per project daripada menambah volume. Kualitas > Kuantitas.";
        } elseif ($businessModel === 'stock') {
            $wh = $data['warehouse_cost'] ?? 0;
            if ($wh > 0) {
                $learningMoment = "📦 Warehouse Rp " . number_format($wh, 0, ',', '.') . "/bulan menambah fixed cost. Pastikan stok turnover cepat agar modal tidak menggendut.";
            } else {
                $learningMoment = "💡 Bisnis stok butuh modal awal besar untuk beli barang. Pastikan cash flow Anda bisa memutar stok minimal 2x/bulan.";
            }
        } else {
            $learningMoment = "💡 Bisnis adalah maraton—bukan sprint. Konsistensi mengalahkan intensitas jangka pendek.";
        }

        return [
            // Backend Scores (Hidden from User)
            'ffs' => round($marginScore),
            'cas' => round($cas),
            'efs' => round($efs),
            'ofs' => round($ofs),
            
            // Frontend Output (Mentally Safe)
            'goal_status' => $goalStatus,
            'status_color' => $statusColor,
            'primary_constraint' => $primary['name'],
            'constraint_message' => $primary['msg'],
            'recommendations' => $recommendations,
            'learning_moment' => $learningMoment,
            
            // Integration Data
            'mentor_focus_area' => isset($constraints['capital']) ? 'low_capital_strategy' : 'growth_strategy',
        ];
    }
}
