# Remote full setup via setup-all-web.php
# Usage:
#   1. Copy deploy\db-credentials.example.ps1 → deploy\db-credentials.ps1
#   2. Fill MySQL password + admin password
#   3. powershell -ExecutionPolicy Bypass -File deploy\setup-remote.ps1

param(
    [string]$SetupUrl = 'https://calculator.suriainfiniti.com/setup-all-web.php'
)

$ErrorActionPreference = 'Stop'
$CredsFile = Join-Path $PSScriptRoot 'db-credentials.ps1'

if (-not (Test-Path $CredsFile)) {
    Write-Host 'FAIL: Create deploy\db-credentials.ps1 from db-credentials.example.ps1 first.' -ForegroundColor Red
    exit 1
}

. $CredsFile

if ($DbPass -eq 'PASTE_MYSQL_PASSWORD_HERE' -or -not $DbPass) {
    Write-Host 'FAIL: Set MySQL password in deploy\db-credentials.ps1' -ForegroundColor Red
    exit 1
}

$body = @{
    db_host    = $DbHost
    db_name    = $DbName
    db_user    = $DbUser
    db_pass    = $DbPass
    admin_user = $AdminUser
    admin_pass = $AdminPass
}

Write-Host "Running remote setup at $SetupUrl ..." -ForegroundColor Cyan

$response = Invoke-WebRequest -Uri $SetupUrl -Method POST -Body $body -UseBasicParsing -TimeoutSec 60

if ($response.Content -match 'Setup complete') {
    Write-Host 'SUCCESS: Setup complete!' -ForegroundColor Green
    Write-Host "Admin login: https://calculator.suriainfiniti.com/admin/login.php"
    Write-Host "Username: $AdminUser"
    exit 0
}

if ($response.Content -match 'class="err"[^>]*>([^<]+)') {
    Write-Host "FAIL: $($Matches[1])" -ForegroundColor Red
    exit 1
}

Write-Host 'Unknown response — open setup page in browser to verify.' -ForegroundColor Yellow
Write-Host $SetupUrl
