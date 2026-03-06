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
            $table->string('type', 50)->nullable()->after('session_id');
            $table->string('title', 255)->nullable()->after('type');
            $table->string('persona', 50)->nullable()->after('title');
            $table->json('data')->nullable()->after('persona');
            $table->integer('version')->default(1)->after('data');
            $table->string('status', 30)->default('completed')->after('version');
            $table->unsignedBigInteger('linked_source_id')->nullable()->after('status');
            $table->string('linked_source_type', 50)->nullable()->after('linked_source_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blueprints', function (Blueprint $table) {
            $table->dropColumn([
                'type', 'title', 'persona', 'data', 'version', 
                'status', 'linked_source_id', 'linked_source_type'
            ]);
        });
    }
};
