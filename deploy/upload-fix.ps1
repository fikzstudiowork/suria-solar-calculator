# Upload fix ke cPanel via FTP
# Usage: powershell -ExecutionPolicy Bypass -File deploy\upload-fix.ps1
#        powershell -ExecutionPolicy Bypass -File deploy\upload-fix.ps1 -SkipBuild

param([switch]$SkipBuild)

$ErrorActionPreference = "Stop"
$CredsFile = Join-Path $PSScriptRoot "ftp-credentials.ps1"

if (-not (Test-Path $CredsFile)) {
    Write-Host ""
    Write-Host "FAIL: Buat file deploy\ftp-credentials.ps1 dulu." -ForegroundColor Red
    Write-Host "Copy dari deploy\ftp-credentials.example.ps1 dan isi password FTP." -ForegroundColor Yellow
    Write-Host ""
    exit 1
}

. $CredsFile

if (-not $SkipBuild) {
    Write-Host "=== Step 1/3: Build package ===" -ForegroundColor Cyan
    & cmd /c (Join-Path $PSScriptRoot "build-cpanel.bat")
    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
} else {
    Write-Host "=== Step 1/3: Skip build ===" -ForegroundColor Yellow
}

$LocalRoot = Join-Path $PSScriptRoot "cpanel-upload"
if (-not (Test-Path $LocalRoot)) {
    Write-Host "FAIL: cpanel-upload folder missing" -ForegroundColor Red
    exit 1
}

$UploadItems = @(
    "index.html",
    "404.html",
    "404",
    "next",
    ".htaccess",
    "admin",
    "includes",
    "api",
    "check-health.php",
    "setup-web.php",
    "setup-all-web.php",
    "setup-config-web.php",
    "config.example.php",
    "schema.sql"
)

function Upload-File {
    param([string]$LocalFile, [string]$RemoteFile)

    $remotePath = $RemoteFile -replace "\\", "/"
    $url = "ftp://$FtpHost/$remotePath"
    $auth = "$FtpUser`:$FtpPass"

    $attempts = @(
        @("-k", "--ftp-ssl-reqd", "--ftp-create-dirs", "-T", $LocalFile, "--user", $auth, $url),
        @("--ftp-create-dirs", "-T", $LocalFile, "--user", $auth, $url),
        @("-k", "--ftp-create-dirs", "-T", $LocalFile, "--user", $auth, "ftps://$FtpHost/$remotePath")
    )

    $labels = @("FTP TLS port 21", "FTP plain port 21", "FTPS port 990")
    $lastErr = ""

    for ($i = 0; $i -lt $attempts.Count; $i++) {
        Write-Host "    try: $($labels[$i])" -ForegroundColor DarkGray
        $prevEap = $ErrorActionPreference
        $ErrorActionPreference = "Continue"
        $out = & curl.exe -sS @($attempts[$i]) 2>&1
        $code = $LASTEXITCODE
        $ErrorActionPreference = $prevEap
        if ($code -eq 0) { return }
        $lastErr = ($out | Out-String).Trim()
    }

    throw "Upload failed: $LocalFile -> $remotePath - $lastErr"
}

function Upload-Dir {
    param([string]$LocalDir, [string]$RemoteDir)
    Get-ChildItem -Path $LocalDir -Recurse -File | ForEach-Object {
        $rel = $_.FullName.Substring($LocalDir.Length).TrimStart("\","/")
        $remote = ($RemoteDir.TrimEnd("/") + "/" + ($rel -replace "\\", "/")).TrimStart("/")
        Write-Host "  -> $remote"
        Upload-File -LocalFile $_.FullName -RemoteFile $remote
    }
}

Write-Host ""
Write-Host "=== Step 2/3: Upload to ftp://$FtpHost/ ===" -ForegroundColor Cyan

foreach ($item in $UploadItems) {
    $local = Join-Path $LocalRoot $item
    if (-not (Test-Path $local)) {
        Write-Host "SKIP missing: $item" -ForegroundColor Yellow
        continue
    }
    if ($RemoteRoot) {
        $remote = "$RemoteRoot/$($item -replace '\\','/')"
    } else {
        $remote = ($item -replace '\\','/')
    }
    if (Test-Path $local -PathType Container) {
        Write-Host "Upload folder: $item"
        Upload-Dir -LocalDir $local -RemoteDir $remote
    } else {
        Write-Host "Upload file: $item"
        Upload-File -LocalFile $local -RemoteFile $remote
    }
}

Write-Host ""
Write-Host "=== Step 3/3: Done ===" -ForegroundColor Green
Write-Host "Test CSS URL on calculator.suriainfiniti.com/next/static/css/"
Write-Host "Then hard refresh calculator homepage"
Write-Host ""
