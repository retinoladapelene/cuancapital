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
        Schema::table('blueprints', function (Blueprint $table) {
            // Add a standard index first to support the Foreign Key during unique index drop
            // Check if index already exists to avoid duplication error (though explicit add is safer if we know it's missing)
            // But if we just add it, Laravel handles index naming.
            $table->index('user_id');
            $table->dropUnique('blueprints_user_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blueprints', function (Blueprint $table) {
            $table->unique('user_id');
            $table->dropIndex(['user_id']); 
        });
    }
};
