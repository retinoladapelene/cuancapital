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
        Schema::create('async_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Scope security
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Job metadata
            $table->string('type'); // e.g., 'strategic_evaluation', 'roadmap_generation'
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            
            // Minimalist Inputs & References
            $table->json('input_parameters')->nullable(); 
            $table->string('reference_type')->nullable(); // e.g., 'MentorSession'
            $table->unsignedBigInteger('reference_id')->nullable(); // Target ID
            
            // Error handling
            $table->text('error_message')->nullable();
            
            $table->timestamps();

            // Critical polling & maintenance indexes
            $table->index(['user_id', 'id']); // Fast secured polling
            $table->index('status'); // Fast worker/maintenance queries
            $table->index('created_at'); // Fast pruning
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('async_jobs');
    }
};
