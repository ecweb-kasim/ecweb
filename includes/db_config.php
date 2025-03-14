<?php
// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "vs-db.cnugewiui8gy.ap-southeast-2.rds.amazonaws.com";
$dbname = "ecweb";
$username = getenv('DB_USERNAME') ?: "admin"; // Fallback to hardcoded value if env not set
$password = getenv('DB_PASSWORD') ?: "VS12345%54321";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Disable emulated prepares
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    http_response_code(500);
    include 'error.php'; // Ensure you create this file or adjust the error handling
    exit;
}
?>