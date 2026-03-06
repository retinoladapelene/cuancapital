<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('simulations', function (Blueprint $table) {
            // Tags generated during roadmap creation (e.g. ['Margin Improvement', 'Traffic Growth'])
            $table->json('generated_tags')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('simulations', function (Blueprint $table) {
            $table->dropColumn('generated_tags');
        });
    }
};
