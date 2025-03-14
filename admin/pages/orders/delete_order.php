<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$id || $id <= 0) {
    header("Location: /admin/index.php?page=orders&error=Invalid%20order%20ID.");
    exit;
}

try {
    $pdo->beginTransaction();

    // Delete related order items
    $stmt = $pdo->prepare("DELETE FROM ecweb.order_items WHERE order_id = ?");
    $stmt->execute([$id]);

    // Delete the order
    $stmt = $pdo->prepare("DELETE FROM ecweb.orders WHERE order_id = ?");
    $stmt->execute([$id]);

    $pdo->commit();

    header("Location: /admin/index.php?page=orders&success=1");
    exit;
} catch (PDOException $e) {
    $pdo->rollBack();
    $errorMessage = urlencode("Error deleting order: " . $e->getMessage());
    header("Location: orders?page=orders&error=$errorMessage");
    exit;
}
?>
