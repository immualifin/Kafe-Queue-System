<?php
// Konfigurasi Database dan CORS
// Ganti '*' dengan domain client yang spesifik untuk keamanan
// header("Access-Control-Allow-Origin: *");
// Contoh: header("Access-Control-Allow-Origin: http://192.168.1.100/tugas_kafe_client");

// Untuk development, gunakan '*' atau sesuaikan dengan IP client
$allowed_origins = [
    "http://localhost",
    "http://127.0.0.1",
    "http://192.168.1.25",
    "http://192.168.1.25/tugas_kafe_client",
    // IP Client yang digunakan
    "http://10.90.35.161",
    "http://10.90.35.161/",
    "http://192.168.1.37",
    "http://192.168.1.37/tugas_kafe_client",
    // Localhost variations
    "http://localhost:80",
    "http://localhost:8080",
    "http://127.0.0.1:80",
    "http://127.0.0.1:8080"
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$origin_host = parse_url($origin, PHP_URL_HOST) ?? '';

// Check if origin is allowed
$is_allowed = false;
foreach ($allowed_origins as $allowed_origin) {
    $allowed_host = parse_url($allowed_origin, PHP_URL_HOST) ?? '';
    if ($origin === $allowed_origin || $origin_host === $allowed_host) {
        $is_allowed = true;
        break;
    }
}

if ($is_allowed) {
    header("Access-Control-Allow-Origin: $origin");
    error_log("CORS: Allowing known origin: " . $origin);
} else {
    // Untuk development, izinkan semua origin
    header("Access-Control-Allow-Origin: *");
    // Log untuk debugging (comment di production)
    error_log("CORS: Allowing unknown origin: " . $origin);
}
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 86400");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'db_kafe_antrian');
define('DB_USER', 'root');
define('DB_PASS', '');

// Koneksi Database
function getConnection() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conn;
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Koneksi database gagal: " . $e->getMessage()
        ]);
        exit();
    }
}

// Helper functions
function jsonResponse($success, $message, $data = null, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);
    exit();
}

function getPostData() {
  $json = file_get_contents('php://input');
  $decoded = json_decode($json, true);
  return $decoded ? $decoded : [];
   // return json_decode($json, true) ?? [];
}

function validateRequired($data, $required_fields) {
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            jsonResponse(false, "Field '$field' wajib diisi", null, 400);
        }
    }
}

function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatRupiah($amount) {
    return "Rp " . number_format($amount, 0, ',', '.');
}

// Autentikasi functions
function getAuthenticatedUserId() {
    // Get Authorization header
    $headers = getallheaders();
    $auth_header = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (empty($auth_header) || !str_starts_with($auth_header, 'Bearer ')) {
        return null;
    }

    $token = substr($auth_header, 7); // Remove 'Bearer ' prefix

    try {
        $conn = getConnection();
        $sql = "SELECT id_user FROM users
                WHERE session_token = :token
                AND (session_expiry IS NULL OR session_expiry > NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            return $user['id_user'];
        }

        return null;
    } catch(PDOException $e) {
        return null;
    }
}

function requireAuth() {
    $user_id = getAuthenticatedUserId();
    if (!$user_id) {
        jsonResponse(false, "Autentikasi diperlukan. Silakan login terlebih dahulu.", null, 401);
    }
    return $user_id;
}
?>
