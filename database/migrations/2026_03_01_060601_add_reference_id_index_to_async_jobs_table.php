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
        Schema::table('async_jobs', function (Blueprint $table) {
             try {
                 $table->index('reference_id');
             } catch (\Exception $e) {
                 // Index might already exist, safe to ignore
             }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('async_jobs', function (Blueprint $table) {
             try {
                $table->dropIndex(['reference_id']);
             } catch (\Exception $e) {
                // Ignore
             }
        });
    }
};
