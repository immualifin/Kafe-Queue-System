-- ==========================================
-- VERIFICATION SCRIPT - RUN AFTER INSTALLATION
-- ==========================================
-- Usage: mysql -u root -p db_kafe_antrian < verify_database.sql

-- Check database exists
SELECT DATABASE() as current_database;

-- List all tables
SHOW TABLES;

-- ==========================================
-- TABLE STRUCTURES
-- ==========================================

-- Check users table structure
DESCRIBE users;

-- Check pesanan table structure (includes cashier tracking)
DESCRIBE pesanan;

-- Check foreign key constraints
SELECT
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE
    TABLE_SCHEMA = DATABASE() AND
    REFERENCED_TABLE_NAME IS NOT NULL;

-- ==========================================
-- SAMPLE DATA VERIFICATION
-- ==========================================

-- Check users data
SELECT 'Users data:' as info;
SELECT id_user, username, nama_lengkap, role, created_at FROM users;

-- Check pesanan with cashier info
SELECT 'Pesanan with cashier tracking:' as info;
SELECT
    p.id_pesanan,
    p.nama_pelanggan,
    p.no_meja,
    p.total_harga,
    p.status_pesanan,
    p.waktu_pesan,
    p.id_user,
    COALESCE(u.nama_lengkap, 'Tidak ada kasir') as nama_kasir,
    COALESCE(u.username, 'N/A') as username_kasir,
    COALESCE(u.role, 'N/A') as role_kasir
FROM pesanan p
LEFT JOIN users u ON p.id_user = u.id_user
ORDER BY p.waktu_pesan DESC;

-- ==========================================
-- STATISTICS
-- ==========================================

-- Order statistics
SELECT 'Order statistics:' as info;
SELECT
    COUNT(*) as total_orders,
    SUM(CASE WHEN id_user IS NOT NULL THEN 1 ELSE 0 END) as orders_with_cashier,
    SUM(CASE WHEN id_user IS NULL THEN 1 ELSE 0 END) as orders_without_cashier,
    COUNT(DISTINCT id_user) as unique_cashiers,
    FORMAT(SUM(total_harga), 0) as total_revenue
FROM pesanan;

-- Performance per cashier
SELECT 'Performance per cashier:' as info;
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

-- Status distribution
SELECT 'Order status distribution:' as info;
SELECT
    status_pesanan,
    COUNT(*) as total_orders,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM pesanan), 2) as percentage
FROM pesanan
GROUP BY status_pesanan
ORDER BY
    CASE status_pesanan
        WHEN 'pending' THEN 1
        WHEN 'proses' THEN 2
        WHEN 'siap' THEN 3
        WHEN 'selesai' THEN 4
    END;

-- ==========================================
-- INDEX VERIFICATION
-- ==========================================

-- Check indexes on all tables
SELECT 'Index information:' as info;
SHOW INDEX FROM pesanan;
SHOW INDEX FROM users;
SHOW INDEX FROM menus;
SHOW INDEX FROM detail_pesanan;

-- ==========================================
-- API TESTING QUERIES
-- ==========================================

-- Query similar to API (orders with cashier info)
SELECT 'API test query - orders with cashier info:' as info;
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
    u.role as role_kasir,
    GROUP_CONCAT(
        CONCAT(dp.id_detail, ':', dp.id_menu, ':', m.nama_menu, ':', dp.jumlah, ':', dp.harga_satuan)
        ORDER BY dp.id_detail
        SEPARATOR '|'
    ) as details
FROM pesanan p
LEFT JOIN users u ON p.id_user = u.id_user
LEFT JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
LEFT JOIN menus m ON dp.id_menu = m.id_menu
WHERE p.status_pesanan IN ('pending', 'proses', 'siap')
GROUP BY p.id_pesanan
ORDER BY
    CASE p.status_pesanan
        WHEN 'pending' THEN 1
        WHEN 'proses' THEN 2
        WHEN 'siap' THEN 3
        WHEN 'selesai' THEN 4
    END,
    p.waktu_pesan ASC
LIMIT 5;

-- ==========================================
-- SUCCESS VERIFICATION
-- ==========================================

-- Final check - cashier tracking functionality
SELECT 'Cashier tracking verification:' as info;
SELECT
    CASE
        WHEN COUNT(DISTINCT p.id_user) > 0 THEN '✅ Cashier tracking is working'
        ELSE '❌ No cashier tracking data found'
    END as cashier_status
FROM pesanan p
WHERE p.id_user IS NOT NULL;

SELECT 'Verification completed!' as final_status;