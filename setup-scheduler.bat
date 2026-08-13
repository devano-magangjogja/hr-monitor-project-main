@echo off
:: Jalankan file ini sebagai Administrator (klik kanan > Run as administrator)

SET PHP_PATH=C:\laragon\bin\php\php8.3.14\php.exe
SET ARTISAN_PATH=C:\laragon\www\hr-monitor-project\artisan
SET WORKING_DIR=C:\laragon\www\hr-monitor-project

echo ============================================
echo  Setup Laravel Scheduler - HR-DWMS
echo ============================================

:: Hapus task lama jika ada
schtasks /delete /tn "LaravelScheduler-HR-DWMS" /f >nul 2>&1

:: Buat task baru — jalankan setiap menit
schtasks /create ^
  /tn "LaravelScheduler-HR-DWMS" ^
  /tr "\"%PHP_PATH%\" \"%ARTISAN_PATH%\" schedule:run" ^
  /sc MINUTE ^
  /mo 1 ^
  /sd 01/01/2024 ^
  /rl HIGHEST ^
  /f

IF %ERRORLEVEL% EQU 0 (
    echo.
    echo [OK] Scheduler berhasil didaftarkan.
    echo      Laravel akan menjalankan schedule:run setiap 1 menit.
    echo.
    echo Jadwal aktif:
    echo   - tasks:generate-daily : Setiap Senin-Sabtu jam 08:00 WIB
    echo   - tasks:mark-not-done  : Setiap hari jam 21:00 WIB
) ELSE (
    echo.
    echo [ERROR] Gagal mendaftarkan scheduler.
    echo         Pastikan file ini dijalankan sebagai Administrator.
)

echo.
pause
