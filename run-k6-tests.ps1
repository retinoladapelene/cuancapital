$env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")
$TOKEN = (php get-k6-token.php 2>$null).Trim()
$JOB   = "019ca957-9012-709c-8a19-397eb64d8659"
$BASE  = "http://localhost:8000"
Write-Host "Token OK, length: $($TOKEN.Length)"
Write-Host "--- TEST 1: Polling Flood 5VU 30s ---"
k6 run --vus 5 --duration 30s -e BASE_URL=$BASE -e TOKEN=$TOKEN -e JOB_ID=$JOB tests/load/k6-polling-flood.js
Write-Host "--- TEST 2: Heavy Endpoints 10VU 30s ---"
k6 run --vus 10 --duration 30s -e BASE_URL=$BASE -e TOKEN=$TOKEN tests/load/k6-heavy-endpoints.js
Write-Host "--- DONE ---"
