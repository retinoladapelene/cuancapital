/**
 * k6 Load Test: Stepwise Concurrency Ramp
 * ───────────────────────────────────────────────────────────────────────────
 * Gradually increases virtual users to find the "knee point" —
 * the exact concurrency level where latency starts rising sharply.
 *
 * Ramp: 50 → 100 → 300 → 500 → 1000 VU
 * Each stage holds for 90 seconds to get stable readings.
 *
 * Tests a realistic mix of endpoints:
 *   60% — GET /me (lightweight, auth check)
 *   20% — GET /jobs/{id}/status (polling, should be cache-served)
 *   10% — POST /mentor/evaluate (heavy async trigger)
 *   10% — GET /mentor/evaluation/latest (DB read)
 *
 * Usage:
 *   k6 run \
 *     -e BASE_URL=http://localhost:8000 \
 *     -e TOKEN=<your_sanctum_token> \
 *     -e JOB_ID=<a_valid_job_uuid> \
 *     tests/load/k6-stepwise.js
 *
 * Watch for: latency knee between stages.
 * If p95 jumps from ~80ms to ~800ms between stages → that VU count is your limit.
 * ───────────────────────────────────────────────────────────────────────────
 */

import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate, Trend } from 'k6/metrics';

const errorRate = new Rate('error_rate');
const lightLatency = new Trend('light_req_ms', true);
const pollingLatency = new Trend('polling_ms', true);
const heavyLatency = new Trend('heavy_ms', true);

export const options = {
    stages: [
        // Stage 1: Warm up
        { duration: '30s', target: 50 },
        { duration: '90s', target: 50 },  // Hold 50 VU

        // Stage 2
        { duration: '30s', target: 100 },
        { duration: '90s', target: 100 },  // Hold 100 VU

        // Stage 3: Real load
        { duration: '30s', target: 300 },
        { duration: '90s', target: 300 },  // Hold 300 VU

        // Stage 4: Stress
        { duration: '30s', target: 500 },
        { duration: '90s', target: 500 },  // Hold 500 VU

        // Stage 5: Push limit (optional — watch for red flags first)
        { duration: '30s', target: 1000 },
        { duration: '60s', target: 1000 },  // Hold 1000 VU

        // Cool down
        { duration: '30s', target: 0 },
    ],
    thresholds: {
        // Must survive 300 VU with these targets
        'light_req_ms': ['p(95)<200'],
        'polling_ms': ['p(95)<150'],
        'heavy_ms': ['p(95)<600'],
        'error_rate': ['rate<0.02'],  // Allow 2% at extreme load
        'http_req_duration': ['p(99)<2000'],  // Hard limit: nothing > 2s at p99
    },
};

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';
const TOKEN = __ENV.TOKEN;
const JOB_ID = __ENV.JOB_ID;

const HEADERS = {
    'Authorization': `Bearer ${TOKEN}`,
    'Accept': 'application/json',
    'Content-Type': 'application/json',
};

const EVALUATE_PAYLOAD = JSON.stringify({
    traffic: 1000, conversion: 2.5, price: 500000,
    cost: 150000, fixed_cost: 3000000, target_revenue: 50000000,
    strategy: 'traffic_scaling', business_type: 'digital',
});

export default function () {
    // Weighted random endpoint selection
    const roll = Math.random();

    if (roll < 0.60) {
        // 60%: Light read — GET /me
        const start = Date.now();
        const res = http.get(`${BASE_URL}/api/me`, { headers: HEADERS });
        lightLatency.add(Date.now() - start);

        const ok = check(res, { '/me: 200': (r) => r.status === 200 });
        errorRate.add(!ok);

    } else if (roll < 0.80) {
        // 20%: Polling (should be Redis cache hit)
        const jobId = JOB_ID || 'test-job-id';
        const start = Date.now();
        const res = http.get(`${BASE_URL}/api/jobs/${jobId}/status`, { headers: HEADERS });
        pollingLatency.add(Date.now() - start);

        const ok = check(res, { 'poll: 200 or 404': (r) => r.status === 200 || r.status === 404 });
        errorRate.add(!ok);

    } else if (roll < 0.90) {
        // 10%: Heavy trigger
        const start = Date.now();
        const res = http.post(`${BASE_URL}/api/mentor/evaluate`, EVALUATE_PAYLOAD, { headers: HEADERS });
        heavyLatency.add(Date.now() - start);

        const ok = check(res, { 'evaluate: 202 or 429': (r) => r.status === 202 || r.status === 429 });
        errorRate.add(!ok);

    } else {
        // 10%: Latest evaluation read
        const start = Date.now();
        const res = http.get(`${BASE_URL}/api/mentor/evaluation/latest`, { headers: HEADERS });
        lightLatency.add(Date.now() - start);

        const ok = check(res, { 'latest eval: 200 or 404': (r) => r.status === 200 || r.status === 404 });
        errorRate.add(!ok);
    }

    // Real user think time: 0.5–2 seconds
    sleep(Math.random() * 1.5 + 0.5);
}

export function handleSummary(data) {
    const fmt = (v) => typeof v === 'number' ? v.toFixed(1) + 'ms' : 'N/A';
    const flag = (v, threshold) => v !== 'N/A' && parseFloat(v) < threshold ? '✅' : `❌ (limit: ${threshold}ms)`;

    const lP95 = data.metrics.light_req_ms?.values?.['p(95)'];
    const poP95 = data.metrics.polling_ms?.values?.['p(95)'];
    const hP95 = data.metrics.heavy_ms?.values?.['p(95)'];
    const errPc = ((data.metrics.error_rate?.values?.rate ?? 0) * 100).toFixed(2);
    const reqs = data.metrics.http_reqs?.values?.count ?? 0;
    const rps = data.metrics.http_reqs?.values?.rate ?? 0;

    console.log('\n═══════════════════════════════════════════════════');
    console.log('  STEPWISE RAMP TEST RESULTS');
    console.log('  (50 → 100 → 300 → 500 → 1000 VU)');
    console.log('═══════════════════════════════════════════════════');
    console.log(`  Total requests   : ${reqs} (~${rps.toFixed(0)} req/s avg)`);
    console.log(`  Light req p95    : ${fmt(lP95)}  ${flag(lP95, 200)}`);
    console.log(`  Polling p95      : ${fmt(poP95)}  ${flag(poP95, 150)}`);
    console.log(`  Heavy req p95    : ${fmt(hP95)}  ${flag(hP95, 600)}`);
    console.log(`  Error rate       : ${errPc}%  ${parseFloat(errPc) < 2 ? '✅' : '❌ RED FLAG'}`);
    console.log('───────────────────────────────────────────────────');
    console.log('  ⚡ Look for the VU count where p95 "jumps".');
    console.log('     That is your knee point / system ceiling.');
    console.log('═══════════════════════════════════════════════════\n');

    return { stdout: '' };
}
