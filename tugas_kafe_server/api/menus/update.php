<?php
// include database and object files
require_once '../../config/database.php';

// Check if it's a PUT request
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    jsonResponse(false, "Method tidak diizinkan", null, 405);
}

// Get id_menu from URL
$id_menu = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id_menu) {
    jsonResponse(false, "ID menu tidak valid", null, 400);
}

try {
    // Always expect JSON request
    $data = getPostData();

    // Sanitize input
    $nama_menu = isset($data['nama_menu']) ? sanitizeInput($data['nama_menu']) : null;
    $harga = isset($data['harga']) ? sanitizeInput($data['harga']) : null;
    $id_kategori = isset($data['id_kategori']) ? sanitizeInput($data['id_kategori']) : null;
    $status_tersedia = isset($data['status_tersedia']) ? (bool)$data['status_tersedia'] : null;
    $gambar = isset($data['gambar']) ? filter_var($data['gambar'], FILTER_VALIDATE_URL) : null;

    // Convert harga to numeric and validate
    $harga_numeric = null;
    if ($harga !== null && $harga !== '') {
        $harga_numeric = is_numeric($harga) ? (float)$harga : 0;
        if ($harga_numeric <= 0) {
            jsonResponse(false, "Harga harus berupa angka positif", null, 400);
        }
    }

    $conn = getConnection();

    // Check if menu exists
    $check_sql = "SELECT id_menu FROM menus WHERE id_menu = :id_menu";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(':id_menu', $id_menu);
    $check_stmt->execute();

    if ($check_stmt->rowCount() === 0) {
        jsonResponse(false, "Menu tidak ditemukan", null, 404);
    }
    
    // Build dynamic update query
    $update_fields = [];
    $params = [];

    if ($nama_menu !== null && $nama_menu !== '') {
        $update_fields[] = "nama_menu = :nama_menu";
        $params[':nama_menu'] = $nama_menu;
    }
    if ($harga_numeric !== null) {
        $update_fields[] = "harga = :harga";
        $params[':harga'] = $harga_numeric;
    }
    if ($id_kategori !== null && $id_kategori !== '') {
        $update_fields[] = "id_kategori = :id_kategori";
        $params[':id_kategori'] = (int) $id_kategori;
    }
    // Also update gambar if it's provided, even if it's an empty string (to remove image)
    if (array_key_exists('gambar', $data)) {
        $update_fields[] = "gambar = :gambar";
        $params[':gambar'] = $gambar;
    }
    if ($status_tersedia !== null) {
        $update_fields[] = "status_tersedia = :status_tersedia";
        $params[':status_tersedia'] = $status_tersedia;
    }

    if (empty($update_fields)) {
        jsonResponse(false, "Tidak ada data yang diupdate", null, 400);
    }

    // Add id_menu to parameters
    $params[':id_menu'] = $id_menu;

    $sql = "UPDATE menus SET " . implode(', ', $update_fields) . " WHERE id_menu = :id_menu";
    $stmt = $conn->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    if ($stmt->execute()) {
        // Get updated data
        $updated_sql = "SELECT m.*, km.nama_kategori
                        FROM menus m
                        LEFT JOIN kategori_menu km ON m.id_kategori = km.id_kategori
                        WHERE m.id_menu = :id_menu";
        $updated_stmt = $conn->prepare($updated_sql);
        $updated_stmt->bindParam(':id_menu', $id_menu);
        $updated_stmt->execute();

        $updated_menu = $updated_stmt->fetch();
        $updated_menu['harga_formatted'] = formatRupiah($updated_menu['harga']);
        // The 'gambar' field is already the correct URL from the database
        
        jsonResponse(true, "Menu berhasil diupdate", $updated_menu);
    } else {
        jsonResponse(false, "Gagal mengupdate menu", null, 500);
    }

} catch(PDOException $e) {
    jsonResponse(false, "Error: " . $e->getMessage(), null, 500);
}
?>
