<?php
require_once 'includes/config.php';

try {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $stmt = $pdo->prepare("SELECT title, image FROM collections WHERE id = ?");
    $stmt->execute([$id]);
    $collection = $stmt->fetch();

    if (!$collection) {
        echo "<h2>Collection Not Found</h2>";
        echo "<p><a href='?page=collections'>Back to Collections</a></p>";
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM collections WHERE id = ?");
    $stmt->execute([$id]);

    $targetDir = '../assets/images/collections/';
    if (!empty($collection['image']) && file_exists($targetDir . $collection['image'])) {
        unlink($targetDir . $collection['image']);
    }

    echo "<div class='success-message'>";
    echo "<h2>Collection Deleted</h2>";
    echo "<p>The collection '{$collection['title']}' has been deleted.</p>";
    echo "<p><a href='?page=collections'>Back to Collections</a></p>";
    echo "</div>";
} catch (PDOException $e) {
    die("Error deleting collection: " . $e->getMessage());
}
?>

<style>
    .success-message {
        background-color: #d4edda;
        color: #155724;
        padding: 15px;
        margin: 20px auto;
        width: 50%;
        border: 1px solid #c3e6cb;
        border-radius: 4px;
    }
</style>