# ─────────────────────────────────────────────────────────────────────────────
# start-workers.ps1 — Local dev multi-worker launcher (Windows)
#
# Launches 3 queue workers in separate PowerShell windows.
# Usage: .\start-workers.ps1
# Usage (custom count): .\start-workers.ps1 -Workers 5
#
# ⚠️  Requires Redis to be running (Memurai or WSL redis-server)
# ─────────────────────────────────────────────────────────────────────────────

param(
    [int]$Workers = 3
)

$AppDir = $PSScriptRoot
$PHP    = "php"

Write-Host ""
Write-Host "🚀 Starting $Workers queue workers..." -ForegroundColor Cyan
Write-Host "   App: $AppDir"
Write-Host "   Queue: redis (default)"
Write-Host ""

1..$Workers | ForEach-Object {
    $WorkerNum = $_
    Start-Process powershell -ArgumentList @(
        "-NoExit",
        "-Command",
        "Set-Location '$AppDir'; Write-Host '⚙  Worker #$WorkerNum started' -ForegroundColor Green; $PHP artisan queue:work redis --sleep=3 --tries=3 --timeout=120"
    ) -WindowStyle Normal

    Write-Host "  ✅ Worker #$WorkerNum window opened" -ForegroundColor Green
    Start-Sleep -Milliseconds 300
}

Write-Host ""
Write-Host "✔ All $Workers workers running. Close windows individually to stop." -ForegroundColor Yellow
Write-Host "  Monitor queue: php artisan queue:monitor"
Write-Host ""
