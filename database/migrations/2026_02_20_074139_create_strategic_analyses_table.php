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
        Schema::create('strategic_analyses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->index(); // Nullable for guest analysis
            $table->string('session_id')->nullable()->index(); // For tracking guest flows
            
            // Context
            $table->string('business_type');
            $table->string('risk_intent');
            
            // Optimized Analytics Columns (Indexed for speed)
            $table->integer('feasibility_score')->index();
            $table->integer('risk_score')->index();
            $table->integer('profit_score')->index();
            $table->integer('efficiency_score')->index();

            // Result
            $table->string('strategy_label')->index();
            $table->string('strategy_version')->default('1.0'); // For long-term consistency

            // Full Data Snapshots (For deep debugging/replay)
            $table->json('input_snapshot');
            $table->json('metrics_snapshot');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strategic_analyses');
    }
};
