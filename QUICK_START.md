# Quick Start Guide - Sistem Pengajuan Transaksi

## Langkah Cepat untuk Menjalankan Aplikasi

### 1. Install Composer Dependencies
```powershell
composer install
```

### 2. Setup Environment
```powershell
# Copy .env.example ke .env
Copy-Item .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Setup Database di .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=transaksi_perusahaan
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Buat Database MySQL
Buka phpMyAdmin atau MySQL CLI, jalankan:
```sql
CREATE DATABASE transaksi_perusahaan;
```

### 5. Jalankan Migration & Seeder
```powershell
# Migrate database
php artisan migrate

# Seed users default
php artisan db:seed

# Create storage link
php artisan storage:link
```

### 6. Jalankan Server
```powershell
php artisan serve
```

Buka browser: **http://localhost:8000**

### 7. Login dengan Akun Default

**Pemohon:**
- Email: `pemohon@perusahaan.com`
- Password: `password`

**Pejabat 1:**
- Email: `pejabat1@perusahaan.com`
- Password: `password`

**Pejabat 2:**
- Email: `pejabat2@perusahaan.com`
- Password: `password`

**Pejabat 3:**
- Email: `pejabat3@perusahaan.com`
- Password: `password`

**Pejabat 4 (Business Executive):**
- Email: `pejabat4@perusahaan.com`
- Password: `password`

## Testing Alur Lengkap

### Skenario: Pengajuan Transaksi Baru

1. **Login sebagai Pemohon**
   - Buat pengajuan transaksi baru
   - Isi semua field yang required
   - Upload lampiran (opsional)
   - Submit transaksi

2. **Login sebagai Pejabat 1**
   - Lihat transaksi yang menunggu approval
   - Review detail transaksi
   - Approve transaksi

3. **Login sebagai Pejabat 2**
   - Lihat transaksi yang sudah disetujui Pejabat 1
   - Lakukan diskusi pra-permohonan
   - Approve untuk lanjut ke pemeriksaan tahap 2
   - Approve lagi untuk lanjut ke Pejabat 3

4. **Login sebagai Pejabat 3**
   - Review transaksi
   - Approve untuk diteruskan ke Pejabat 4

5. **Login sebagai Pejabat 4 (Business Executive)**
   - Review final transaksi
   - Approve sebagai persetujuan akhir

6. **Login kembali sebagai Pejabat 3**
   - Informasikan hasil approval

7. **Login sebagai Pejabat 2**
   - Finalisasi dan selesaikan proses

8. **Login sebagai Pemohon**
   - Lihat transaksi yang sudah disetujui
   - Status: Selesai ✅

## Troubleshooting Cepat

### Masalah: "Class not found"
**Solusi:**
```powershell
composer dump-autoload
```

### Masalah: "SQLSTATE[HY000] [1049] Unknown database"
**Solusi:**
Buat database dulu di MySQL:
```sql
CREATE DATABASE transaksi_perusahaan;
```

### Masalah: "419 Page Expired"
**Solusi:**
Clear cache:
```powershell
php artisan cache:clear
php artisan config:clear
```

### Masalah: File upload error
**Solusi:**
```powershell
php artisan storage:link
```

Pastikan folder `storage/app/public` ada dan writable.

### Masalah: CSS/JS tidak load
**Solusi:**
Pastikan file ada di:
- `public/css/custom.css`
- `public/js/app-custom.js`
- `public/js/transactions-*.js`

## Struktur Menu Berdasarkan Role

### Pemohon
- Dashboard
- Buat Pengajuan ➕
- Daftar Transaksi
- Logout

### Pejabat 1-4
- Dashboard
- Daftar Transaksi
- Perlu Persetujuan ⏰
- Logout

## Tips Penggunaan

1. **Simpan sebagai Draft** dulu sebelum submit agar bisa diedit
2. **Upload lampiran** untuk dokumentasi yang lebih lengkap
3. **Gunakan filter** di DataTable untuk cari transaksi tertentu
4. **Lihat Timeline** untuk tracking progress approval
5. **Baca ketentuan** pengajuan di form create

## Fitur Unggulan

✨ **AJAX Realtime** - Semua aksi tanpa reload halaman
✨ **Responsive Design** - Bisa diakses dari HP/Tablet
✨ **Timeline Approval** - Track siapa saja yang sudah approve
✨ **Upload Dokumen** - Support PDF, DOC, XLS, Image
✨ **Status Badge** - Visual indikator status transaksi
✨ **Auto Generate Nomor** - Nomor transaksi otomatis
✨ **Money Format** - Input rupiah otomatis terformat

## Nomor Kontak Support

Jika ada kendala, hubungi:
- IT Support: support@perusahaan.com
- Developer: developer@perusahaan.com

---

**Selamat menggunakan Sistem Pengajuan Transaksi Resmi Perusahaan!** 🎉
