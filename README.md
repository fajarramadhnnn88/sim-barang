# SIM Barang Support v2

Sistem Informasi Manajemen Data Barang Support — Laravel 11, UI Modern, Kelola Divisi.

## Cara Install

```bash
# 1. Buat project Laravel
cd C:\xampp\htdocs
composer create-project laravel/laravel sim-barang
cd sim-barang

# 2. Extract ZIP ini, copy semua file ke sim-barang/, Replace All

# 3. Setup environment
copy .env.example .env
php artisan key:generate

# 4. Buat database sim_barang di phpMyAdmin

# 5. Edit .env → DB_DATABASE=sim_barang, DB_USERNAME=root, DB_PASSWORD=

# 6. Jalankan migration & seeder
php artisan migrate:fresh --seed
php artisan storage:link

# 7. Jalankan server
php artisan serve
```

Buka: http://localhost:8000

## Akun Default (password: password)

| Email | Role |
|-------|------|
| superadmin@simbarang.id | Super Admin |
| direktur@simbarang.id | Direktur |
| wadir@simbarang.id | Wakil Direktur |
| purchasing@simbarang.id | Purchasing |
| admin.ti@simbarang.id | Admin Divisi |
| staff.ti@simbarang.id | Staff |

## Fitur
- Dashboard dengan grafik & alert stok
- Data Barang (CRUD + foto)
- Barang Masuk & Keluar (auto-update stok)
- Stock Balance real-time
- Pengajuan dengan alur approval bertingkat (≤10jt→Wadir, >10jt→Direktur)
- Timeline approval + audit log
- Laporan + cetak
- Kelola Divisi/Grup (BARU)
- Kelola Pengguna & Role
- UI Modern dengan font Inter
