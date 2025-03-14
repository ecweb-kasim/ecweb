<?php
require_once 'includes/config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("Invalid social link ID.");
}

try {
    $checkStmt = $pdo->prepare("SELECT id FROM social_links WHERE id = ?");
    $checkStmt->execute([$id]);
    if ($checkStmt->rowCount() === 0) {
        die("Social link with ID {$id} not found.");
    }

    $deleteStmt = $pdo->prepare("DELETE FROM social_links WHERE id = ?");
    $deleteStmt->execute([$id]);

    $_SESSION['success'] = "Social link with ID {$id} deleted successfully!";
    header("Location: ?page=social_links");
    exit;
} catch (PDOException $e) {
    die("Error deleting social link: " . htmlspecialchars($e->getMessage()));
}