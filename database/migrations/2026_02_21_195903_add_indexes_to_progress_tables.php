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
        Schema::table('user_course_progress', function (Blueprint $table) {
            $table->index(['user_id', 'course_id'], 'user_id_course_id_idx');
        });

        Schema::table('user_lesson_progress', function (Blueprint $table) {
            $table->index(['user_id', 'lesson_id'], 'user_id_lesson_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_course_progress', function (Blueprint $table) {
            $table->dropIndex('user_id_course_id_idx');
        });

        Schema::table('user_lesson_progress', function (Blueprint $table) {
            $table->dropIndex('user_id_lesson_id_idx');
        });
    }
};
