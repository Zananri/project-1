# Sistem Pengajuan Transaksi Resmi Perusahaan

Aplikasi web untuk mengelola pengajuan transaksi resmi perusahaan dengan sistem approval bertingkat.

## Fitur Utama

- ✅ Form pengajuan transaksi lengkap sesuai ketentuan perusahaan
- ✅ Sistem approval bertingkat (Pejabat 1 → Pejabat 2 → Pejabat 3 → Pejabat 4)
- ✅ Dashboard statistik dan monitoring transaksi
- ✅ CRUD transaksi dengan jQuery AJAX
- ✅ Upload dokumen lampiran
- ✅ Timeline tracking persetujuan
- ✅ Responsive design dengan Bootstrap 5
- ✅ Role-based access control (Pemohon & Pejabat 1-4)

## Teknologi yang Digunakan

- **Backend:** Laravel 12
- **Frontend:** Bootstrap 5, jQuery
- **Database:** MySQL
- **UI Components:** Bootstrap Icons, DataTables, Select2, SweetAlert2

## Persyaratan Sistem

- PHP >= 8.2
- Composer
- MySQL/MariaDB
- XAMPP/WAMP/LAMP (untuk development)

## Instalasi

### 1. Clone atau Extract Project

Pastikan project berada di folder `c:\xampp\htdocs\nama-aplikasi`

### 2. Install Dependencies

Buka terminal/PowerShell di folder project, jalankan:

```powershell
composer install
```

### 3. Konfigurasi Environment

Copy file `.env.example` menjadi `.env`:

```powershell
Copy-Item .env.example .env
```

Generate application key:

```powershell
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Buat Database

Buat database baru di MySQL:

```sql
CREATE DATABASE nama_database_anda;
```

### 6. Jalankan Migration dan Seeder

Jalankan migration untuk membuat tabel:

```powershell
php artisan migrate
```

Jalankan seeder untuk data dummy users:

```powershell
php artisan db:seed
```

### 7. Create Storage Link

Untuk upload file:

```powershell
php artisan storage:link
```

### 8. Jalankan Aplikasi

```powershell
php artisan serve
```

Buka browser dan akses: `http://localhost:8000`

## Akun Default

Setelah menjalankan seeder, Anda dapat login dengan akun berikut:

| Role | Email | Password |
|------|-------|----------|
| Pemohon | pemohon@perusahaan.com | password |
| Pejabat 1 | pejabat1@perusahaan.com | password |
| Pejabat 2 | pejabat2@perusahaan.com | password |
| Pejabat 3 | pejabat3@perusahaan.com | password |
| Pejabat 4 | pejabat4@perusahaan.com | password |

## Alur Pengajuan Transaksi

### Flowchart Proses Approval

1. **Permohonan** - Pemohon mengisi dan submit form
2. **Pemeriksaan Tahap 1** - Pejabat 1 mereview dan approve/reject
3. **Diskusi Pra-Permohonan** - Pejabat 2 melakukan diskusi
4. **Pemeriksaan Tahap 2** - Pejabat 2 mereview detail
5. **Dilengkapi** - Jika perlu kelengkapan data
6. **Disetujui** - Pejabat 3 dan Pejabat 4 approve
7. **Diinformasikan** - Hasil dikembalikan ke Pemohon
8. **Permohonan Dapat Dieksekusi** - Selesai

### Status Transaksi

- **Draft** - Transaksi belum diajukan
- **Menunggu Pejabat 1-4** - Menunggu persetujuan
- **Diskusi Pra-Permohonan** - Dalam tahap diskusi
- **Pemeriksaan Tahap 2** - Dalam pemeriksaan lanjutan
- **Dilengkapi** - Membutuhkan kelengkapan data
- **Disetujui Pejabat 4** - Sudah disetujui final
- **Diinformasikan** - Informasi sudah disampaikan
- **Selesai** - Proses approval selesai
- **Ditolak** - Transaksi ditolak
- **Ajukan Ulang** - Perlu pengajuan ulang

## Struktur File Penting

### Backend

- `app/Models/Transaction.php` - Model Transaksi
- `app/Models/TransactionApproval.php` - Model Approval
- `app/Http/Controllers/TransactionController.php` - Controller utama
- `app/Http/Controllers/DashboardController.php` - Controller dashboard
- `database/migrations/` - File migration database

