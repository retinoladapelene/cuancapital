<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Course;
use App\Models\Simulation;
use App\Models\SimulationStep;
use App\Models\SimulationOption;
use App\Services\SimulationFactory;

class SimulationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Simulation::truncate();
        SimulationStep::truncate();
        SimulationOption::truncate();
        Schema::enableForeignKeyConstraints();

        $courses = Course::all();

        foreach ($courses as $course) {
            $data = SimulationFactory::getSimulationSet($course->level ?? 'beginner');

            $sim = Simulation::create([
                'module_id' => $course->id,
                'title' => $data['title'],
                'intro_text' => $data['intro_text'],
                'difficulty_level' => $data['difficulty_level'],
                'xp_reward' => $data['xp_reward'],
            ]);

            foreach ($data['steps'] as $index => $stepData) {
                $step = SimulationStep::create([
                    'simulation_id' => $sim->id,
                    'question' => $stepData['question'],
                    'order' => $index + 1,
                ]);

                foreach ($stepData['options'] as $opt) {
                    SimulationOption::create([
                        'step_id' => $step->id,
                        'label' => $opt['label'],
                        'effect_json' => $opt['effect'],
                        'feedback_text' => $opt['feedback'],
                    ]);
                }
            }
        }
    }
}
