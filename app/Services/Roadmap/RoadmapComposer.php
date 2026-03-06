<?php

namespace App\Services\Roadmap;

use Illuminate\Support\Collection;

class RoadmapComposer
{
    /**
     * Assemble ranked steps into phases and produce the final roadmap payload.
     *
     * @param Collection $rankedSteps  Steps sorted by priority_score (desc)
     * @param array      $strategy     { primary_strategy, secondary_strategy, focus_area }
     * @param float      $confidence   0-100 from ScoringEngine
     * @return array  Final roadmap payload
     */
    public function compose(Collection $rankedSteps, array $strategy, float $confidence): array
    {
        // ── Phase Buckets ─────────────────────────────────────────────────────────
        $quickWins = collect();
        $growth    = collect();
        $scaling   = collect();

        foreach ($rankedSteps as $dto) {
            // Unpack the DTO produced by RoadmapScoringEngine
            $step  = $dto['model'];
            $score = $dto['priority_score'];
            $difficulty = $step->difficulty ?? 5;

            if ($difficulty <= 3) {
                $quickWins->push($this->formatStep($step, 'quick_win', $score));
            } elseif ($difficulty <= 6) {
                $growth->push($this->formatStep($step, 'growth', $score));
            } else {
                $scaling->push($this->formatStep($step, 'scaling', $score));
            }
        }

        // ── Phase Objects ─────────────────────────────────────────────────────────
        $phases = [];

        if ($quickWins->isNotEmpty()) {
            $phases[] = [
                'name'        => 'Quick Wins',
                'description' => 'Tindakan mudah dengan dampak langsung. Mulai dari sini.',
                'duration'    => '1-2 minggu',
                'steps'       => $quickWins->values()->toArray(),
            ];
        }

        if ($growth->isNotEmpty()) {
            $phases[] = [
                'name'        => 'Growth Engine',
                'description' => 'Strategi medium-term untuk membangun momentum.',
                'duration'    => '1-2 bulan',
                'steps'       => $growth->values()->toArray(),
            ];
        }

        if ($scaling->isNotEmpty()) {
            $phases[] = [
                'name'        => 'Scale Up',
                'description' => 'Eksekusi lanjutan setelah fondasi kuat.',
                'duration'    => '3-6 bulan',
                'steps'       => $scaling->values()->toArray(),
            ];
        }

        // ── Confidence Label ──────────────────────────────────────────────────────
        $reliabilityLabel = match (true) {
            $confidence >= 75 => 'High',
            $confidence >= 50 => 'Medium',
            default           => 'Low',
        };

        // ── Final Payload ─────────────────────────────────────────────────────────
        return [
            'summary'          => "Roadmap ini berfokus pada strategi **{$strategy['primary_strategy']}**.",
            'strategy'         => $strategy,
            'confidence_score' => $confidence,
            'reliability'      => $reliabilityLabel,
            'total_steps'      => $rankedSteps->count(),
            'phases'           => $phases,
        ];
    }

    // ── Helpers ───────────────────────────────────────────────────────────────────

    private function formatStep($step, string $phase, float $priorityScore = 0): array
    {
        return [
            'id'              => $step->id,
            'title'           => $step->title,
            'description'     => $step->description,
            'category'        => $step->category,
            'impact_score'    => $step->impact,
            'difficulty_score'=> $step->difficulty,
            'priority_score'  => $priorityScore,
            'estimated_time'  => $step->required_time,
            'outcome_type'    => $step->outcome_type,
            'phase'           => $phase,
            'reasoning'       => $this->buildReasoning($step, $phase),
            'actions'         => $this->getFallbackActions($step->category, $step->title),
        ];
    }

    private function buildReasoning($step, string $phase): string
    {
        $impact = $step->impact >= 8 ? 'dampak tinggi' : ($step->impact >= 5 ? 'dampak sedang' : 'dampak rendah');
        $ease   = $step->difficulty <= 3 ? 'mudah dikerjakan' : ($step->difficulty <= 6 ? 'butuh effort sedang' : 'perlu keahlian lebih');

        return "Step ini diprioritaskan karena {$impact} dan {$ease}. " .
               ($phase === 'quick_win' ? 'Cocok sebagai langkah awal.' : '');
    }

    private function getFallbackActions(?string $category, string $title): array
    {
        return match ($category) {
            'traffic' => [
                'Buat Content Calendar 1 Bulan penuh (30 ide konten)',
                'Setup & Verifikasi Meta Business Manager / TikTok Ads',
                'Re-purpose 1 materi konten ke format Reels, TikTok, dan Shorts',
                'Test Campaign Budget Optimization dengan budget kecil (50rb/hari)',
                'Pantau Cost Per Click (CPC) dan Click Through Rate (CTR)',
                'Optimaliasi Bio profile & link tree',
            ],
            'conversion' => [
                'Test 3 headline berbeda pada Landing Page',
                'Tambahkan Garansi Uang Kembali atau bonus tambahan',
                'Percepat waktu muat (loading time) halaman website/layanan',
                'Pasang social proof (Testimonial/Review pelanggan asli)',
                'Gunakan Call-to-Action button dengan warna mencolok',
                'Identifikasi produk komplementer untuk Upsell / Cross-sell',
            ],
            'finance' => [
                'List semua Fixed Cost bulanan secara mendetail',
                'Cek harga modal (COGS) vs Harga Jual saat ini',
                'Negosiasi ulang term pembayaran dengan supplier',
                'Eliminasi biaya operasional software/layanan yang tidak krusial',
                'Buat target efisiensi pengeluaran minimum 10%',
                'Rekapitulasi rasio Profit Margin setiap akhir minggu',
            ],
            'operations' => [
                'Automatisasi flow Customer Service FAQ dengan bot/template',
                'Delegasikan tugas rutin berulang ke staf junior/VA',
                'Audit SOP workflow tim internal secara berkala',
                'Integrasi sistem notifikasi pesanan otomatis',
                'Perbarui database kontak supplier & vendor',
                'Review bottleneck produksi atau waktu pengiriman'
            ],
            default => [
                'Lakukan riset mendalam terkait langkah ini',
                'Tentukan penanggung jawab (PIC) eksekusi',
                'Buat timeline tenggat waktu penyelesaian',
                'Eksekusi strategi sesuai guideline awal',
                'Monitor hasil metrik setiap akhir pekan',
                'Lakukan penyesuaian evaluasi iteratif'
            ]
        };
    }
}
