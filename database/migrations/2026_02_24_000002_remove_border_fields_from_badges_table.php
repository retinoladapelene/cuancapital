<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove border-related columns from badges table.
     * Badges are ONLY about name + icon + rarity — borders are handled by border_frames.
     */
    public function up(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->dropColumn(['css_class', 'border_svg']);
        });
    }

    public function down(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->string('css_class')->nullable();
            $table->text('border_svg')->nullable();
        });
    }
};
