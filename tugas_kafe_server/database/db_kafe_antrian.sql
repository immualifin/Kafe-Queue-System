-- Database: db_kafe_antrian
CREATE DATABASE IF NOT EXISTS db_kafe_antrian;
USE db_kafe_antrian;

-- Tabel 1: Users (Admin/Kasir)
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'kasir') NOT NULL DEFAULT 'kasir',
    session_token VARCHAR(255) NULL,
    session_expiry TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel 2: Kategori Menu
CREATE TABLE kategori_menu (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    icon VARCHAR(100),
    slug VARCHAR(100) NOT NULL UNIQUE
);

-- Tabel 3: Menus (Relasi ke kategori_menu)
CREATE TABLE menus (
    id_menu INT AUTO_INCREMENT PRIMARY KEY,
    id_kategori INT NOT NULL,
    nama_menu VARCHAR(150) NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    gambar VARCHAR(255),
    status_tersedia BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES kategori_menu(id_kategori) ON DELETE CASCADE
);

-- Tabel 4: Pesanan
CREATE TABLE pesanan (
    id_pesanan INT AUTO_INCREMENT PRIMARY KEY,
    nama_pelanggan VARCHAR(100) NOT NULL,
    no_meja VARCHAR(10) NOT NULL,
    total_harga DECIMAL(10,2) NOT NULL,
    status_pesanan ENUM('pending', 'proses', 'siap', 'selesai') NOT NULL DEFAULT 'pending',
    waktu_pesan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    id_user INT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Tabel 5: Detail Pesanan (Relasi ke pesanan dan menus)
CREATE TABLE detail_pesanan (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_pesanan INT NOT NULL,
    id_menu INT NOT NULL,
    jumlah INT NOT NULL,
    harga_satuan DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan) ON DELETE CASCADE,
    FOREIGN KEY (id_menu) REFERENCES menus(id_menu) ON DELETE CASCADE
);

-- Insert Data Awal untuk Users (password di-hash manual untuk kompatibilitas)
INSERT INTO users (username, password, nama_lengkap, role) VALUES
('admin', '$2a$12$tT4.oa.kU4P5WRJCKsKLluJhMz/PnftbIzHYPvJGDhW6R9MCLrTle', 'Administrator', 'admin'),
('kasir1', '$2a$12$M9K0G1ah496UHf/2iDNELeAki4IVvz6ExKzclXOAMEMkFGkLy9IH2', 'Kasir 1', 'kasir');

-- Insert Data Awal untuk Kategori Menu
INSERT INTO kategori_menu (nama_kategori, deskripsi, icon, slug) VALUES
('Makanan Berat', 'Makanan utama dan nasi', '🍛', 'makanan-berat'),
('Minuman', 'Berbagai jenis minuman dingin dan panas', '🥤', 'minuman'),
('Cemilan', 'Makanan ringan untuk teman ngopi', '🍿', 'cemilan'),
('Dessert', 'Pencuci mulut manis', '🍰', 'dessert');

-- Insert Data Awal untuk Menus
INSERT INTO menus (id_kategori, nama_menu, harga, gambar, status_tersedia) VALUES
-- Makanan Berat
(1, 'Nasi Goreng Special', 25000.00, 'nasi_goreng.jpg', TRUE),
(1, 'Mie Ayam Bakso', 22000.00, 'mie_ayam.jpg', TRUE),
(1, 'Ayam Bakar Madu', 35000.00, 'ayam_bakar.jpg', TRUE),
(1, 'Nasi Rames Komplit', 28000.00, 'nasi_rames.jpg', TRUE),
-- Minuman
(2, 'Es Teh Manis', 8000.00, 'es_teh.jpg', TRUE),
(2, 'Kopi Hitam', 10000.00, 'kopi_hitam.jpg', TRUE),
(2, 'Jus Alpukat', 15000.00, 'jus_alpukat.jpg', TRUE),
(2, 'Es Cendol', 12000.00, 'es_cendol.jpg', TRUE),
-- Cemilan
(3, 'Kentang Goreng', 18000.00, 'kentang_goreng.jpg', TRUE),
(3, 'Pisang Goreng', 15000.00, 'pisang_goreng.jpg', TRUE),
(3, 'Roti Bakar', 20000.00, 'roti_bakar.jpg', TRUE),
(3, 'Sosis Bakar', 25000.00, 'sosis_bakar.jpg', TRUE),
-- Dessert
(4, 'Es Krim Vanila', 12000.00, 'es_krim.jpg', TRUE),
(4, 'Pancake Madu', 22000.00, 'pancake.jpg', TRUE),
(4, 'Brownies Coklat', 18000.00, 'brownies.jpg', TRUE),
(4, 'Wafel Coklat', 25000.00, 'wafel.jpg', TRUE);

-- Buat index untuk optimasi
CREATE INDEX idx_pesanan_status ON pesanan(status_pesanan);
CREATE INDEX idx_pesanan_waktu ON pesanan(waktu_pesan);
CREATE INDEX idx_pesanan_user ON pesanan(id_user);
CREATE INDEX idx_detail_pesanan ON detail_pesanan(id_pesanan);
CREATE INDEX idx_menu_kategori ON menus(id_kategori);
CREATE INDEX idx_menu_tersedia ON menus(status_tersedia);

-- ==========================================
-- SETELAH INSTALLATION - UPDATE DATA EXISTING
-- ==========================================

-- Update existing pesanan dengan user default (admin dengan id=1)
UPDATE pesanan SET id_user = 1 WHERE id_user IS NULL;

-- ==========================================
-- FINAL VERIFICATION
-- ==========================================

-- Show successful installation message
SELECT 'Database installation completed successfully!' as message;
SELECT 'Tables created: users, kategori_menu, menus, pesanan, detail_pesanan' as tables_created;
SELECT 'Sample data inserted and updated with cashier tracking enabled' as status;
SELECT 'Ready for cafe application with cashier tracking functionality!' as ready;

-- ==========================================
-- SAMPLE QUERY UNTUK APLIKASI
-- ==========================================

-- Query untuk mengambil pesanan dengan info kasir (ini yang dipakai di API)
SELECT
    p.id_pesanan,
    p.nama_pelanggan,
    p.no_meja,
    p.total_harga,
    p.status_pesanan,
    p.waktu_pesan,
    p.updated_at,
    p.id_user,
    u.nama_lengkap as nama_kasir,
    u.username as username_kasir,
    u.role as role_kasir
FROM pesanan p
LEFT JOIN users u ON p.id_user = u.id_user
WHERE p.status_pesanan IN ('pending', 'proses', 'siap')
ORDER BY
    CASE p.status_pesanan
        WHEN 'pending' THEN 1
        WHEN 'proses' THEN 2
        WHEN 'siap' THEN 3
        WHEN 'selesai' THEN 4
    END,
    p.waktu_pesan ASC;

-- Query untuk statistik per kasir
SELECT
    u.nama_lengkap as nama_kasir,
    u.role,
    COUNT(p.id_pesanan) as total_orders,
    SUM(p.total_harga) as total_revenue,
    FORMAT(SUM(p.total_harga), 0) as total_revenue_formatted
FROM users u
LEFT JOIN pesanan p ON u.id_user = p.id_user
GROUP BY u.id_user, u.nama_lengkap, u.role
ORDER BY total_orders DESC;
