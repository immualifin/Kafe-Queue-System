# Database Cafe Antrian - Setup Instructions

## File Database
- **File Utama**: `db_kafe_antrian.sql` (lengkap dengan semua fitur cashier tracking)

## Cara Install Database

### 1. Via Terminal/MySQL Command Line
```bash
# Install database
mysql -u root -p < db_kafe_antrian.sql

# Verify installation (optional)
mysql -u root -p db_kafe_antrian < verify_database.sql
```

### 2. Via phpMyAdmin
1. Buka phpMyAdmin
2. Klik tab "Import"
3. Pilih file `db_kafe_antrian.sql`
4. Klik "Go"
5. (Optional) Import `verify_database.sql` untuk verifikasi

## Apa Saja yang Termasuk?

### ✅ Struktur Database Lengkap:
- **Tabel users**: Admin dan Kasir dengan authentication
- **Tabel kategori_menu**: Kategori makanan/minuman
- **Tabel menus**: Menu items dengan relasi ke kategori
- **Tabel pesanan**: Data pesanan dengan **cashier tracking**
- **Tabel detail_pesanan**: Detail item per pesanan

### ✅ Foreign Key & Index:
- `pesanan.id_user` → `users.id_user` (ON DELETE SET NULL)
- Index untuk optimasi performa query

### ✅ Sample Data:
- 2 users: admin dan kasir1
- 4 kategori menu
- 16 menu items
- Update existing orders dengan admin user

### ✅ Verifikasi & Testing:
- Query untuk cek struktur dan foreign key
- Sample query yang dipakai di API
- Statistik tracking per kasir

## Fitur Cashier Tracking

### 🎯 Cara Kerja:
1. **Order Creation**: ID kasir otomatis tersimpan
2. **Status Update**: ID kasir yang update status juga tersimpan
3. **Frontend Display**: Nama kasir muncul di admin panel

### 📊 Query Contoh:

**Ambil semua pesanan dengan info kasir:**
```sql
SELECT
    p.id_pesanan,
    p.nama_pelanggan,
    p.total_harga,
    p.status_pesanan,
    u.nama_lengkap as nama_kasir,
    u.role
FROM pesanan p
LEFT JOIN users u ON p.id_user = u.id_user
ORDER BY p.waktu_pesan DESC;
```

**Statistik per kasir:**
```sql
SELECT
    u.nama_lengkap as nama_kasir,
    u.role,
    COUNT(p.id_pesanan) as total_orders,
    SUM(p.total_harga) as total_revenue
FROM users u
LEFT JOIN pesanan p ON u.id_user = p.id_user
GROUP BY u.id_user, u.nama_lengkap, u.role
ORDER BY total_orders DESC;
```

## User Accounts Default

| Username | Password | Role | Nama Lengkap |
|----------|----------|------|--------------|
| admin | admin123 | admin | Administrator |
| kasir1 | kasir123 | kasir | Kasir 1 |

*Note: Password sudah di-hash dengan bcrypt*

## Setelah Install

1. **Test login** di: `http://localhost/tugas_kafe_client/login.php`
2. **Buat pesanan baru** untuk test cashier tracking
3. **Check admin panel** untuk melihat nama kasir di setiap pesanan

## Troubleshooting

### Jika error "Duplicate column name id_user":
- Database sudah ada dan column `id_user` sudah ada
- Tidak perlu install ulang, cukup update aplikasinya

### Jika ingin reset database:
```sql
DROP DATABASE IF EXISTS db_kafe_antrian;
```
Kemudian install ulang dengan file `db_kafe_antrian.sql`

---

## 🎉 Selamat! Sistem cashier tracking siap digunakan!

Sekarang setiap transaksi bisa dilacak ke staff yang bertanggung jawab. ✨