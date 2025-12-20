# API Endpoints Documentation

## Authentication

### Login
- **URL:** `/login`
- **Method:** `POST`
- **Auth:** No
- **Body:**
  ```json
  {
    "email": "pemohon@perusahaan.com",
    "password": "password",
    "remember": true
  }
  ```
- **Success Response:** Redirect to `/dashboard`

### Logout
- **URL:** `/logout`
- **Method:** `POST`
- **Auth:** Required
- **Success Response:** Redirect to `/login`

## Dashboard

### Get Dashboard
- **URL:** `/dashboard`
- **Method:** `GET`
- **Auth:** Required
- **Success Response:**
  ```
  HTML view with statistics and recent transactions
  ```

## Transactions

### Get All Transactions (DataTable)
- **URL:** `/transactions/get-data`
- **Method:** `POST`
- **Auth:** Required
- **Body:**
  ```json
  {
    "draw": 1,
    "start": 0,
    "length": 10,
    "search": {
      "value": ""
    },
    "order": [
      {
        "column": 0,
        "dir": "desc"
      }
    ]
  }
  ```
- **Success Response:**
  ```json
  {
    "draw": 1,
    "recordsTotal": 100,
    "recordsFiltered": 100,
    "data": [
      {
        "id": 1,
        "nomor_transaksi": "01.12/2025",
        "nama_pemohon": "John Doe",
        "nama_perusahaan": "PT ABC",
        "tanggal_pengajuan": "01/12/2025",
        "total": "Rp 10.000.000",
        "status": "draft",
        "status_label": "<span class='badge bg-secondary'>Draft</span>",
        "can_approve": false
      }
    ]
  }
  ```

### Get Transaction List (View)
- **URL:** `/transactions`
- **Method:** `GET`
- **Auth:** Required
- **Success Response:** HTML view with DataTable

### Get Transaction Detail
- **URL:** `/transactions/{id}`
- **Method:** `GET`
- **Auth:** Required
- **Success Response:** HTML view with transaction details

### Get Transaction Detail (AJAX)
- **URL:** `/transactions/{id}/detail`
- **Method:** `GET`
- **Auth:** Required
- **Success Response:**
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "nomor_transaksi": "01.12/2025",
      "nama_pemohon": "John Doe",
      "nama_perusahaan": "PT ABC",
      "tanggal_pengajuan": "2025-12-01",
      "uraian_transaksi": "Pembayaran vendor",
      "total": 10000000,
      "status": "draft",
      "approvals": []
    }
  }
  ```

### Create Transaction Form
- **URL:** `/transactions/create`
- **Method:** `GET`
- **Auth:** Required (Pemohon only)
- **Success Response:** HTML form

### Store Transaction
- **URL:** `/transactions`
- **Method:** `POST`
- **Auth:** Required (Pemohon only)
- **Body (multipart/form-data):**
  ```json
  {
    "nama_pemohon": "John Doe",
    "nama_perusahaan": "PT ABC",
    "tanggal_pengajuan": "2025-12-01",
    "uraian_transaksi": "Pembayaran vendor",
    "total": "10000000",
    "dasar_transaksi": "Invoice #123",
    "lawan_transaksi": "PT XYZ",
    "rekening_transaksi": "1234567890",
    "rencana_tanggal_transaksi": "2025-12-15",
    "pengakuan_transaksi": "Beban Operasional",
    "keterangan": "Urgent",
    "lampiran_dokumen": "file.pdf"
  }
  ```
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Transaksi berhasil dibuat",
    "data": {
      "id": 1,
      "nomor_transaksi": "01.12/2025"
    }
  }
  ```
- **Error Response:**
  ```json
  {
    "success": false,
    "message": "Validasi gagal",
    "errors": {
      "nama_pemohon": ["Nama pemohon wajib diisi"],
      "total": ["Total harus berupa angka"]
    }
  }
  ```

### Edit Transaction Form
- **URL:** `/transactions/{id}/edit`
- **Method:** `GET`
- **Auth:** Required (Owner & Draft only)
- **Success Response:** HTML form with existing data

### Update Transaction
- **URL:** `/transactions/{id}`
- **Method:** `PUT/PATCH`
- **Auth:** Required (Owner & Draft only)
- **Body:** Same as Store Transaction
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Transaksi berhasil diperbarui",
    "data": {
      "id": 1,
      "nomor_transaksi": "01.12/2025"
    }
  }
  ```

### Delete Transaction
- **URL:** `/transactions/{id}`
- **Method:** `DELETE`
- **Auth:** Required (Owner & Draft only)
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Transaksi berhasil dihapus"
  }
  ```

### Submit Transaction for Approval
- **URL:** `/transactions/{id}/submit`
- **Method:** `POST`
- **Auth:** Required (Owner & Draft only)
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Transaksi berhasil diajukan untuk persetujuan Pejabat 1"
  }
  ```

### Approve Transaction
- **URL:** `/transactions/{id}/approve`
- **Method:** `POST`
- **Auth:** Required (Pejabat only)
- **Body:**
  ```json
  {
    "catatan": "Approved"
  }
  ```
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Transaksi berhasil disetujui"
  }
  ```
- **Error Response:**
  ```json
  {
    "success": false,
    "message": "Transaksi tidak dalam tahap persetujuan Anda"
  }
  ```

### Reject Transaction
- **URL:** `/transactions/{id}/reject`
- **Method:** `POST`
- **Auth:** Required (Pejabat only)
- **Body:**
  ```json
  {
    "alasan_penolakan": "Data tidak lengkap"
  }
  ```
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Transaksi berhasil ditolak"
  }
  ```

### Request Completion
- **URL:** `/transactions/{id}/request-completion`
- **Method:** `POST`
- **Auth:** Required (Pejabat 2 & 3 only)
- **Body:**
  ```json
  {
    "catatan": "Mohon lengkapi dokumen A dan B"
  }
  ```
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Permintaan kelengkapan berhasil dikirim"
  }
  ```

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "Anda tidak memiliki akses untuk melakukan aksi ini."
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Data tidak ditemukan"
}
```

### 422 Validation Error
```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "Terjadi kesalahan server"
}
```

## Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Request successful |
| 201 | Created - Resource created |
| 400 | Bad Request - Invalid request |
| 401 | Unauthorized - Not authenticated |
| 403 | Forbidden - No permission |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation error |
| 500 | Internal Server Error |

## Transaction Status Flow

```
draft 
  → menunggu_pejabat_1 (Submit)
  → diskusi_pra_permohonan (Pejabat 1 Approve)
  → menunggu_pejabat_2 (Pejabat 2 Approve)
  → pemeriksaan_tahap_2 (Pejabat 2 Approve)
  → menunggu_pejabat_3 (Pejabat 2 Approve)
  → menunggu_pejabat_4 (Pejabat 3 Approve)
  → disetujui_pejabat_4 (Pejabat 4 Approve)
  → diinformasikan (Pejabat 3 Approve)
  → selesai (Pejabat 2 Approve)
```

Alternative flows:
- Any step → `ditolak` (Pejabat Reject)
- Pejabat 2/3 → `dilengkapi` (Request Completion)

## File Upload Specifications

### Accepted File Types
- PDF: application/pdf
- Word: .doc, .docx
- Excel: .xls, .xlsx
- Images: .jpg, .jpeg, .png

### Max File Size
5 MB (5242880 bytes)

### Upload Path
`storage/app/public/transactions/`

### Public Access
`http://localhost:8000/storage/transactions/{filename}`
