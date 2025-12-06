<?php
// include database and object files
require_once '../../config/database.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, "Method tidak diizinkan", null, 405);
}

try {
    // Always expect JSON request now
    $data = getPostData();

    // Validate required fields
    validateRequired($data, ['nama_menu', 'harga', 'id_kategori']);

    // Sanitize input
    $nama_menu = sanitizeInput($data['nama_menu']);
    $harga = sanitizeInput($data['harga']);
    $id_kategori = sanitizeInput($data['id_kategori']);
    $status_tersedia = isset($data['status_tersedia']) ? (bool)$data['status_tersedia'] : true;
    $gambar = isset($data['gambar']) ? filter_var($data['gambar'], FILTER_VALIDATE_URL) : null;

    // Convert harga to numeric and validate
    $harga_numeric = is_numeric($harga) ? (float)$harga : 0;
    if ($harga_numeric <= 0) {
        jsonResponse(false, "Harga harus berupa angka positif", null, 400);
    }

    $conn = getConnection();

    // Check if menu already exists
    $check_sql = "SELECT id_menu FROM menus WHERE nama_menu = :nama_menu";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(':nama_menu', $nama_menu);
    $check_stmt->execute();

    if ($check_stmt->rowCount() > 0) {
        jsonResponse(false, "Menu dengan nama tersebut sudah ada", null, 400);
    }

    // Insert menu
    $sql = "INSERT INTO menus (nama_menu, harga, id_kategori, gambar, status_tersedia)
            VALUES (:nama_menu, :harga, :id_kategori, :gambar, :status_tersedia)";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nama_menu', $nama_menu);
    $stmt->bindParam(':harga', $harga_numeric, PDO::PARAM_STR);
    $stmt->bindParam(':id_kategori', $id_kategori);
    $stmt->bindParam(':gambar', $gambar); // Save the URL directly
    $stmt->bindParam(':status_tersedia', $status_tersedia, PDO::PARAM_BOOL);

    if ($stmt->execute()) {
        $id_menu = $conn->lastInsertId();
        $response_data = [
            'id_menu' => $id_menu,
            'nama_menu' => $nama_menu,
            'harga' => $harga,
            'harga_formatted' => formatRupiah($harga),
            'id_kategori' => $id_kategori,
            'gambar' => $gambar, // Return the saved URL
            'status_tersedia' => $status_tersedia
        ];
        jsonResponse(true, "Menu berhasil ditambahkan", $response_data, 201);
    } else {
        jsonResponse(false, "Gagal menambahkan menu", null, 500);
    }

} catch(PDOException $e) {
    jsonResponse(false, "Error: " . $e->getMessage(), null, 500);
}
?>
