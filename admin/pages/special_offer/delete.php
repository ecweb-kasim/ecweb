<?php
require_once 'includes/config.php';


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("Invalid special offer ID.");
}

try {
    $checkStmt = $pdo->prepare("SELECT id FROM special_offer WHERE id = ?");
    $checkStmt->execute([$id]);
    if ($checkStmt->rowCount() === 0) {
        die("Special offer with ID {$id} not found.");
    }

    $deleteStmt = $pdo->prepare("DELETE FROM special_offer WHERE id = ?");
    $deleteStmt->execute([$id]);

    $_SESSION['success'] = "Special offer with ID {$id} deleted successfully!";
    header("Location: ?page=special_offer");
    exit;
} catch (PDOException $e) {
    die("Error deleting special offer: " . htmlspecialchars($e->getMessage()));
}