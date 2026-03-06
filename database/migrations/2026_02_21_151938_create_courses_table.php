<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('level')->default('beginner');     // beginner | intermediate | advanced
            $table->string('category')->nullable();
            $table->string('thumbnail')->nullable();           // [Strategic Enhancement] UI engagement
            $table->unsignedInteger('xp_reward')->default(0); // Course-completion bonus XP
            $table->unsignedInteger('lessons_count')->default(0); // [Improvement 1] cached count
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
