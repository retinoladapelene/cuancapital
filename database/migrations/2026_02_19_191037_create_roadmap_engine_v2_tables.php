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
        // 1. Extend business_profiles with Context Data
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->string('stage')->nullable(); // startup, growth, scaling
            $table->string('primary_goal')->nullable(); // profit, growth, validation, scale
            $table->string('experience_level')->nullable(); // beginner, intermediate, expert
            $table->string('risk_tolerance')->nullable(); // low, medium, high
            $table->string('team_size')->nullable(); // solo, small, medium, large
            $table->string('time_availability')->nullable(); // full_time, part_time
        });

        // 2. Create the Master Library for Roadmap Steps
        // Note: 'roadmap_steps' is already used for user instances in a previous migration.
        // We will call this 'roadmap_library_steps' to distinguish the master data.
        Schema::create('roadmap_library_steps', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            
            // Classification
            $table->string('category'); // traffic, conversion, retention, finance, product
            $table->string('stage')->nullable(); // ALL, startup, growth, scaling
            $table->string('channel')->nullable(); // ALL, ads, organic, marketplace
            
            // Scoring & Logic
            $table->integer('difficulty')->default(5); // 1-10
            $table->integer('impact')->default(5); // 1-10
            $table->integer('priority_weight')->default(1); // Manual override, default 1
            
            // Logic Fields (JSON)
            $table->json('conditions')->nullable(); // { "requires_traffic": 1000, "max_cost": 500 }
            $table->string('outcome_type')->nullable(); // traffic_boost, margin_fix, etc.
            
            // Resources
            $table->decimal('required_budget', 15, 2)->default(0);
            $table->string('required_time')->nullable(); // "2 days", "1 week"
            $table->json('tags')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roadmap_library_steps');

        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'stage', 
                'primary_goal', 
                'experience_level', 
                'risk_tolerance', 
                'team_size', 
                'time_availability'
            ]);
        });
    }
};
