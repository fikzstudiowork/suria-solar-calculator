@echo off
REM Build cPanel upload package for calculator.suriainfiniti.com
setlocal
cd /d "%~dp0.."
set ROOT=%CD%
set PKG=%ROOT%\deploy\cpanel-upload
set ZIP=%ROOT%\deploy\suria-calculator-cpanel.zip

echo === Suria Calculator cPanel Package ===

if exist "%PKG%" rmdir /s /q "%PKG%"
mkdir "%PKG%"
mkdir "%PKG%\uploads"

echo [1/4] Building frontend...
cd "%ROOT%\frontend"
set NEXT_PUBLIC_API_URL=https://calculator.suriainfiniti.com
set NEXT_PUBLIC_TURNSTILE_SITE_KEY=1x00000000000000000000AA
call npm run build
if errorlevel 1 (
  echo Build failed!
  exit /b 1
)

echo [2/4] Copying frontend export...
xcopy /E /I /Y "%ROOT%\frontend\out\*" "%PKG%\"

echo [2b/4] Rename _next to next (cPanel/LiteSpeed fix)...
node "%ROOT%\deploy\rename-next-assets.mjs" "%PKG%"
if exist "%PKG%\_next" rmdir /s /q "%PKG%\_next"
if exist "%PKG%\next" copy /Y "%ROOT%\deploy\next-htaccess" "%PKG%\next\.htaccess"

echo [3/4] Copying backend...
xcopy /E /I /Y "%ROOT%\backend\api" "%PKG%\api\"
xcopy /E /I /Y "%ROOT%\backend\admin" "%PKG%\admin\"
xcopy /E /I /Y "%ROOT%\backend\includes" "%PKG%\includes\"
copy /Y "%ROOT%\backend\.htaccess" "%PKG%\"
copy /Y "%ROOT%\backend\setup.php" "%PKG%\"
copy /Y "%ROOT%\backend\setup-web.php" "%PKG%\"
copy /Y "%ROOT%\backend\setup-all-web.php" "%PKG%\"
copy /Y "%ROOT%\backend\setup-config-web.php" "%PKG%\"
copy /Y "%ROOT%\backend\check-health.php" "%PKG%\"
copy /Y "%ROOT%\backend\config.example.php" "%PKG%\"
copy /Y "%ROOT%\backend\db\schema.sql" "%PKG%\schema.sql"

echo [4/4] Creating ZIP...
if exist "%ZIP%" del "%ZIP%"
powershell -NoProfile -Command "Compress-Archive -Path '%PKG%\*' -DestinationPath '%ZIP%' -Force"

echo.
echo DONE!
echo Upload folder: %PKG%
echo ZIP file:      %ZIP%
echo.
echo Next: see CPANEL-PASANG.md
endlocal
