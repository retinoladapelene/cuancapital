<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApiLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $endpoints = [
            ['method' => 'GET', 'path' => '/api/me/roadmaps', 'base_latency' => 80],
            ['method' => 'POST', 'path' => '/api/auth/login', 'base_latency' => 250],
            ['method' => 'POST', 'path' => '/api/mentor/evaluate', 'base_latency' => 1200],
            ['method' => 'GET', 'path' => '/api/blueprints/active', 'base_latency' => 60],
            ['method' => 'POST', 'path' => '/api/simulator/calculate', 'base_latency' => 120]
        ];

        $now = \Carbon\Carbon::now();

        for ($i = 0; $i < 200; $i++) {
            $ep = $endpoints[array_rand($endpoints)];
            
            // Randomize latency a bit
            $latency = $ep['base_latency'] + rand(-20, $ep['base_latency'] * 0.5);
            
            // 5% chance of an error (500), otherwise 200
            $status = rand(1, 100) <= 5 ? 500 : 200;
            
            // Random time in the past 24 hours
            $createdAt = $now->copy()->subMinutes(rand(1, 1440));

            \App\Models\ApiLog::create([
                'method' => $ep['method'],
                'endpoint' => $ep['path'],
                'status_code' => $status,
                'latency_ms' => $latency,
                'user_id' => rand(1, 10),
                'created_at' => $createdAt,
                'updated_at' => $createdAt
            ]);
        }
    }
}
