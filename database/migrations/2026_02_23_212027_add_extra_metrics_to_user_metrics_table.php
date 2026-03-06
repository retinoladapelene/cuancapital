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
        Schema::table('user_metrics', function (Blueprint $table) {
            $table->integer('blueprints_saved')->default(0)->after('perfect_runs');
            $table->integer('roadmap_items_completed')->default(0)->after('blueprints_saved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_metrics', function (Blueprint $table) {
            $table->dropColumn(['blueprints_saved', 'roadmap_items_completed']);
        });
    }
};
