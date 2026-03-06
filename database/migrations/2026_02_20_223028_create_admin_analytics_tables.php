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
        // 1. admin_daily_metrics
        Schema::create('admin_daily_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->integer('total_users')->default(0);
            $table->integer('new_users')->default(0);
            $table->integer('active_users')->default(0);
            $table->integer('total_blueprints')->default(0);
            $table->integer('new_blueprints')->default(0);
            $table->integer('total_roadmaps')->default(0);
            $table->integer('new_roadmaps')->default(0);
            
            // Average Metrics
            $table->float('avg_feasibility')->default(0);
            $table->float('avg_profitability')->default(0);
            $table->float('avg_risk')->default(0);
            $table->float('avg_efficiency')->default(0);
            $table->float('avg_probability_score')->default(0);
            
            // Financial & Conversion
            $table->bigInteger('avg_target_profit')->default(0);
            $table->float('roadmap_conversion_rate')->default(0);
            $table->float('rgp_to_simulator_rate')->default(0);
            $table->float('simulator_to_mentor_rate')->default(0);
            $table->float('mentor_to_roadmap_rate')->default(0);
            
            // High Level Tags
            $table->float('boncos_percentage')->default(0);
            $table->integer('high_intent_users')->default(0);
            
            $table->timestamps();
        });

        // 2. admin_funnel_snapshot
        Schema::create('admin_funnel_snapshot', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->integer('total_visitors')->default(0); // If tracked, else 0
            $table->integer('rgp_users')->default(0);
            $table->integer('simulator_users')->default(0);
            $table->integer('mentor_users')->default(0);
            $table->integer('roadmap_users')->default(0);
            $table->timestamps();
        });

        // 3. admin_ai_summary
        Schema::create('admin_ai_summary', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->text('summary_text');
            $table->timestamps();
        });

        // 4. api_logs (for system health indexing)
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint')->index();
            $table->string('method');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->integer('status_code')->index();
            $table->float('response_time_ms'); // In milliseconds
            $table->timestamps(); // created_at is automatically indexed here if we want, but let's add explicitly
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
        Schema::dropIfExists('admin_ai_summary');
        Schema::dropIfExists('admin_funnel_snapshot');
        Schema::dropIfExists('admin_daily_metrics');
        Schema::dropIfExists('admin_analytics_tables'); // The stub name
    }
};
