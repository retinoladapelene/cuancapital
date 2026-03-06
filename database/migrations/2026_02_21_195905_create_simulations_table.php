<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('simulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('courses')->onDelete('cascade');
            $table->string('title');
            $table->text('intro_text');
            $table->string('difficulty_level'); // beginner, intermediate, advanced, expert, master
            $table->integer('xp_reward')->default(50);
            $table->timestamps();
        });

        Schema::create('simulation_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_id')->constrained('simulations')->onDelete('cascade');
            $table->text('question');
            $table->integer('order')->default(1);
            $table->timestamps();
        });

        Schema::create('simulation_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('step_id')->constrained('simulation_steps')->onDelete('cascade');
            $table->text('label');
            $table->json('effect_json'); // {"profit": -10, "traffic": 30, "brand": 5}
            $table->text('feedback_text');
            $table->timestamps();
        });

        Schema::create('user_simulation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('simulation_id')->constrained('simulations')->onDelete('cascade');
            $table->integer('attempts')->default(1);
            $table->integer('score')->default(0);
            $table->json('result_json'); // {"profit": 15, "traffic": 20, "brand": 30}
            $table->string('rating')->nullable(); // Failed, Beginner, Good, Expert, Perfect Strategist
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['user_id', 'simulation_id']); // One cumulative result per user per simulation (tracking best/recent)
        });

        Schema::create('simulation_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('simulation_id')->constrained('simulations')->onDelete('cascade');
            $table->foreignId('current_step_id')->nullable()->constrained('simulation_steps')->nullOnDelete();
            $table->json('state_json'); // {"profit": 0, "traffic": 0, "brand": 0}
            $table->timestamp('started_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'simulation_id']); // Only one active session at a time per user per sim
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulation_sessions');
        Schema::dropIfExists('user_simulation_results');
        Schema::dropIfExists('simulation_options');
        Schema::dropIfExists('simulation_steps');
        Schema::dropIfExists('simulations');
    }
};
