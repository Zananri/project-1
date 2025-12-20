# 📋 SISTEM PENGAJUAN TRANSAKSI RESMI PERUSAHAAN

## 🎯 Ringkasan Proyek

Aplikasi web berbasis Laravel untuk mengelola pengajuan dan approval transaksi resmi perusahaan dengan sistem persetujuan bertingkat.

## ✨ Fitur Lengkap

### 👤 Manajemen User & Role
- ✅ 5 Role: Pemohon, Pejabat 1, Pejabat 2, Pejabat 3, Pejabat 4
- ✅ Role-based access control
- ✅ Authentication & authorization

### 📝 Manajemen Transaksi
- ✅ Form pengajuan lengkap (11 field + lampiran)
- ✅ CRUD transaksi via AJAX
- ✅ Auto-generate nomor transaksi
- ✅ Upload dokumen pendukung (PDF, DOC, XLS, JPG, PNG)
- ✅ Status tracking real-time
- ✅ Timeline approval

### ✅ Sistem Approval Bertingkat
- ✅ Approval flow: Pejabat 1 → 2 → 3 → 4
- ✅ Approve / Reject dengan catatan
- ✅ Request kelengkapan data (Pejabat 2 & 3)
- ✅ Email notifications (ready to implement)

### 📊 Dashboard & Reporting
- ✅ Dashboard dengan statistik
- ✅ Card stats (Total, Pending, Approved, Rejected)
- ✅ Tabel transaksi terbaru
- ✅ Filter & search

### 🎨 User Interface
- ✅ Responsive design (mobile-friendly)
- ✅ Bootstrap 5 admin template
- ✅ Modern & professional
- ✅ Timeline tracking
- ✅ Status badges
- ✅ Interactive modals

### ⚡ Teknologi AJAX
- ✅ Create tanpa reload
- ✅ Update tanpa reload
- ✅ Delete tanpa reload
- ✅ DataTables server-side
- ✅ Form validation real-time
- ✅ SweetAlert notifications

## 📁 Struktur File yang Dibuat

### Backend (Laravel)
```
app/
├── Http/Controllers/
│   ├── Auth/
│   │   └── AuthenticatedSessionController.php ✅
│   ├── DashboardController.php ✅
│   └── TransactionController.php ✅
├── Models/
│   ├── User.php (updated) ✅
│   ├── Transaction.php ✅
│   └── TransactionApproval.php ✅

database/
├── migrations/
│   ├── 2025_12_20_000001_add_role_to_users_table.php ✅
│   ├── 2025_12_20_000002_create_transactions_table.php ✅
│   └── 2025_12_20_000003_create_transaction_approvals_table.php ✅
└── seeders/
    ├── DatabaseSeeder.php (updated) ✅
    └── UserSeeder.php ✅
```

### Frontend (Blade Views)
```
resources/views/
├── layouts/
│   ├── app.blade.php ✅
│   └── partials/
│       ├── sidebar.blade.php ✅
│       ├── header.blade.php ✅
│       └── footer.blade.php ✅
├── auth/
│   └── login.blade.php ✅
├── dashboard.blade.php ✅
└── transactions/
    ├── index.blade.php ✅
    ├── create.blade.php ✅
    ├── show.blade.php ✅
    └── edit.blade.php ✅
```

### Assets (CSS & JavaScript)
```
public/
├── css/
│   └── custom.css ✅
└── js/
    ├── app-custom.js ✅
    ├── transactions-index.js ✅
    ├── transactions-create.js ✅
    ├── transactions-show.js ✅
    └── transactions-edit.js ✅
```

### Routes
```
routes/
├── web.php (updated) ✅
└── auth.php ✅
```

### Dokumentasi
```
INSTALLATION_GUIDE.md ✅ (Panduan instalasi lengkap)
QUICK_START.md ✅ (Panduan quick start)
COMMANDS.md ✅ (Laravel artisan commands)
API_DOCUMENTATION.md ✅ (API endpoints)
DEPLOYMENT_CHECKLIST.md ✅ (Deployment guide)
install.bat ✅ (Windows installation script)
start.bat ✅ (Server start script)
```

## 🗄️ Database Schema

