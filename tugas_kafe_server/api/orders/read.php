<?php
// include database and object files
require_once '../../config/database.php';

// Debug logging untuk CORS troubleshooting
error_log("API read.php called. Origin: " . ($_SERVER['HTTP_ORIGIN'] ?? 'not set'));
error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
error_log("Request URI: " . $_SERVER['REQUEST_URI']);
error_log("GET params: " . json_encode($_GET));

try {
    $conn = getConnection();

    // Get parameters
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    $status = isset($_GET['status']) ? sanitizeInput($_GET['status']) : null;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $date = isset($_GET['date']) ? sanitizeInput($_GET['date']) : null;

    // Build WHERE clause
    $where_conditions = [];
    $params = [];

    if ($id) {
        $where_conditions[] = "p.id_pesanan = :id";
        $params[':id'] = $id;
    }

    if ($status && in_array($status, ['pending', 'proses', 'siap', 'selesai'])) {
        $where_conditions[] = "p.status_pesanan = :status";
        $params[':status'] = $status;
    }

    if ($date) {
        $where_conditions[] = "DATE(p.waktu_pesan) = :date";
        $params[':date'] = $date;
    }

    $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

    // If specific ID requested, adjust limit and pagination
    if ($id) {
        $limit = 1;
        $offset = 0;
    }

    // Get orders with details and cashier info
    $sql = "SELECT
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
            LEFT JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
            LEFT JOIN menus m ON dp.id_menu = m.id_menu
            LEFT JOIN users u ON p.id_user = u.id_user
            $where_clause
            GROUP BY p.id_pesanan
            ORDER BY
                CASE p.status_pesanan
                    WHEN 'pending' THEN 1
                    WHEN 'proses' THEN 2
                    WHEN 'siap' THEN 3
                    WHEN 'selesai' THEN 4
                END,
                p.waktu_pesan ASC
            LIMIT :limit OFFSET :offset";

    $stmt = $conn->prepare($sql);

    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $orders = $stmt->fetchAll();

    // Process orders to include items
    $processed_orders = [];
    foreach ($orders as $order) {
        $items = [];
        if ($order['details']) {
            $details_array = explode('|', $order['details']);
            foreach ($details_array as $detail) {
                $detail_parts = explode(':', $detail);
                if (count($detail_parts) >= 5) {
                    list($id_detail, $id_menu, $nama_menu, $jumlah, $harga_satuan) = $detail_parts;
                    $items[] = [
                        'id_detail' => (int)$id_detail,
                        'id_menu' => (int)$id_menu,
                        'nama_menu' => $nama_menu,
                        'jumlah' => (int)$jumlah,
                        'harga_satuan' => (float)$harga_satuan,
                        'subtotal' => (float)$harga_satuan * (int)$jumlah,
                        'harga_satuan_formatted' => formatRupiah($harga_satuan),
                        'subtotal_formatted' => formatRupiah($harga_satuan * (int)$jumlah)
                    ];
                }
            }
        }

        // Prepare cashier information
        $kasir_info = null;
        if ($order['id_user']) {
            $kasir_info = [
                'id_user' => (int)$order['id_user'],
                'nama_lengkap' => $order['nama_kasir'],
                'username' => $order['username_kasir'],
                'role' => $order['role_kasir'],
                'display_name' => $order['nama_kasir'] ?? $order['username_kasir']
            ];
        }

        $processed_orders[] = [
            'id_pesanan' => (int)$order['id_pesanan'],
            'nama_pelanggan' => $order['nama_pelanggan'],
            'no_meja' => $order['no_meja'],
            'total_harga' => (float)$order['total_harga'],
            'total_harga_formatted' => formatRupiah($order['total_harga']),
            'status_pesanan' => $order['status_pesanan'],
            'status_display' => ucwords($order['status_pesanan']),
            'waktu_pesan' => $order['waktu_pesan'],
            'waktu_pesan_formatted' => date('d M Y H:i', strtotime($order['waktu_pesan'])),
            'updated_at' => $order['updated_at'],
            'updated_at_formatted' => date('d M Y H:i', strtotime($order['updated_at'])),
            'kasir' => $kasir_info,
            'items' => $items
        ];
    }

    // Get statistics
    $stats_sql = "SELECT
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status_pesanan = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status_pesanan = 'proses' THEN 1 ELSE 0 END) as proses,
                    SUM(CASE WHEN status_pesanan = 'siap' THEN 1 ELSE 0 END) as siap,
                    SUM(CASE WHEN status_pesanan = 'selesai' THEN 1 ELSE 0 END) as selesai,
                    SUM(total_harga) as total_revenue
                  FROM pesanan";

    $stats_stmt = $conn->prepare($stats_sql);
    $stats_stmt->execute();
    $stats = $stats_stmt->fetch();

    $statistics = [
        'total_orders' => (int)$stats['total_orders'],
        'pending' => (int)$stats['pending'],
        'proses' => (int)$stats['proses'],
        'siap' => (int)$stats['siap'],
        'selesai' => (int)$stats['selesai'],
        'total_revenue' => (float)$stats['total_revenue'],
        'total_revenue_formatted' => formatRupiah($stats['total_revenue'])
    ];

    $response_data = [
        'orders' => $processed_orders,
        'statistics' => $statistics,
        'pagination' => [
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => count($processed_orders) === $limit
        ]
    ];

    // Add debug info for single order requests
    if ($id) {
        if (count($processed_orders) === 0) {
            jsonResponse(false, "Pesanan dengan ID $id tidak ditemukan", null, 404);
        }
        $response_data['debug'] = [
            'requested_id' => $id,
            'orders_found' => count($processed_orders)
        ];
    }

    jsonResponse(true, "Data pesanan berhasil diambil", $response_data);

} catch(PDOException $e) {
    jsonResponse(false, "Error: " . $e->getMessage(), null, 500);
} catch(Exception $e) {
    jsonResponse(false, "General Error: " . $e->getMessage(), null, 500);
}
?>