<?php
session_start();
include '../includes/db_config.php'; // Include database connection

header('Content-Type: application/json');

// Log errors to a file for debugging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/Users/ASUS/Desktop/ecweb/ecweb/logs/error.log');
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log('User not logged in: $_SESSION[\'user_id\'] is not set');
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
error_log("User ID: $user_id");

// Get the cart, total, username, and shipping address from the request
$data = json_decode(file_get_contents('php://input'), true);
$cart = $data['cart'] ?? [];
$total = $data['total'] ?? 0;
$username = $data['username'] ?? 'N/A';
$shippingAddress = $data['shippingAddress'] ?? 'Not provided';

error_log('Cart data: ' . print_r($cart, true));
error_log('Total: ' . $total);
error_log('Username: ' . $username);
error_log('Shipping Address: ' . $shippingAddress);

// Check if the cart is empty
if (empty($cart)) {
    error_log('Cart is empty');
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit();
}

// Validate cart items
foreach ($cart as $item) {
    if (!isset($item['id']) || !isset($item['quantity']) || !isset($item['price'])) {
        error_log('Invalid cart item: ' . print_r($item, true));
        echo json_encode(['success' => false, 'message' => 'Invalid cart item: Missing required fields']);
        exit();
    }
}

try {
    $pdo->beginTransaction();

    // Insert into orders table with username and shipping_address
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_date, total_amount, username, shipping_address) VALUES (?, NOW(), ?, ?, ?)");
    $stmt->execute([$user_id, $total, $username, $shippingAddress]);
    $order_id = $pdo->lastInsertId();
    error_log("Order inserted successfully. Order ID: $order_id");

    // Insert into order_items table (using product_id)
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, size, color) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($cart as $item) {
        $size = $item['size'] ?? null;
        $color = $item['color'] ?? null;
        $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price'], $size, $color]);
    }

    $pdo->commit();
    error_log('Order and items saved successfully');
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('Database error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('General error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'General error: ' . $e->getMessage()]);
}
?>