### Users Table
- id, name, email, password
- role (pemohon, pejabat_1-4)
- jabatan, divisi
- timestamps

### Transactions Table
- id, nomor_transaksi, user_id
- nama_pemohon, nama_perusahaan
- tanggal_pengajuan
- uraian_transaksi, total
- dasar_transaksi
- lawan_transaksi, rekening_transaksi
- rencana_tanggal_transaksi
- pengakuan_transaksi, keterangan
- status (13 status)
- alasan_penolakan
- tanggal_disetujui, tanggal_ditolak
- lampiran_dokumen
- timestamps, soft_deletes

### Transaction Approvals Table
- id, transaction_id, user_id
- role (pejabat_1-4)
- status (pending, approved, rejected)
- catatan, tanggal_approval
- timestamps

## 🔐 Akun Default

| Email | Password | Role |
|-------|----------|------|
| pemohon@perusahaan.com | password | Pemohon |
| pejabat1@perusahaan.com | password | Pejabat 1 |
| pejabat2@perusahaan.com | password | Pejabat 2 |
| pejabat3@perusahaan.com | password | Pejabat 3 |
| pejabat4@perusahaan.com | password | Pejabat 4 |

## 🚀 Cara Menjalankan (Quick Start)

### Opsi 1: Menggunakan Script (Rekomendasi)
```powershell
# Double-click file install.bat
# Ikuti instruksi di layar
# Setelah selesai, double-click start.bat
```

### Opsi 2: Manual
```powershell
# 1. Install dependencies
composer install

# 2. Setup environment
Copy-Item .env.example .env
php artisan key:generate

# 3. Buat database 'transaksi_perusahaan' di MySQL

# 4. Migrate & seed
php artisan migrate
php artisan db:seed
php artisan storage:link

# 5. Run server
php artisan serve
```

Akses: **http://localhost:8000**

## 📊 Flow Approval Transaksi

```
┌──────────────┐
│   Draft      │ Pemohon buat transaksi
└──────┬───────┘
       │ Submit
       ▼
┌──────────────────────┐
│ Menunggu Pejabat 1   │ Review & approve
└──────┬───────────────┘
       │ Approve
       ▼
┌──────────────────────┐
│ Diskusi Pra-Permohonan│ Pejabat 2 diskusi
└──────┬───────────────┘
       │ Approve
       ▼
┌──────────────────────┐
│ Menunggu Pejabat 2   │ Review
└──────┬───────────────┘
       │ Approve
       ▼
┌──────────────────────┐
│ Pemeriksaan Tahap 2  │ Detail check
└──────┬───────────────┘
       │ Approve
       ▼
┌──────────────────────┐
│ Menunggu Pejabat 3   │ Review
└──────┬───────────────┘
       │ Approve
       ▼
┌──────────────────────┐
│ Menunggu Pejabat 4   │ Final approval (Business Executive)
└──────┬───────────────┘
       │ Approve
       ▼
┌──────────────────────┐
│ Disetujui Pejabat 4  │ Approved
└──────┬───────────────┘
       │ Inform back
       ▼
┌──────────────────────┐
│   Diinformasikan     │ Notify stakeholders
└──────┬───────────────┘
       │ Complete
       ▼
┌──────────────────────┐
│      Selesai         │ ✅ Transaction executable
└──────────────────────┘
```

**Alternative Flows:**
- ❌ Any step → **Ditolak** (Reject)
- 🔄 Pejabat 2/3 → **Dilengkapi** (Request completion)

## 🎯 Field Form Pengajuan

1. **Nama Pemohon** ⭐ - Nama pengaju
2. **Nama Perusahaan** ⭐ - Perusahaan pengaju
3. **Tanggal Pengajuan** ⭐ - Tanggal submit
4. **Uraian Transaksi** ⭐ - Deskripsi lengkap
5. **Total** ⭐ - Nominal rupiah
6. **Dasar Transaksi** - Dokumen dasar
7. **Lawan Transaksi** - Pihak lawan
8. **Rekening Transaksi** - No. rekening
9. **Rencana Tanggal Transaksi** - Tanggal eksekusi
10. **Pengakuan Transaksi** - Akun biaya
11. **Keterangan** - Info tambahan
12. **Lampiran Dokumen** - File pendukung

