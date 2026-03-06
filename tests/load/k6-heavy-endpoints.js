/**
 * k6 Load Test: Heavy Endpoint Stress Test
 * ───────────────────────────────────────────────────────────────────────────
 * Tests the 3 heavy async-trigger endpoints under concurrent load.
 * These endpoints should be fast (< 500ms) because they just:
 *   1. Create an AsyncJob record in DB
 *   2. Dispatch a queue job
 *   3. Return 202 Accepted
 *
 * The actual heavy computation runs inside queue workers.
 *
 * Endpoints tested:
 *   POST /api/mentor/evaluate    (strategic evaluation trigger)
 *   POST /api/mentor/calculate   (financial calculation trigger)
 *   POST /api/mentor/roadmap/generate  (roadmap generation trigger)
 *
 * Usage:
 *   k6 run \
 *     -e BASE_URL=http://localhost:8000 \
 *     -e TOKEN=<your_sanctum_token> \
 *     tests/load/k6-heavy-endpoints.js
 *
 * Thresholds:
 *   p(95) trigger response < 500ms
 *   error rate < 1%
 * ───────────────────────────────────────────────────────────────────────────
 */

import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate, Trend } from 'k6/metrics';

const errorRate = new Rate('error_rate');
const evaluateTime = new Trend('evaluate_ms', true);
const calculateTime = new Trend('calculate_ms', true);
const roadmapTime = new Trend('roadmap_ms', true);

export const options = {
    stages: [
        { duration: '30s', target: 30 },  // Ramp up to 30 users
        { duration: '60s', target: 100 },  // Sustain at 100 users
        { duration: '30s', target: 0 },  // Ramp down
    ],
    thresholds: {
        'evaluate_ms': ['p(95)<500'],
        'calculate_ms': ['p(95)<500'],
        'roadmap_ms': ['p(95)<500'],
        'error_rate': ['rate<0.01'],
        'http_req_duration': ['p(95)<600'],
    },
};

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';
const TOKEN = __ENV.TOKEN;

const HEADERS = {
    'Authorization': `Bearer ${TOKEN}`,
    'Accept': 'application/json',
    'Content-Type': 'application/json',
};

// ── Realistic test payloads ───────────────────────────────────────────────
const EVALUATE_PAYLOAD = JSON.stringify({
    traffic: 1000,
    conversion: 2.5,
    price: 500000,
    cost: 150000,
    fixed_cost: 3000000,
    target_revenue: 50000000,
    strategy: 'traffic_scaling',
    business_type: 'digital',
});

const CALCULATE_PAYLOAD = JSON.stringify({
    traffic: 800,
    conversion: 3.0,
    price: 350000,
    cost: 100000,
    fixed_cost: 2000000,
    target_revenue: 30000000,
});

export default function () {
    const vuId = __VU;

    // ── Round-robin across 3 endpoints to distribute load ────────────────
    const round = vuId % 3;

    if (round === 0) {
        // Strategic Evaluate
        const start = Date.now();
        const res = http.post(`${BASE_URL}/api/mentor/evaluate`, EVALUATE_PAYLOAD, { headers: HEADERS });
        evaluateTime.add(Date.now() - start);

        const ok = check(res, {
            'evaluate: 202 accepted': (r) => r.status === 202,
            'evaluate: has job_id': (r) => {
                try { return !!JSON.parse(r.body).data.job_id; } catch { return false; }
            },
        });
        errorRate.add(!ok);

    } else if (round === 1) {
        // Mentor Calculate
        const start = Date.now();
        const res = http.post(`${BASE_URL}/api/mentor/calculate`, CALCULATE_PAYLOAD, { headers: HEADERS });
        calculateTime.add(Date.now() - start);

        const ok = check(res, {
            'calculate: 202 accepted': (r) => r.status === 202,
            'calculate: has job_id': (r) => {
                try { return !!JSON.parse(r.body).data.job_id; } catch { return false; }
            },
        });
        errorRate.add(!ok);

    } else {
        // Roadmap Generate
        const start = Date.now();
        const res = http.post(`${BASE_URL}/api/mentor/roadmap/generate`, '{}', { headers: HEADERS });
        roadmapTime.add(Date.now() - start);

        const ok = check(res, {
            'roadmap: 202 or 422': (r) => r.status === 202 || r.status === 422,
            'roadmap: valid json': (r) => {
                try { JSON.parse(r.body); return true; } catch { return false; }
            },
        });
        errorRate.add(!ok);
    }

    // Realistic think time between requests
    sleep(Math.random() * 2 + 1);  // 1–3 seconds
}

export function handleSummary(data) {
    const fmt = (v) => typeof v === 'number' ? v.toFixed(1) : (v ?? 'N/A');

    const evalP95 = data.metrics.evaluate_ms?.values?.['p(95)'];
    const calcP95 = data.metrics.calculate_ms?.values?.['p(95)'];
    const rmP95 = data.metrics.roadmap_ms?.values?.['p(95)'];
    const errRate = ((data.metrics.error_rate?.values?.rate ?? 0) * 100).toFixed(2);
    const reqs = data.metrics.http_reqs?.values?.count ?? 0;

    console.log('\n═══════════════════════════════════════════════════');
    console.log('  HEAVY ENDPOINT TEST RESULTS');
    console.log('═══════════════════════════════════════════════════');
    console.log(`  Total requests   : ${reqs}`);
    console.log(`  Evaluate p95     : ${fmt(evalP95)}ms  ${evalP95 < 500 ? '✅' : '❌'}`);
    console.log(`  Calculate p95    : ${fmt(calcP95)}ms  ${calcP95 < 500 ? '✅' : '❌'}`);
    console.log(`  Roadmap p95      : ${fmt(rmP95)}ms  ${rmP95 < 500 ? '✅' : '❌'}`);
    console.log(`  Error rate       : ${errRate}%  ${parseFloat(errRate) < 1 ? '✅' : '❌ RED FLAG'}`);
    console.log('═══════════════════════════════════════════════════\n');

    return { stdout: '' };
}
