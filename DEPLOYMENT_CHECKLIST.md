# Deployment Checklist - Sistem Pengajuan Transaksi

## Pre-Deployment Checklist

### ✅ Code Quality
- [ ] Semua fitur sudah ditest
- [ ] Tidak ada error di console browser
- [ ] Tidak ada error di Laravel log
- [ ] Code sudah di-review
- [ ] Dokumentasi sudah lengkap

### ✅ Security
- [ ] APP_ENV = production di .env
- [ ] APP_DEBUG = false di .env
- [ ] APP_KEY sudah di-generate
- [ ] Password default sudah diganti
- [ ] CSRF protection aktif
- [ ] SQL injection prevention
- [ ] XSS protection aktif
- [ ] File upload validation
- [ ] Authentication & authorization properly implemented

### ✅ Database
- [ ] Backup database development
- [ ] Migration sudah final
- [ ] Seeder untuk production ready
- [ ] Index database sudah optimal
- [ ] Foreign keys sudah benar

### ✅ Performance
- [ ] Config cache: `php artisan config:cache`
- [ ] Route cache: `php artisan route:cache`
- [ ] View cache: `php artisan view:cache`
- [ ] Autoloader optimized: `composer dump-autoload --optimize`
- [ ] Production dependencies only: `composer install --no-dev --optimize-autoloader`
- [ ] Images optimized
- [ ] CSS/JS minified (jika ada build process)

### ✅ Files & Folders
- [ ] .env tidak di-commit ke git
- [ ] .gitignore properly configured
- [ ] storage folder writable
- [ ] bootstrap/cache writable
- [ ] Storage link created
- [ ] Vendor folder ada (jika tidak via Composer)

## Server Requirements

### PHP Requirements
- [ ] PHP >= 8.2
- [ ] BCMath extension
- [ ] Ctype extension
- [ ] Fileinfo extension
- [ ] JSON extension
- [ ] Mbstring extension
- [ ] OpenSSL extension
- [ ] PDO extension
- [ ] PDO MySQL extension
- [ ] Tokenizer extension
- [ ] XML extension

### Server Configuration
- [ ] Apache/Nginx configured
- [ ] Document root pointing to /public
- [ ] mod_rewrite enabled (Apache)
- [ ] .htaccess working
- [ ] PHP memory_limit >= 128M
- [ ] upload_max_filesize = 10M
- [ ] post_max_size = 10M
- [ ] max_execution_time = 300

### Database Server
- [ ] MySQL/MariaDB >= 8.0
- [ ] Database created
- [ ] Database user created with proper permissions
- [ ] UTF8MB4 charset
- [ ] Connection tested

## Deployment Steps

### 1. Upload Files
```bash
# Option A: Via FTP
Upload all files except:
- .git
- .env (will be created on server)
- node_modules
- storage/* (will be created)
- bootstrap/cache/*

# Option B: Via Git
git clone repository-url
cd repository-name
```

### 2. Install Dependencies
```bash
# Install Composer dependencies (production only)
composer install --no-dev --optimize-autoloader

# If NPM is used
npm install --production
npm run build
```

### 3. Environment Configuration
```bash
# Copy .env.example
cp .env.example .env

# Edit .env with production values
nano .env

# Generate application key
php artisan key:generate
```

### 4. Environment Variables (.env)
```env
APP_NAME="Sistem Pengajuan Transaksi"
APP_ENV=production
APP_KEY=base64:... (auto-generated)
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_strong_password

SESSION_DRIVER=file
SESSION_LIFETIME=120

CACHE_STORE=file
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Set Permissions
```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows (Run as Administrator)
icacls storage /grant Everyone:(OI)(CI)F /T
icacls bootstrap\cache /grant Everyone:(OI)(CI)F /T
```

### 6. Run Migrations
```bash
# Run migrations
php artisan migrate --force