⭐ = Wajib diisi

## 🛠️ Teknologi Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Database:** MySQL 8.0+
- **Frontend:** Bootstrap 5, jQuery 3.7
- **UI Components:**
  - DataTables (server-side)
  - Select2 (dropdown enhancement)
  - SweetAlert2 (notifications)
  - Bootstrap Icons
- **Authentication:** Laravel Breeze (custom)
- **File Storage:** Laravel Storage (local/public)

## 📦 Dependencies

### Composer (PHP)
- laravel/framework: ^12.0
- laravel/tinker: ^2.10

### CDN (JavaScript/CSS)
- Bootstrap 5.3.0
- jQuery 3.7.0
- DataTables 1.13.6
- Select2 4.1.0
- SweetAlert2 11
- Bootstrap Icons 1.11.0

## 📝 Ketentuan Pengajuan

1. Form diajukan setelah approval Pejabat 1
2. Pengajuan: **Senin & Kamis sebelum 12.00 WIB**
3. Setelah waktu → proses hari kerja berikutnya
4. Wajib gunakan formulir resmi
5. Pengaju bertanggung jawab atas data

## 🔒 Keamanan

- ✅ CSRF Protection
- ✅ SQL Injection Prevention
- ✅ XSS Protection
- ✅ Authentication required
- ✅ Authorization per role
- ✅ File upload validation
- ✅ Input sanitization
- ✅ Password hashing (bcrypt)

## 📱 Browser Support

- ✅ Chrome (recommended)
- ✅ Firefox
- ✅ Edge
- ✅ Safari
- ✅ Mobile browsers

## 📞 Support & Dokumentasi

Untuk informasi lebih lengkap, baca:
- 📖 [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md) - Panduan instalasi detail
- 🚀 [QUICK_START.md](QUICK_START.md) - Quick start guide
- 💻 [COMMANDS.md](COMMANDS.md) - Laravel commands
- 🌐 [API_DOCUMENTATION.md](API_DOCUMENTATION.md) - API endpoints
- 🚢 [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) - Deployment guide

## 🎓 Developer Notes

### Best Practices Applied
- ✅ MVC Pattern
- ✅ Repository Pattern (dapat ditambahkan)
- ✅ Service Layer (dapat ditambahkan)
- ✅ RESTful API principles
- ✅ DRY (Don't Repeat Yourself)
- ✅ SOLID Principles
- ✅ Code documentation
- ✅ Error handling
- ✅ Input validation

### Code Organization
- Models: Business logic & relationships
- Controllers: HTTP request handling
- Views: Presentation layer
- JavaScript: Client-side interactions
- CSS: Custom styling

### Testing Recommendations
- Unit tests for Models
- Feature tests for Controllers
- Browser tests for UI (Laravel Dusk)

## 🔄 Future Enhancements

Fitur yang dapat ditambahkan:
- [ ] Email notifications
- [ ] Export to PDF/Excel
- [ ] Advanced reporting & analytics
- [ ] Audit trail & activity log
- [ ] Batch operations
- [ ] API for mobile app
- [ ] Real-time notifications (WebSocket)
- [ ] Document versioning
- [ ] Advanced search & filters
- [ ] Dashboard charts & graphs
- [ ] Multi-language support
- [ ] Dark mode

## 📄 License

Proprietary - Internal Company Use Only

## 👨‍💻 Credits

Developed with ❤️ using:
- Laravel Framework
- Bootstrap
- jQuery
- DataTables
- SweetAlert2

---

## ⚠️ Important Notes

### Untuk Development:
1. Gunakan `php artisan serve` untuk testing
2. Check `storage/logs/laravel.log` jika ada error
3. Jalankan `php artisan optimize:clear` jika ada cache issue

### Untuk Production:
1. **WAJIB** ganti semua password default
2. **WAJIB** set `APP_DEBUG=false`
3. **WAJIB** gunakan HTTPS
4. Setup backup otomatis database
5. Monitor error logs
6. Setup cron untuk maintenance

---

**🎉 Aplikasi siap digunakan! Selamat mencoba!**

Untuk pertanyaan atau issue, silakan hubungi developer atau baca dokumentasi lengkap.
