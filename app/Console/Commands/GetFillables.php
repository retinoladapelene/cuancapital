<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class GetFillables extends Command
{
    protected $signature = 'get:fillables';
    protected $description = 'Get fillable columns for models';

    public function handle()
    {
        $models = [
            'App\Models\StrategyBlueprint',
            'App\Models\StrategicAnalysis',
            'App\Models\RoadmapStep',
            'App\Models\RoadmapProgress',
            'App\Models\RoadmapAction',
            'App\Models\Roadmap',
            'App\Models\ApiLog',
            'App\Models\AdArsenal',
            'App\Models\ActivityLog'
        ];

        foreach ($models as $m) {
            if (!class_exists($m)) continue;
            $t = (new $m)->getTable();
            $cols = array_diff(Schema::getColumnListing($t), ['id', 'created_at', 'updated_at']);
            $this->info($m . '|' . implode(',', $cols));
        }
    }
}
