# 📋 Sistem Pengajuan Transaksi Resmi Perusahaan

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-red" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-blue" alt="PHP">
  <img src="https://img.shields.io/badge/Bootstrap-5-purple" alt="Bootstrap">
  <img src="https://img.shields.io/badge/MySQL-8.0+-orange" alt="MySQL">
</p>

## 📖 Tentang Aplikasi

Sistem Pengajuan Transaksi Resmi Perusahaan adalah aplikasi web berbasis Laravel yang dirancang untuk mengelola dan memproses pengajuan transaksi dengan sistem approval bertingkat. Aplikasi ini memastikan setiap transaksi melalui proses verifikasi yang ketat dari berbagai level pejabat sebelum dapat dieksekusi.

### ✨ Fitur Utama

- 🔐 **Authentication & Authorization** - Role-based access control untuk 5 tipe user
- 📝 **Form Pengajuan Lengkap** - 11 field data + upload dokumen
- ✅ **Approval Workflow** - Sistem persetujuan 4 tingkat (Pejabat 1 → 2 → 3 → 4)
- 📊 **Dashboard Interaktif** - Statistik dan monitoring real-time
- ⚡ **AJAX CRUD** - Operasi tanpa reload halaman menggunakan jQuery
- 📱 **Responsive Design** - Bootstrap 5 admin template yang mobile-friendly
- 📎 **Upload Dokumen** - Support PDF, DOC, XLS, dan gambar
- 🔔 **Timeline Tracking** - Lacak progress approval secara visual
- 💾 **Auto-save Draft** - Simpan progress sebelum submit

## 🚀 Quick Start

### Instalasi Otomatis (Windows)

```powershell
# Double-click file install.bat
# Ikuti petunjuk di layar
```

### Instalasi Manual

```powershell
# 1. Install dependencies
composer install

# 2. Setup environment
Copy-Item .env.example .env
php artisan key:generate

# 3. Konfigurasi database di .env, lalu buat database
# DB_DATABASE=transaksi_perusahaan

# 4. Migrate & seed
php artisan migrate
php artisan db:seed
php artisan storage:link

# 5. Jalankan server
php artisan serve
```

Buka browser: **http://localhost:8000**

## 🔐 Akun Default

| Role | Email | Password |
|------|-------|----------|
| Pemohon | pemohon@perusahaan.com | password |
| Pejabat 1 | pejabat1@perusahaan.com | password |
| Pejabat 2 | pejabat2@perusahaan.com | password |
| Pejabat 3 | pejabat3@perusahaan.com | password |
| Pejabat 4 | pejabat4@perusahaan.com | password |

## 📚 Dokumentasi Lengkap

- 📖 [Installation Guide](INSTALLATION_GUIDE.md) - Panduan instalasi detail
- 🚀 [Quick Start Guide](QUICK_START.md) - Memulai dengan cepat
- 💻 [Commands Reference](COMMANDS.md) - Laravel Artisan commands
- 🌐 [API Documentation](API_DOCUMENTATION.md) - Endpoint & responses
- 🚢 [Deployment Checklist](DEPLOYMENT_CHECKLIST.md) - Production deployment
- 📊 [Project Summary](PROJECT_SUMMARY.md) - Ringkasan lengkap proyek

## 🛠️ Teknologi

- **Backend:** Laravel 12, PHP 8.2+
- **Database:** MySQL 8.0+
- **Frontend:** Bootstrap 5, jQuery 3.7
- **UI Components:** DataTables, Select2, SweetAlert2, Bootstrap Icons

## 📋 Alur Approval

### Forward Flow (Pengajuan ke Persetujuan)
```
Draft → Pejabat 1 (Review) → Diskusi Pra-Permohonan → 
Pejabat 2 (Pemeriksaan) → Pejabat 3 (Approval) → 
Pejabat 4 (Final Approval) ✅
```

### Backward Flow (Penerusan Dokumen Disetujui)
```
Pejabat 4 (Setuju) → Pejabat 3 (Teruskan) → 
Pejabat 2 (Selesaikan) → Pemohon (Terima) ✅
```

**Catatan:**
- Pejabat 2 dapat meminta kelengkapan data jika diperlukan
- Setelah Pejabat 4 menyetujui, dokumen diteruskan kembali melalui Pejabat 3 → Pejabat 2 → Pemohon
- Pemohon dapat mengarsipkan formulir yang telah disetujui sebagai dasar transaksi

## 🎯 Ketentuan Pengajuan

1. Formulir diajukan setelah persetujuan Pejabat 1
2. **Waktu pengajuan: Senin & Kamis sebelum 12.00 WIB**
3. Pengajuan lewat waktu diproses hari kerja berikutnya
4. Wajib menggunakan formulir resmi dengan data lengkap
5. Pengaju bertanggung jawab atas kebenaran data

## 📦 Persyaratan Sistem

- PHP >= 8.2
- Composer
- MySQL/MariaDB >= 8.0
- Web Server (Apache/Nginx)
- Extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

## 🔒 Keamanan

Aplikasi ini dilengkapi dengan:
- CSRF Protection
- SQL Injection Prevention
- XSS Protection
- Authentication & Authorization
- File Upload Validation
- Password Hashing (Bcrypt)

## 📱 Browser Support

- Chrome (Recommended)
- Firefox
- Microsoft Edge
- Safari
- Mobile Browsers

## 🤝 Kontribusi

Untuk kontribusi atau melaporkan bug, silakan hubungi tim development.

## 📄 License

Proprietary - Internal Company Use Only

---

**Built with ❤️ using Laravel Framework**

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
