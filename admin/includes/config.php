<?php
// includes/config.php
$host = "vs-db.cnugewiui8gy.ap-southeast-2.rds.amazonaws.com";
$dbname = "ecweb";
$username = "admin";
$password = "VS12345%54321";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>