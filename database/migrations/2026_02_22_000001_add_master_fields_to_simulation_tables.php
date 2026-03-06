<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── simulation_steps: step_type + is_irreversible ─────────────────
        Schema::table('simulation_steps', function (Blueprint $table) {
            // 'decision' = normal choice | 'event' = pressure event (no choice needed) | 'briefing' = context only
            $table->string('step_type')->default('decision')->after('order');
            $table->boolean('is_irreversible')->default(false)->after('step_type');
        });

        // ── simulations: scoring_formula + initial_state_json ─────────────
        Schema::table('simulations', function (Blueprint $table) {
            // Formula string evaluated on frontend/engine e.g. "profitability*0.25+growth_rate*0.20+..."
            $table->string('scoring_formula', 512)->nullable()->after('xp_reward');
            // Starting metrics e.g. {"profitability":50,"growth_rate":50,"brand_equity":50,"system_strength":50,"investor_confidence":50}
            $table->json('initial_state_json')->nullable()->after('scoring_formula');
        });
    }

    public function down(): void
    {
        Schema::table('simulation_steps', function (Blueprint $table) {
            $table->dropColumn(['step_type', 'is_irreversible']);
        });

        Schema::table('simulations', function (Blueprint $table) {
            $table->dropColumn(['scoring_formula', 'initial_state_json']);
        });
    }
};
