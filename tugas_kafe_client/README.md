# Tugas Kafe - Client

## Deskripsi
Frontend aplikasi Warung Kafe untuk pelanggan dan administrator.

## Struktur Folder
```
tugas_kafe_client/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       ├── script_public.js
│       └── script_admin.js
├── config.js
├── index.php          - Halaman utama untuk pelanggan
├── login.php          - Login admin/kasir
├── dashboard.php      - Dashboard admin
└── display_antrian.php - Tampilan antrian
```

## Fitur

### Pelanggan
- View menu dan kategori
- Tambah menu ke keranjang
- Buat pesanan
- Lihat status antrian

### Administrator
- Login dashboard
- Kelola menu (CRUD)
- Lihat dan kelola pesanan
- Update status pesanan

## Konfigurasi

Edit `config.js` untuk mengatur koneksi ke server:
```javascript
const API_CONFIG = {
    SERVER_URL: 'http://IP_SERVER/tugas_kafe_server',
    // ... konfigurasi lainnya
};
```

## Cara Menjalankan
1. Pastikan server API sudah running
2. Konfigurasi koneksi server di `config.js`
3. Deploy ke web server atau akses langsung
4. Akses `index.php` untuk pelanggan atau `login.php` untuk admin

## Requirements
- Web browser modern dengan JavaScript enabled
- Koneksi ke server API
- Bootstrap 5.3.0 (dari CDN)

## Cara Setup
1. Copy folder ini ke device client
2. Edit `config.js` sesuaikan SERVER_URL dengan IP/domain server
3. Deploy ke web server (XAMPP, Apache, Nginx, dll)
4. Akses aplikasi melalui browser

## Note
- Folder ini berisi pure frontend (PHP hanya untuk templating)
- Semua operasi data dilakukan via API call ke server
- Gambar menu di-load dari server API