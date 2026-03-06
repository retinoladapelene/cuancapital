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
        Schema::table('roadmap_steps', function (Blueprint $table) {
            $table->string('priority')->nullable()->after('strategy_tag');
            $table->integer('priority_score')->nullable()->after('priority');
            $table->integer('estimated_weeks')->nullable()->after('priority_score');
            $table->json('expected_impact')->nullable()->after('estimated_weeks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roadmap_steps', function (Blueprint $table) {
            $table->dropColumn([
                'priority',
                'priority_score',
                'estimated_weeks',
                'expected_impact'
            ]);
        });
    }
};
