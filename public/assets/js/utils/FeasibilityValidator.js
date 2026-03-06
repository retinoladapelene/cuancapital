
/**
 * Feasibility Validator Module
 * 
 * DESIGN PRINCIPLE:
 * This module is a PURE FUNCTION (Stateless).
 * It does NOT modify inputs. It only validates them.
 */

export const ValidationStatus = {
    VALID: "valid",
    WARNING: "warning",
    INVALID: "invalid"
};

/**
 * Main Validation Entry Point
 * @param {Object} inputs - { price, cost, margin, cpc, conversion }
 * @param {Object} context - { channel: 'ads'|'organic'|'hybrid' }
 * @returns {Object} { status, reason, discrepancies[] }
 */
export function validate(inputs, context = {}) {
    // 1. Check FATAL Blockers (Must Stop Simulation)
    const fatal = checkImpossible(inputs);
    if (fatal.length > 0) {
        return {
            status: ValidationStatus.INVALID,
            reason: fatal[0], // Primary reason
            discrepancies: fatal
        };
    }

    // 2. Check RISKS (Soft Warnings)
    const risks = checkRisks(inputs, context);
    if (risks.length > 0) {
        return {
            status: ValidationStatus.WARNING,
            reason: risks[0],
            discrepancies: risks
        };
    }

    // 3. All Good
    return {
        status: ValidationStatus.VALID,
        reason: null,
        discrepancies: []
    };
}

/**
 * Checks for physically impossible business models.
 * @param {Object} inputs 
 * @returns {Array} List of error messages
 */
function checkImpossible({ price, cost, margin, cpc, conversion }) {
    const errors = [];

    // Safety: Ensure numbers and handle NaN
    const safePrice = Number(price) || 0;
    const safeCost = Number(cost) || 0;
    const safeMargin = Number(margin) || 0;
    const safeCpc = Number(cpc) || 0;
    const safeConv = Number(conversion) || 0;

    // 0. Input Health Check
    if (isNaN(safePrice) || isNaN(safeMargin)) errors.push("Invalid non-numeric input detected.");

    // 1. Critical Conversion Check (Must be done first)
    if (safeConv <= 0) {
        errors.push("Conversion Rate must be greater than 0%");
        return errors; // Stop here, otherwise CPA calc is infinite
    }

    // 2. Strict Margin Check (< 1% is effectively 0)
    if (safeMargin < 0.01) {
        errors.push("Margin too low (< 1%). Business model is not viable.");
    }

    // Profit Amount
    const profitPerUnit = safePrice * safeMargin;

    // 3. CPA vs Profit (The Killer)
    // CPA = CPC / ConversionRate
    const cpa = (safeCpc / safeConv);

    // Allow small buffer? No.
    // If CPA > Profit = Burn money.
    if (cpa >= profitPerUnit) {
        const cpaFmt = Math.round(cpa).toLocaleString('id-ID');
        const profitFmt = Math.round(profitPerUnit).toLocaleString('id-ID');
        errors.push(`Biaya Iklan (CPA Rp ${cpaFmt}) > Profit/Unit (Rp ${profitFmt}). Rugi setiap transaksi.`);
    }

    return errors;
}

/**
 * Checks for "possible but dangerous" models.
 * @param {Object} inputs
 * @returns {Array} List of warning messages
 */
function checkRisks({ price, margin, cpc, conversion }, context = {}) {
    const warnings = [];
    const channel = context.channel || 'ads'; // Default comparison context

    const safeMargin = Number(margin) || 0;
    const safeConv = Number(conversion) || 0;
    const safeCpc = Number(cpc) || 0;
    const safePrice = Number(price) || 0;


    // 1. Margin too thin
    if (safeMargin < 0.10) {
        warnings.push("Margin sangat tipis (<10%). Butuh volume sangat tinggi.");
    }

    // 2. Conversion Unrealistic
    // 2. Conversion Unrealistic Context
    const maxConv = (channel === 'organic') ? 0.05 : 0.15; // Organic usually lower closing rate if unoptimized
    // Or actually organic (hot leads) can be higher?
    // Reviewer said: cold=3%, email=15%, retargeting=25%.
    // If inputs are generic, safe limit 15% is mostly ok, but strict on cold traffic.
    // Let's stick to reviewer's hint: Generic 15% is too high for cold.

    const limit = (channel === 'organic') ? 0.20 : 0.05; // Ads cold traffic limit ~5%?
    // Let's use a safe balanced limit.
    // If > 20% on any channel is sus.

    if (safeConv > 0.20) {
        warnings.push("Conversion Rate > 20% sangat jarang terjadi (kecuali hot market).");
    } else if (channel === 'ads' && safeConv > 0.05) {
        // warnings.push("Conversion Ads > 5% butuh funnel sangat matang.");
        // keep it simple for now as requested
    }

    // Reviewer request: "if (safeConv > 0.15)" generic is bad.
    // Fix:
    if (safeConv > 0.15) {
        warnings.push("Conversion Rate > 15% sangat tinggi. Pastikan data valid.");
    }

    // 3. CPC > Price (Weird but technically possible if LTV is high? But here we do single sale sim)
    if (safeCpc > safePrice) {
        warnings.push("CPC lebih mahal dari harga produk. Sangat berisiko.");
    }

    return warnings;
}
