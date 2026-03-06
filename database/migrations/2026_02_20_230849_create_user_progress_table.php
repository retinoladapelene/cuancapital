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
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('experience_version', 10)->default('v1');
            $table->integer('xp_points')->default(0);
            
            // Boolean Checks (Idempotency Guards)
            $table->boolean('is_rgp_completed')->default(false);
            $table->boolean('is_simulator_used')->default(false);
            $table->boolean('is_mentor_completed')->default(false);
            $table->boolean('is_roadmap_generated')->default(false);
            
            // Timestamp Milestones
            $table->timestamp('rgp_completed_at')->nullable();
            $table->timestamp('simulator_used_at')->nullable();
            $table->timestamp('mentor_completed_at')->nullable();
            $table->timestamp('roadmap_generated_at')->nullable();
            
            // Percentage tracking
            $table->integer('roadmap_completion_percent')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
