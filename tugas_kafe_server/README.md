# Tugas Kafe - Server

## Deskripsi
Backend API untuk aplikasi Warung Kafe.

## Struktur Folder
```
tugas_kafe_server/
├── api/
│   ├── auth/
│   │   ├── login.php
│   │   └── check_session.php
│   ├── menus/
│   │   ├── create.php
│   │   ├── read.php
│   │   ├── update.php
│   │   └── delete.php
│   └── orders/
│       ├── create.php
│       ├── read.php
│       └── update_status.php
├── config/
│   └── database.php
├── uploads/
│   └── (gambar-gambar menu)
└── database/
    └── db_kafe_antrian.sql
```

## API Endpoints

### Authentication
- `POST /api/auth/login.php` - Login admin/kasir
- `GET /api/auth/check_session.php` - Validasi session

### Menus
- `GET /api/menus/read.php` - Ambil semua menu
- `POST /api/menus/create.php` - Tambah menu baru
- `PUT /api/menus/update.php` - Update menu
- `DELETE /api/menus/delete.php` - Hapus menu

### Orders
- `GET /api/orders/read.php` - Ambil semua pesanan
- `POST /api/orders/create.php` - Buat pesanan baru
- `PUT /api/orders/update_status.php` - Update status pesanan

## Database Setup
1. Import file SQL: `database/db_kafe_antrian.sql`
2. Konfigurasi koneksi database di `config/database.php`

## Konfigurasi
Edit `config/database.php` untuk mengatur:
- Host database
- Nama database
- Username/password
- CORS settings

## Cara Menjalankan
1. Pastikan web server (Apache/Nginx) dan PHP terinstall
2. Konfigurasi virtual host untuk folder ini
3. Import database
4. Akses API endpoint sesuai kebutuhan

## Security
- CORS sudah dikonfigurasi
- Input validation dan sanitization
- Session management
- Upload file validation