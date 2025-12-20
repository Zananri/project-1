@echo off
echo ========================================
echo Instalasi Sistem Pengajuan Transaksi
echo ========================================
echo.

REM Check if composer is installed
where composer >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Composer tidak ditemukan!
    echo Silakan install Composer terlebih dahulu dari https://getcomposer.org
    pause
    exit /b 1
)

REM Check if php is installed
where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: PHP tidak ditemukan!
    echo Silakan install PHP atau XAMPP terlebih dahulu
    pause
    exit /b 1
)

echo [1/8] Installing Composer dependencies...
call composer install
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Composer install gagal!
    pause
    exit /b 1
)

echo.
echo [2/8] Copying .env.example to .env...
if not exist .env (
    copy .env.example .env
    echo .env file created successfully!
) else (
    echo .env file already exists, skipping...
)

echo.
echo [3/8] Generating application key...
call php artisan key:generate

echo.
echo [4/8] Creating storage link...
call php artisan storage:link

echo.
echo ========================================
echo Database Configuration
echo ========================================
echo.
echo Silakan buat database MySQL terlebih dahulu dengan nama: transaksi_perusahaan
echo Atau edit file .env untuk menggunakan nama database yang berbeda
echo.
echo Tekan Enter untuk melanjutkan setelah database dibuat...
pause >nul

echo.
echo [5/8] Running migrations...
call php artisan migrate
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR: Migration gagal!
    echo Pastikan:
    echo 1. Database sudah dibuat
    echo 2. Konfigurasi database di .env sudah benar
    echo 3. MySQL service sudah running
    pause
    exit /b 1
)

echo.
echo [6/8] Running seeders...
call php artisan db:seed

echo.
echo [7/8] Clearing cache...
call php artisan optimize:clear

echo.
echo [8/8] Optimizing application...
call php artisan optimize

echo.
echo ========================================
echo Installation Complete!
echo ========================================
echo.
echo Aplikasi berhasil diinstall!
echo.
echo Akun Default:
echo - Pemohon: pemohon@perusahaan.com / password
echo - Pejabat 1: pejabat1@perusahaan.com / password
echo - Pejabat 2: pejabat2@perusahaan.com / password
echo - Pejabat 3: pejabat3@perusahaan.com / password
echo - Pejabat 4: pejabat4@perusahaan.com / password
echo.
echo Untuk menjalankan aplikasi:
echo php artisan serve
echo.
echo Lalu buka browser: http://localhost:8000
echo.
pause