# Seed production data (users only)
php artisan db:seed --class=UserSeeder --force
```

### 7. Create Storage Link
```bash
php artisan storage:link
```

### 8. Optimize Application
```bash
# Clear all cache
php artisan optimize:clear

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize composer autoloader
composer dump-autoload --optimize
```

### 9. Verify Installation
- [ ] Visit homepage - Should show login page
- [ ] Login with test account
- [ ] Create test transaction
- [ ] Upload file test
- [ ] Approve/reject test
- [ ] Check all menu navigation
- [ ] Test responsive design
- [ ] Check browser console for errors

## Apache Configuration (.htaccess in /public)

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

## Nginx Configuration

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/html/nama-aplikasi/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## SSL Configuration (Production)

### Using Let's Encrypt (Free)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache

# Get certificate
sudo certbot --apache -d yourdomain.com

# Auto-renew
sudo certbot renew --dry-run
```

### Update .env
```env
APP_URL=https://yourdomain.com
SESSION_SECURE_COOKIE=true
```

## Post-Deployment

### Change Default Passwords
```bash
# Via Tinker
php artisan tinker

# Update password
$user = User::where('email', 'pemohon@perusahaan.com')->first();
$user->password = Hash::make('new_strong_password');
$user->save();
```

### Setup Backup
```bash
# Install backup package (optional)
composer require spatie/laravel-backup

# Or manual backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u user -p database_name > backup_$DATE.sql
tar -czf backup_$DATE.tar.gz storage/app/public
```

### Setup Monitoring
- [ ] Setup error monitoring (Sentry, Bugsnag)
- [ ] Setup uptime monitoring
- [ ] Setup log rotation
- [ ] Setup database backup automation
- [ ] Setup email notifications for errors

### Setup Maintenance
```bash
# Create maintenance page
php artisan down --render="errors::503"

# Bring back up
php artisan up
```

## Rollback Plan

### If Deployment Fails
1. Keep old version in separate folder
2. Database backup before migration
3. Quick rollback script:
```bash
#!/bin/bash
# Restore database
mysql -u user -p database_name < backup.sql

# Restore files
rm -rf /var/www/html/current
mv /var/www/html/backup /var/www/html/current

# Clear cache
php artisan optimize:clear
```

## Testing in Production

### Smoke Tests
- [ ] Login works
- [ ] Dashboard loads
- [ ] Create transaction works
- [ ] Edit transaction works
- [ ] Delete transaction works
- [ ] Approve flow works
- [ ] File upload works
- [ ] Email notifications work (if configured)

### Performance Tests
- [ ] Page load time < 3s
- [ ] Database queries optimized
- [ ] No N+1 query problems
- [ ] Images loading fast
- [ ] AJAX requests fast

## Maintenance Mode

### Enable
```bash
php artisan down --secret="token123"
# Access via: https://yourdomain.com/token123
```

### Disable
```bash
php artisan up
```

## Common Issues & Solutions

### Issue: 500 Internal Server Error
**Solution:**
1. Check `storage/logs/laravel.log`
2. Verify folder permissions
3. Check .env configuration
4. Run `php artisan optimize:clear`

### Issue: Mix/Vite manifest not found
**Solution:**
```bash
# If using Vite
npm install
npm run build

# If using Mix
npm install
npm run production
```

### Issue: Storage link broken
**Solution:**
```bash
rm public/storage
php artisan storage:link
```

### Issue: Routes not working
**Solution:**
```bash
php artisan route:clear
php artisan route:cache
```

## Security Hardening

### Additional Security
- [ ] Disable directory listing
- [ ] Hide PHP version
- [ ] Add security headers
- [ ] Rate limiting configured
- [ ] CORS configured properly
- [ ] Input sanitization
- [ ] Output escaping
- [ ] SQL injection prevention
- [ ] XSS prevention
- [ ] CSRF protection

### .htaccess Security Headers
```apache
<IfModule mod_headers.c>
    Header set X-XSS-Protection "1; mode=block"
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
```

## Final Checklist

- [ ] Application accessible via domain
- [ ] HTTPS working (SSL certificate valid)
- [ ] All features working as expected
- [ ] Error pages customized
- [ ] Contact information updated
- [ ] Terms & privacy policy added (if needed)
- [ ] Analytics configured (if needed)
- [ ] Backup system working
- [ ] Monitoring alerts configured
- [ ] Documentation updated
- [ ] Admin trained on system
- [ ] Users notified of launch

---

**Remember:** Always test in staging environment first before deploying to production!
