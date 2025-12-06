<?php
// include database and object files
require_once '../../config/database.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, "Method tidak diizinkan", null, 405);
}

try {
    // Get POST data
    $data = getPostData();

    // Validate required fields
    validateRequired($data, ['username', 'password']);

    // Sanitize input
    $username = sanitizeInput($data['username']);
    $password = $data['password'];

    $conn = getConnection();

    // Check if user exists
    $sql = "SELECT id_user, username, password, nama_lengkap, role, created_at
            FROM users
            WHERE username = :username";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        jsonResponse(false, "Username atau password salah", null, 401);
    }

    $user = $stmt->fetch();

    // Verify password
    if (!password_verify($password, $user['password'])) {
        jsonResponse(false, "Username atau password salah", null, 401);
    }

    // Create session token (simple approach, in production use JWT)
    $session_token = bin2hex(random_bytes(32));
    $session_expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));

    // Store session token (in production, store in separate sessions table)
    $update_sql = "UPDATE users
                  SET session_token = :session_token,
                      session_expiry = :session_expiry
                  WHERE id_user = :id_user";

    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bindParam(':session_token', $session_token);
    $update_stmt->bindParam(':session_expiry', $session_expiry);
    $update_stmt->bindParam(':id_user', $user['id_user']);

    if ($update_stmt->execute()) {
        // Prepare response data
        $response_data = [
            'user' => [
                'id_user' => (int)$user['id_user'],
                'username' => $user['username'],
                'nama_lengkap' => $user['nama_lengkap'],
                'role' => $user['role'],
                'created_at' => $user['created_at'],
                'created_at_formatted' => date('d M Y H:i', strtotime($user['created_at']))
            ],
            'session' => [
                'token' => $session_token,
                'expires_at' => $session_expiry,
                'expires_at_formatted' => date('d M Y H:i', strtotime($session_expiry))
            ],
            'login_time' => date('Y-m-d H:i:s'),
            'login_time_formatted' => date('d M Y H:i')
        ];

        jsonResponse(true, "Login berhasil", $response_data);
    } else {
        jsonResponse(false, "Gagal membuat sesi", null, 500);
    }

} catch(PDOException $e) {
    jsonResponse(false, "Database Error: " . $e->getMessage(), null, 500);
}
?>