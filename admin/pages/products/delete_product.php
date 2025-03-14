<?php

// Include database connection
require_once 'includes/config.php';

try {
    // Get product ID from URL
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    // Fetch the product to display in the confirmation
    $stmt = $pdo->prepare("SELECT title, image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo "<h2>Product Not Found</h2>";
        echo "<p><a href='?page=products'>Back to Products</a></p>";
        exit;
    }

    // Delete the product from the database
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);

    // Optionally delete the image file if it exists
    $targetDir = '../assets/images/products/';
    if (!empty($product['image']) && file_exists($targetDir . $product['image'])) {
        unlink($targetDir . $product['image']);
    }

    echo "<div class='success-message'>";
    echo "<h2>Product Deleted</h2>";
    echo "<p>The product '{$product['title']}' has been deleted.</p>";
    echo "<p><a href='?page=products'>Back to Products</a></p>";
    echo "</div>";
} catch (PDOException $e) {
    die("Error deleting product: " . $e->getMessage());
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