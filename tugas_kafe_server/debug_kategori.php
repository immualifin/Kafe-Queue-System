<?php
// Debug kategori data
require_once 'config/database.php';

try {
    $conn = getConnection();

    // Get all menus with their categories
    $sql = "SELECT m.id_menu, m.nama_menu, m.id_kategori, km.nama_kategori
            FROM menus m
            LEFT JOIN kategori_menu km ON m.id_kategori = km.id_kategori
            ORDER BY m.id_menu";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "=== Menu Categories Debug ===\n";
    foreach ($menus as $menu) {
        echo "Menu ID: {$menu['id_menu']}\n";
        echo "Nama: {$menu['nama_menu']}\n";
        echo "ID Kategori: " . ($menu['id_kategori'] ?? 'NULL') . "\n";
        echo "Nama Kategori: " . ($menu['nama_kategori'] ?? 'NULL') . "\n";
        echo "---\n";
    }

    // Get all categories
    $sql2 = "SELECT * FROM kategori_menu";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->execute();
    $categories = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    echo "\n=== Available Categories ===\n";
    foreach ($categories as $cat) {
        echo "ID: {$cat['id_kategori']}, Nama: {$cat['nama_kategori']}\n";
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>