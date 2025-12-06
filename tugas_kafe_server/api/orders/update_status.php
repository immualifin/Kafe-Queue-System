<?php
// include database and object files
require_once '../../config/database.php';

// Check if it's a PUT request
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    jsonResponse(false, "Method tidak diizinkan", null, 405);
}

// Get id_pesanan from URL
$id_pesanan = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id_pesanan) {
    jsonResponse(false, "ID pesanan tidak valid", null, 400);
}

try {
    // Get authenticated user ID
    $id_user = requireAuth(); // This will get the logged-in user's ID

    // Get PUT data
    $data = getPostData();

    // Validate required fields
    validateRequired($data, ['status_pesanan']);

    $status_pesanan = sanitizeInput($data['status_pesanan']);

    // Validate status
    $valid_statuses = ['pending', 'proses', 'siap', 'selesai'];
    if (!in_array($status_pesanan, $valid_statuses)) {
        jsonResponse(false, "Status pesanan tidak valid. Gunakan: pending, proses, siap, atau selesai", null, 400);
    }

    $conn = getConnection();

    // Check if order exists
    $check_sql = "SELECT * FROM pesanan WHERE id_pesanan = :id_pesanan";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(':id_pesanan', $id_pesanan);
    $check_stmt->execute();

    if ($check_stmt->rowCount() === 0) {
        jsonResponse(false, "Pesanan tidak ditemukan", null, 404);
    }

    $current_order = $check_stmt->fetch();

    // Validate status flow
    $status_flow = [
        'pending' => ['proses', 'selesai'],
        'proses' => ['siap', 'selesai'],
        'siap' => ['selesai'],
        'selesai' => [] // Cannot change from completed
    ];

    if (!in_array($status_pesanan, $status_flow[$current_order['status_pesanan']])) {
        jsonResponse(false, "Tidak dapat mengubah status dari '{$current_order['status_pesanan']}' ke '{$status_pesanan}'", null, 400);
    }

    // Update status with user tracking
    $update_sql = "UPDATE pesanan
                  SET status_pesanan = :status_pesanan,
                      id_user = :id_user,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id_pesanan = :id_pesanan";

    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bindParam(':status_pesanan', $status_pesanan);
    $update_stmt->bindParam(':id_user', $id_user);
    $update_stmt->bindParam(':id_pesanan', $id_pesanan);

    if ($update_stmt->execute()) {
        // Get updated order with details and cashier info
        $order_sql = "SELECT
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
                     WHERE p.id_pesanan = :id_pesanan
                     GROUP BY p.id_pesanan";

        $order_stmt = $conn->prepare($order_sql);
        $order_stmt->bindParam(':id_pesanan', $id_pesanan);
        $order_stmt->execute();

        $order = $order_stmt->fetch();

        // Process details
        $items = [];
        if ($order['details']) {
            $details_array = explode('|', $order['details']);
            foreach ($details_array as $detail) {
                list($id_detail, $id_menu, $nama_menu, $jumlah, $harga_satuan) = explode(':', $detail);
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

        $response_data = [
            'id_pesanan' => (int)$order['id_pesanan'],
            'nama_pelanggan' => $order['nama_pelanggan'],
            'no_meja' => $order['no_meja'],
            'total_harga' => (float)$order['total_harga'],
            'total_harga_formatted' => formatRupiah($order['total_harga']),
            'status_pesanan' => $order['status_pesanan'],
            'status_display' => ucwords($order['status_pesanan']),
            'status_color' => getStatusColor($order['status_pesanan']),
            'waktu_pesan' => $order['waktu_pesan'],
            'waktu_pesan_formatted' => date('d M Y H:i', strtotime($order['waktu_pesan'])),
            'updated_at' => $order['updated_at'],
            'updated_at_formatted' => date('d M Y H:i', strtotime($order['updated_at'])),
            'kasir' => $kasir_info,
            'items' => $items
        ];

        jsonResponse(true, "Status pesanan berhasil diupdate", $response_data);
    } else {
        jsonResponse(false, "Gagal mengupdate status pesanan", null, 500);
    }

} catch(PDOException $e) {
    jsonResponse(false, "Error: " . $e->getMessage(), null, 500);
}

function getStatusColor($status) {
    $colors = [
        'pending' => '#ffc107',
        'proses' => '#17a2b8',
        'siap' => '#28a745',
        'selesai' => '#6c757d'
    ];
    return $colors[$status] ?? '#6c757d';
}
?>