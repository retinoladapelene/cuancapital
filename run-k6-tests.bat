@echo off
:: Step 1: Get token into a file first
php get-k6-token.php 2>nul 1>"%TEMP%\k6tok.txt"

:: Step 2: Read file into variable the proper CMD way
set K6_TOKEN=
for /f "delims=" %%i in (%TEMP%\k6tok.txt) do set K6_TOKEN=%%i

echo Token received (length check)
echo Token starts with: %K6_TOKEN:~0,5%...

echo.
echo -----------------------------------------------
echo  TEST 1: Polling Flood Smoke (5 VU x 30s)
echo -----------------------------------------------
k6 run --vus 5 --duration 30s ^
  -e "BASE_URL=http://localhost:8000" ^
  -e "TOKEN=%K6_TOKEN%" ^
  -e "JOB_ID=019ca957-9012-709c-8a19-397eb64d8659" ^
  tests\load\k6-polling-flood.js

echo.
echo -----------------------------------------------
echo  TEST 2: Heavy Endpoints (10 VU x 30s)
echo -----------------------------------------------
k6 run --vus 10 --duration 30s ^
  -e "BASE_URL=http://localhost:8000" ^
  -e "TOKEN=%K6_TOKEN%" ^
  tests\load\k6-heavy-endpoints.js

echo.
echo -----------------------------------------------
echo  ALL TESTS COMPLETE
echo -----------------------------------------------
pause
