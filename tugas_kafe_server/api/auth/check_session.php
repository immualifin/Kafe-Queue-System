<?php
// include database and object files
require_once '../../config/database.php';

// Check if it's a GET request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, "Method tidak diizinkan", null, 405);
}

try {
    // Get Authorization header
    $headers = getallheaders();
    $auth_header = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (!$auth_header) {
        jsonResponse(false, "Token tidak ditemukan", null, 401);
    }

    // Extract token (format: "Bearer <token>")
    $token_parts = explode(' ', $auth_header);
    if (count($token_parts) !== 2 || $token_parts[0] !== 'Bearer') {
        jsonResponse(false, "Format token tidak valid", null, 401);
    }

    $session_token = $token_parts[1];

    $conn = getConnection();

    // Check session token
    $sql = "SELECT id_user, username, nama_lengkap, role, session_expiry
            FROM users
            WHERE session_token = :session_token
            AND session_expiry > NOW()";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':session_token', $session_token);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        jsonResponse(false, "Sesi tidak valid atau telah kadaluarsa", null, 401);
    }

    $user = $stmt->fetch();

    $response_data = [
        'user' => [
            'id_user' => (int)$user['id_user'],
            'username' => $user['username'],
            'nama_lengkap' => $user['nama_lengkap'],
            'role' => $user['role']
        ],
        'session_expiry' => $user['session_expiry'],
        'session_expiry_formatted' => date('d M Y H:i', strtotime($user['session_expiry'])),
        'is_valid' => true
    ];

    jsonResponse(true, "Sesi valid", $response_data);

} catch(PDOException $e) {
    jsonResponse(false, "Database Error: " . $e->getMessage(), null, 500);
}
?>