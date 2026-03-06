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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'login_method')) {
                $table->string('login_method')->default('email')->after('email');
            }
        });

        Schema::table('blueprints', function (Blueprint $table) {
            if (!Schema::hasColumn('blueprints', 'business_model')) {
                $table->string('business_model', 50)->nullable()->after('type');
            }
            if (!Schema::hasColumn('blueprints', 'target_profit')) {
                $table->bigInteger('target_profit')->nullable()->after('business_model');
            }
        });

        Schema::table('roadmaps', function (Blueprint $table) {
            if (!Schema::hasColumn('roadmaps', 'total_actions')) {
                $table->integer('total_actions')->default(0);
            }
            if (!Schema::hasColumn('roadmaps', 'completed_actions')) {
                $table->integer('completed_actions')->default(0)->after('total_actions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roadmaps', function (Blueprint $table) {
            if (Schema::hasColumn('roadmaps', 'total_actions')) {
                $table->dropColumn('total_actions');
            }
            if (Schema::hasColumn('roadmaps', 'completed_actions')) {
                $table->dropColumn('completed_actions');
            }
        });

        Schema::table('blueprints', function (Blueprint $table) {
            if (Schema::hasColumn('blueprints', 'business_model')) {
                $table->dropColumn('business_model');
            }
            if (Schema::hasColumn('blueprints', 'target_profit')) {
                $table->dropColumn('target_profit');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'login_method')) {
                $table->dropColumn('login_method');
            }
        });
    }
};
