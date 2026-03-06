/**
 * k6 Load Test: Polling Flood Simulation
 * ───────────────────────────────────────────────────────────────────────────
 * Simulates the most dangerous scenario:
 *   300 users, each polling job status every 3 seconds for 2 minutes.
 *   = ~100 req/sec to GET /jobs/{id}/status
 *
 * With DB-only: MySQL chokes at this load.
 * With Redis cache (TTL 3s): ~97% requests are cache hits — DB barely touched.
 *
 * Usage:
 *   k6 run \
 *     -e BASE_URL=http://localhost:8000 \
 *     -e TOKEN=<your_sanctum_token> \
 *     -e JOB_ID=<a_valid_job_uuid> \
 *     tests/load/k6-polling-flood.js
 *
 * Thresholds (PASS/FAIL):
 *   p(95) of polling < 100ms
 *   error rate < 1%
 * ───────────────────────────────────────────────────────────────────────────
 */

import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate, Trend } from 'k6/metrics';

// ── Custom Metrics ────────────────────────────────────────────────────────
const errorRate = new Rate('error_rate');
const pollLatency = new Trend('poll_latency_ms', true);

// ── Test Config ───────────────────────────────────────────────────────────
export const options = {
    scenarios: {
        polling_flood: {
            executor: 'constant-vus',
            vus: 300,        // 300 concurrent pollers
            duration: '2m',
            gracefulStop: '10s',
        },
    },
    thresholds: {
        // p95 of all polling requests must be under 100ms
        'poll_latency_ms': ['p(95)<100'],
        // Error rate must stay under 1%
        'error_rate': ['rate<0.01'],
        // Also validate built-in http duration
        'http_req_duration': ['p(95)<200'],
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

export default function () {
    // ── Poll job status ────────────────────────────────────────────────────
    const start = Date.now();
    const res = http.get(`${BASE_URL}/api/jobs/${JOB_ID}/status`, { headers: HEADERS });
    const ms = Date.now() - start;

    pollLatency.add(ms);

    const ok = check(res, {
        'status is 200 or 404': (r) => r.status === 200 || r.status === 404,
        'response has status field': (r) => {
            try {
                const body = JSON.parse(r.body);
                return body.data && body.data.status !== undefined;
            } catch { return false; }
        },
        'response under 100ms': () => ms < 100,
    });

    errorRate.add(!ok);

    // Simulate realistic polling interval: 3 seconds between polls
    sleep(3);
}

export function handleSummary(data) {
    const p95 = data.metrics.poll_latency_ms?.values?.['p(95)'] ?? 'N/A';
    const p99 = data.metrics.poll_latency_ms?.values?.['p(99)'] ?? 'N/A';
    const avg = data.metrics.poll_latency_ms?.values?.avg ?? 'N/A';
    const errPc = ((data.metrics.error_rate?.values?.rate ?? 0) * 100).toFixed(2);
    const reqs = data.metrics.http_reqs?.values?.count ?? 0;

    console.log('\n═══════════════════════════════════════════════════');
    console.log('  POLLING FLOOD TEST RESULTS');
    console.log('═══════════════════════════════════════════════════');
    console.log(`  Total requests : ${reqs}`);
    console.log(`  Avg latency    : ${typeof avg === 'number' ? avg.toFixed(1) : avg}ms`);
    console.log(`  p95 latency    : ${typeof p95 === 'number' ? p95.toFixed(1) : p95}ms  ${p95 < 100 ? '✅' : '❌ RED FLAG'}`);
    console.log(`  p99 latency    : ${typeof p99 === 'number' ? p99.toFixed(1) : p99}ms`);
    console.log(`  Error rate     : ${errPc}%  ${errPc < 1 ? '✅' : '❌ RED FLAG'}`);
    console.log('═══════════════════════════════════════════════════\n');

    return { stdout: '' };
}
