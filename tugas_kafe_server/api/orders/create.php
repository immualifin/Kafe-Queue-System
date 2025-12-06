<?php
// include database and object files
require_once '../../config/database.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, "Method tidak diizinkan", null, 405);
}

try {
    // Get authenticated user ID
    $id_user = requireAuth(); // This will get the logged-in user's ID

    // Get POST data
    $data = getPostData();

    // Validate required fields
    validateRequired($data, ['nama_pelanggan', 'no_meja', 'items']);

    // Sanitize input
    $nama_pelanggan = sanitizeInput($data['nama_pelanggan']);
    $no_meja = sanitizeInput($data['no_meja']);
    $items = $data['items'];

    // Validate items array
    if (!is_array($items) || empty($items)) {
        jsonResponse(false, "Items harus berupa array dan tidak boleh kosong", null, 400);
    }

    $conn = getConnection();

    // Start transaction
    $conn->beginTransaction();

    try {
        // Calculate total harga and validate items
        $total_harga = 0;
        $validated_items = [];

        foreach ($items as $item) {
            if (!isset($item['id_menu']) || !isset($item['jumlah']) || $item['jumlah'] <= 0) {
                throw new Exception("Data item tidak valid");
            }

            $id_menu = (int)$item['id_menu'];
            $jumlah = (int)$item['jumlah'];

            // Get menu info and check availability
            $menu_sql = "SELECT id_menu, nama_menu, harga, status_tersedia
                        FROM menus
                        WHERE id_menu = :id_menu";
            $menu_stmt = $conn->prepare($menu_sql);
            $menu_stmt->bindParam(':id_menu', $id_menu);
            $menu_stmt->execute();

            if ($menu_stmt->rowCount() === 0) {
                throw new Exception("Menu dengan ID $id_menu tidak ditemukan");
            }

            $menu = $menu_stmt->fetch();
            if (!$menu['status_tersedia']) {
                throw new Exception("Menu '{$menu['nama_menu']}' tidak tersedia");
            }

            $subtotal = $menu['harga'] * $jumlah;
            $total_harga += $subtotal;

            $validated_items[] = [
                'id_menu' => $id_menu,
                'nama_menu' => $menu['nama_menu'],
                'jumlah' => $jumlah,
                'harga_satuan' => $menu['harga'],
                'subtotal' => $subtotal
            ];
        }

        // Generate order number
        $order_number = 'ORD' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        // Insert into pesanan table with user ID
        $pesanan_sql = "INSERT INTO pesanan (nama_pelanggan, no_meja, total_harga, status_pesanan, id_user)
                        VALUES (:nama_pelanggan, :no_meja, :total_harga, 'pending', :id_user)";

        $pesanan_stmt = $conn->prepare($pesanan_sql);
        $pesanan_stmt->bindParam(':nama_pelanggan', $nama_pelanggan);
        $pesanan_stmt->bindParam(':no_meja', $no_meja);
        $pesanan_stmt->bindParam(':total_harga', $total_harga);
        $pesanan_stmt->bindParam(':id_user', $id_user);
        $pesanan_stmt->execute();

        $id_pesanan = $conn->lastInsertId();

        // Insert into detail_pesanan table
        $detail_sql = "INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, harga_satuan)
                       VALUES (:id_pesanan, :id_menu, :jumlah, :harga_satuan)";
        $detail_stmt = $conn->prepare($detail_sql);

        foreach ($validated_items as $item) {
            $detail_stmt->bindParam(':id_pesanan', $id_pesanan);
            $detail_stmt->bindParam(':id_menu', $item['id_menu']);
            $detail_stmt->bindParam(':jumlah', $item['jumlah']);
            $detail_stmt->bindParam(':harga_satuan', $item['harga_satuan']);
            $detail_stmt->execute();
        }

        $conn->commit();

        // Prepare response data
        $response_data = [
            'id_pesanan' => $id_pesanan,
            'order_number' => $order_number,
            'nama_pelanggan' => $nama_pelanggan,
            'no_meja' => $no_meja,
            'total_harga' => $total_harga,
            'total_harga_formatted' => formatRupiah($total_harga),
            'status_pesanan' => 'pending',
            'items' => $validated_items,
            'waktu_pesan' => date('Y-m-d H:i:s')
        ];

        jsonResponse(true, "Pesanan berhasil dibuat", $response_data, 201);

    } catch(Exception $e) {
        $conn->rollBack();
        jsonResponse(false, "Error: " . $e->getMessage(), null, 400);
    }

} catch(PDOException $e) {
    jsonResponse(false, "Database Error: " . $e->getMessage(), null, 500);
}
?>