
import { validate } from '../public/assets/js/utils/FeasibilityValidator.js';

console.log("=== Running Feasibility Validator Tests ===\n");

const tests = [
    {
        name: "Impossible: Strict Margin (< 1%)",
        input: { price: 100000, margin: 0.005, conversion: 0.02, cpc: 100, cost: 99500 },
        context: { channel: 'ads' },
        expectedStatus: 'invalid', // Should fail strict margin
        expectedReason: 'Margin too low'
    },
    {
        name: "Impossible: Zero Conversion",
        input: { price: 100000, margin: 0.2, conversion: 0, cpc: 500 },
        context: { channel: 'ads' },
        expectedStatus: 'invalid',
        expectedReason: 'Conversion Rate must be greater than 0'
    },
    {
        name: "Impossible: CPA > Profit",
        input: { price: 100000, margin: 0.2, conversion: 0.01, cpc: 2500 },
        // Profit = 20k. CPA = 2500 / 0.01 = 250,000. Loss.
        context: { channel: 'ads' },
        expectedStatus: 'invalid',
        expectedReason: 'Biaya Iklan'
    },
    {
        name: "Risky: Thin Margin (< 10%)",
        input: { price: 100000, margin: 0.05, conversion: 0.02, cpc: 100 },
        context: { channel: 'ads' },
        expectedStatus: 'warning',
        expectedReason: 'Margin sangat tipis'
    },
    {
        name: "Risky: High Ads Conversion (> 5% for Cold Ads?? Wait, logic check)",
        // In checkRisks: if (channel === 'ads' && safeConv > 0.05) -> I commented this out in code.
        // Let's check generally huge conversion > 15%
        input: { price: 100000, margin: 0.3, conversion: 0.16, cpc: 100 },
        context: { channel: 'ads' },
        expectedStatus: 'warning',
        expectedReason: 'Conversion Rate > 15%'
    },
    {
        name: "Valid: Standard Dropship",
        input: { price: 150000, margin: 0.2, conversion: 0.015, cpc: 1500 },
        // Profit = 30k. CPA = 1500/0.015 = 100k. Wait. 
        // CPA 100k > Profit 30k. This should be INVALID.
        // Let's check common benchmarks.
        // Dropship Margin 20% = 30k.
        // To be profitable, CPA < 30k.
        // CPC 1500 -> Max Conv needed = 1500 / 30000 = 0.05 (5%).
        // With 1.5% conv, CPA is 100k. Loss.
        // Setting input to be PROFITABLE.
        // cpc 300. CPA = 300 / 0.015 = 20k. < 30k. Profitable.
        input: { price: 150000, margin: 0.2, conversion: 0.015, cpc: 300 },
        context: { channel: 'ads' },
        expectedStatus: 'valid'
    }
];

tests.forEach(test => {
    const result = validate(test.input, test.context);
    const passed = result.status === test.expectedStatus;

    // Optional: Check reason contains expected keyword
    const reasonMatch = !test.expectedReason || (result.reason && result.reason.includes(test.expectedReason));

    if (passed && reasonMatch) {
        console.log(`✅ PASS: ${test.name}`);
    } else {
        console.error(`❌ FAIL: ${test.name}`);
        console.error(`   Expected: ${test.expectedStatus} containing "${test.expectedReason}"`);
        console.error(`   Got:      ${result.status} - ${result.reason}`);
    }
});