### Frontend

- `resources/views/layouts/app.blade.php` - Layout utama
- `resources/views/dashboard.blade.php` - Halaman dashboard
- `resources/views/transactions/` - Halaman transaksi (index, create, show, edit)
- `public/css/custom.css` - Custom CSS
- `public/js/` - File JavaScript AJAX

### Routes

- `routes/web.php` - Web routes
- `routes/auth.php` - Authentication routes

## Cara Penggunaan

### Untuk Pemohon

1. Login dengan akun pemohon
2. Klik "Buat Pengajuan Baru" di dashboard atau sidebar
3. Isi semua field yang diperlukan sesuai ketentuan
4. Upload lampiran dokumen (opsional)
5. Klik "Simpan sebagai Draft" untuk menyimpan atau "Simpan dan Ajukan" untuk langsung submit
6. Pantau status transaksi di "Daftar Transaksi"

### Untuk Pejabat

1. Login dengan akun pejabat
2. Lihat daftar transaksi yang perlu approval
3. Klik "Detail" pada transaksi yang ingin direview
4. Review informasi transaksi dan timeline approval
5. Pilih aksi:
   - **Setujui** - Menyetujui transaksi
   - **Tolak** - Menolak transaksi dengan alasan
   - **Minta Kelengkapan** - Meminta data tambahan (hanya Pejabat 2)

## Field Form Pengajuan

1. **Nama Pemohon** - Nama pengaju transaksi
2. **Nama Perusahaan** - Nama perusahaan pengaju
3. **Tanggal Pengajuan** - Tanggal submit form
4. **Uraian Transaksi** - Deskripsi lengkap transaksi
5. **Total** - Nominal transaksi dalam rupiah
6. **Dasar Transaksi** - Dokumen/dasar transaksi
7. **Lawan Transaksi** - Pihak yang bertransaksi
8. **Rekening Transaksi** - Nomor rekening tujuan
9. **Rencana Tanggal Transaksi** - Tanggal eksekusi
10. **Pengakuan Transaksi** - Akun pembiayaan
11. **Keterangan** - Informasi tambahan
12. **Lampiran Dokumen** - File pendukung (PDF, DOC, XLS, JPG, PNG)

## Ketentuan Pengajuan

1. Formulir Pengajuan diajukan setelah memperoleh persetujuan dari Pejabat 1
2. Formulir Pengajuan diajukan setiap hari **Senin dan Kamis sebelum pukul 12.00 WIB**
3. Pengajuan setelah batas waktu akan diproses pada hari kerja berikutnya
4. Setiap pengajuan wajib menggunakan Formulir resmi dengan informasi lengkap
5. Pengaju bertanggung jawab atas kebenaran dan ketepatan data

## Troubleshooting

### Error saat migrate

Pastikan database sudah dibuat dan konfigurasi di `.env` benar.

### Error 500 saat upload file

Pastikan sudah menjalankan `php artisan storage:link`

### DataTable tidak muncul

Cek apakah semua file JS sudah terload dengan benar di browser console.

### Login tidak bisa

Pastikan sudah menjalankan `php artisan db:seed` untuk membuat user default.

## Fitur AJAX

Semua operasi CRUD menggunakan jQuery AJAX untuk pengalaman yang smooth tanpa reload halaman:

- ✅ Create transaksi
- ✅ Read/Display transaksi (DataTables)
- ✅ Update transaksi
- ✅ Delete transaksi
- ✅ Approve/Reject transaksi
- ✅ Submit transaksi

## Browser Support

- Chrome (recommended)
- Firefox
- Edge
- Safari

## Developer

Sistem ini dibuat dengan teknologi modern dan best practices Laravel untuk memastikan:
- Security (CSRF Protection, Authentication)
- Performance (Eager Loading, Caching)
- Maintainability (MVC Pattern, Clean Code)
- User Experience (AJAX, Responsive Design)

## License

Proprietary - Internal Company Use Only

---

**Catatan:** Untuk production, pastikan untuk:
1. Ubah `APP_ENV=production` di `.env`
2. Ubah `APP_DEBUG=false` di `.env`
3. Set proper `APP_URL` di `.env`
4. Gunakan password yang kuat untuk semua akun
5. Aktifkan HTTPS
6. Backup database secara berkala
