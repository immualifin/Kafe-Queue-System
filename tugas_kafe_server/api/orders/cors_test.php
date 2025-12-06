<?php
require_once '../../config/database.php';

// Simple CORS test endpoint
try {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? 'unknown';

    $response = [
        'success' => true,
        'message' => 'CORS test successful',
        'origin' => $origin,
        'method' => $_SERVER['REQUEST_METHOD'],
        'headers' => getallheaders(),
        'timestamp' => date('Y-m-d H:i:s')
    ];

    jsonResponse(true, "CORS test successful", $response);

} catch(Exception $e) {
    jsonResponse(false, "CORS test failed: " . $e->getMessage(), null);
}
?>