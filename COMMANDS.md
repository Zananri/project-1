# Panduan Command Laravel - Sistem Pengajuan Transaksi

## Persiapan Awal

### 1. Install Dependencies
```powershell
# Install Composer dependencies
composer install

# Install NPM dependencies (jika ada)
npm install
```

### 2. Environment Setup
```powershell
# Copy .env.example
Copy-Item .env.example .env

# Generate application key
php artisan key:generate

# Create storage link
php artisan storage:link
```

## Database Commands

### Migration
```powershell
# Jalankan semua migration
php artisan migrate

# Jalankan migration dengan seeder
php artisan migrate --seed

# Rollback migration terakhir
php artisan migrate:rollback

# Rollback semua migration
php artisan migrate:reset

# Reset dan migrate ulang
php artisan migrate:fresh

# Reset, migrate, dan seed
php artisan migrate:fresh --seed
```

### Seeder
```powershell
# Jalankan semua seeder
php artisan db:seed

# Jalankan seeder tertentu
php artisan db:seed --class=UserSeeder
```

## Cache Commands

### Clear Cache
```powershell
# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Clear all cache
php artisan optimize:clear
```

### Build Cache
```powershell
# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize application
php artisan optimize
```

## Development Commands

### Run Development Server
```powershell
# Start server (default port 8000)
php artisan serve

# Start server dengan port custom
php artisan serve --port=8080

# Start server dengan host custom
php artisan serve --host=192.168.1.100
```

### Tinker (Laravel REPL)
```powershell
# Masuk ke Laravel Tinker
php artisan tinker

# Contoh penggunaan di Tinker:
# User::all();
# Transaction::count();
# User::where('role', 'pemohon')->first();
```

## Useful Commands

### List Routes
```powershell
# Lihat semua routes
php artisan route:list

# Filter routes by name
php artisan route:list --name=transactions

# Filter routes by method
php artisan route:list --method=POST
```

### Generate Code
```powershell
# Generate controller
php artisan make:controller NamaController

# Generate model
php artisan make:model NamaModel

# Generate migration
php artisan make:migration create_nama_table

# Generate seeder
php artisan make:seeder NamaSeeder

# Generate middleware
php artisan make:middleware NamaMiddleware

# Generate request
php artisan make:request NamaRequest
```

### Database Info
```powershell
# Show database info
php artisan db:show

# Show table info
php artisan db:table users

# Monitor database queries
php artisan db:monitor
```

## Maintenance Commands

### Down/Up Mode
```powershell
# Put application in maintenance mode
php artisan down

# Remove application from maintenance mode
php artisan up

# Maintenance mode with secret
php artisan down --secret="rahasia123"
# Akses: http://localhost:8000/rahasia123
```

### Storage Commands
```powershell
# Create storage link
php artisan storage:link

# Delete storage link
Remove-Item public/storage
```

## Specific to This Project

### Reset Database Completely
```powershell
# 1. Drop all tables and migrate fresh
php artisan migrate:fresh

# 2. Seed users
php artisan db:seed --class=UserSeeder
```

### Create New User Manually
```powershell
php artisan tinker

# Di Tinker:
User::create([
    'name' => 'Nama User',
    'email' => 'email@example.com',
    'password' => Hash::make('password'),
    'role' => 'pemohon',
    'jabatan' => 'Staff',
    'divisi' => 'Keuangan'
]);
```

### Check Application Status
```powershell
# About application
php artisan about

# Check environment
php artisan env

# List installed packages
composer show
```

## Troubleshooting Commands

### Fix Permissions (Linux/Mac)
```bash
# Set proper permissions
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Fix Permissions (Windows)
```powershell
# Reset permissions
icacls storage /grant Everyone:(OI)(CI)F /T
icacls bootstrap/cache /grant Everyone:(OI)(CI)F /T
```

### Regenerate Composer Autoload
```powershell
composer dump-autoload
```

### Clear Everything
```powershell
# Clear all caches and optimize
php artisan optimize:clear
php artisan optimize
```

## Production Deployment Commands

### Optimize for Production
```powershell
# Update .env
# APP_ENV=production
# APP_DEBUG=false

# Install production dependencies only
composer install --optimize-autoloader --no-dev

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### Security
```powershell
# Generate new app key (HATI-HATI: akan logout semua user)
php artisan key:generate

# Clear sensitive data
php artisan cache:clear
php artisan config:clear
```

## Backup & Restore

### Manual Backup Database
```powershell
# Export database
mysqldump -u root -p transaksi_perusahaan > backup.sql

# Import database
mysql -u root -p transaksi_perusahaan < backup.sql
```

### Backup Storage
```powershell
# Backup storage folder
Compress-Archive -Path storage/app/public/* -DestinationPath backup-storage.zip
```

## Testing Commands

### Run Tests
```powershell
# Run all tests
php artisan test

# Run specific test
php artisan test --filter NamaTest

# Run with coverage
php artisan test --coverage
```

## Queue Commands (jika menggunakan queue)

```powershell
# Run queue worker
php artisan queue:work

# Run queue listener
php artisan queue:listen

# Restart queue workers
php artisan queue:restart

# Clear failed jobs
php artisan queue:flush
```

## Logging

### View Logs
```powershell
# Tail log file (Linux/Mac)
tail -f storage/logs/laravel.log

# View last 100 lines (PowerShell)
Get-Content storage/logs/laravel.log -Tail 100

# Clear logs
Remove-Item storage/logs/*.log
```

## Custom Artisan Commands (jika ada)

```powershell
# Example: Clear old transactions
php artisan transactions:cleanup

# Example: Send reminder emails
php artisan transactions:remind
```

## Quick Reference Card

| Task | Command |
|------|---------|
| Start server | `php artisan serve` |
| Reset database | `php artisan migrate:fresh --seed` |
| Clear all cache | `php artisan optimize:clear` |
| View routes | `php artisan route:list` |
| Laravel tinker | `php artisan tinker` |
| Create storage link | `php artisan storage:link` |
| Run seeder | `php artisan db:seed` |
| Generate key | `php artisan key:generate` |

---

**Note:** Selalu backup database sebelum menjalankan migration reset atau fresh!
