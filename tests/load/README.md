# k6 Load Tests — cuan-workflow

Load testing suite untuk Phase 3 Performance & Scalability.

## Install k6 (Windows)

```powershell
# Option 1: winget (paling mudah)
winget install k6 --source winget

# Option 2: Download installer langsung
# https://dl.k6.io/msi/k6-latest-amd64.msi

# Verify
k6 version
```

## Setup: Sanctum Token (Test User)

> ⚠️ **Buat user test khusus** — jangan pakai akun admin asli.
> Load test akan generate ribuan log dan bisa ganggu achievement/XP system.

**Cara ambil token:**

```bash
# Via API
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"testuser@example.com","password":"password"}' \
  | grep -o '"token":"[^"]*"'
```

**Cara ambil Job ID untuk polling test:**

```bash
# Trigger satu job dulu, ambil job_id dari response
curl -X POST http://localhost:8000/api/mentor/calculate \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"traffic":1000,"conversion":2.5,"price":500000,"cost":150000,"fixed_cost":3000000,"target_revenue":50000000}'
```

---

## Test Suite

### 1. Polling Flood Test (Paling Penting)

Simulasi 300 user polling tiap 3 detik — skenario mematikan tanpa Redis cache.

```powershell
k6 run `
  -e BASE_URL=http://localhost:8000 `
  -e TOKEN=<your_token> `
  -e JOB_ID=<valid_job_uuid> `
  tests/load/k6-polling-flood.js
```

**Target:** p95 < 100ms, error rate < 1%

---

### 2. Heavy Endpoint Test

Stress test `POST /mentor/evaluate`, `POST /mentor/calculate`, `POST /mentor/roadmap/generate`.

```powershell
k6 run `
  -e BASE_URL=http://localhost:8000 `
  -e TOKEN=<your_token> `
  tests/load/k6-heavy-endpoints.js
```

**Target:** Trigger response < 500ms (hanya dispatch job, bukan tunggu selesai)

---

### 3. Stepwise Ramp (Cari Ceiling)

Temukan batas maksimal sistem: 50 → 100 → 300 → 500 → 1000 VU.

```powershell
k6 run `
  -e BASE_URL=http://localhost:8000 `
  -e TOKEN=<your_token> `
  -e JOB_ID=<valid_job_uuid> `
  tests/load/k6-stepwise.js
```

**Durasi:** ~13 menit total. Lihat di mana latency mulai naik tajam (knee point).

---

## Jalankan Workers Sebelum Test

```powershell
# Sebelum test: pastikan queue workers berjalan
.\start-workers.ps1              # 3 workers (default)
.\start-workers.ps1 -Workers 5  # 5 workers untuk load test berat

# Atau satu instance manual:
php artisan queue:work redis --sleep=3 --tries=3
```

---

## Target Angka Sehat

| Metric | Target | Red Flag |
|--------|--------|----------|
| Polling p95 | < 100ms | > 500ms |
| Heavy trigger p95 | < 500ms | > 2000ms |
| Error rate | < 1% | > 1% |
| CPU (saat test) | < 70% | > 80% |
| Queue backlog | < 10 job | > 100 job terus-menerus |

## Monitor Saat Test Berjalan

```powershell
# Queue status (PowerShell)
while ($true) {
    php artisan queue:monitor redis:default
    Start-Sleep 5
}

# DB connections (MySQL)
# SELECT COUNT(*), status FROM information_schema.processlist GROUP BY status;

# Laravel log tail
Get-Content storage/logs/laravel.log -Wait -Tail 50
```
