<?php
// include database and object files
require_once '../../config/database.php';

// Check if it's a DELETE request
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    jsonResponse(false, "Method tidak diizinkan", null, 405);
}

// Get id_menu from URL
$id_menu = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id_menu) {
    jsonResponse(false, "ID menu tidak valid", null, 400);
}

try {
    $conn = getConnection();

    // Check if menu exists
    $check_sql = "SELECT * FROM menus WHERE id_menu = :id_menu";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(':id_menu', $id_menu);
    $check_stmt->execute();

    if ($check_stmt->rowCount() === 0) {
        jsonResponse(false, "Menu tidak ditemukan", null, 404);
    }

    $menu = $check_stmt->fetch();

    // Check if menu is used in any orders
    $check_orders_sql = "SELECT COUNT(*) as count FROM detail_pesanan WHERE id_menu = :id_menu";
    $check_orders_stmt = $conn->prepare($check_orders_sql);
    $check_orders_stmt->bindParam(':id_menu', $id_menu);
    $check_orders_stmt->execute();
    $orders_count = $check_orders_stmt->fetch()['count'];

    if ($orders_count > 0) {
        jsonResponse(false, "Tidak dapat menghapus menu yang sudah ada dalam pesanan. Set status tidak tersedia saja.", null, 400);
    }

    // Start transaction
    $conn->beginTransaction();

    try {
        // Delete menu image if exists
        if ($menu['gambar']) {
            $image_path = dirname(__DIR__, 2) . '/server/uploads/' . $menu['gambar'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        // Delete menu
        $delete_sql = "DELETE FROM menus WHERE id_menu = :id_menu";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bindParam(':id_menu', $id_menu);
        $delete_stmt->execute();

        $conn->commit();

        jsonResponse(true, "Menu berhasil dihapus", null);

    } catch(Exception $e) {
        $conn->rollBack();
        throw $e;
    }

} catch(PDOException $e) {
    jsonResponse(false, "Error: " . $e->getMessage(), null, 500);
}
?>