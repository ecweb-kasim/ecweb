<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/config.php';

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize variables
$successMessage = '';
$errorMessage = '';

// Fetch the order ID from the URL
$order_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$order_id || $order_id <= 0) {
    header("Location: orders.php");
    exit;
}

// Fetch the existing order data
try {
    $stmt = $pdo->prepare("SELECT * FROM ecweb.orders WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header("Location: orders.php");
        exit;
    }
} catch (PDOException $e) {
    $errorMessage = "Error fetching order: " . $e->getMessage();
    header("Location: index.php?page=orders&error=" . urlencode($errorMessage));
    exit;
}

// Handle form submission for editing the order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
    $total_amount = filter_input(INPUT_POST, 'total_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $order_date = filter_input(INPUT_POST, 'order_date', FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $shipping_address = filter_input(INPUT_POST, 'shipping_address', FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);

    // Server-side validation
    if (!$user_id || $user_id <= 0) {
        $errorMessage = "Invalid User ID.";
    } elseif (!$total_amount || $total_amount <= 0) {
        $errorMessage = "Total Amount must be greater than 0.";
    } elseif (!$order_date) {
        $errorMessage = "Order Date is required.";
    } elseif (!$status || !in_array($status, ['pending', 'shipped', 'delivered'])) {
        $errorMessage = "Invalid status.";
    } elseif (!$shipping_address) {
        $errorMessage = "Shipping Address is required.";
    } elseif (!$username) {
        $errorMessage = "Username is required.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE ecweb.orders SET user_id = ?, total_amount = ?, order_date = ?, status = ?, shipping_address = ?, username = ? WHERE order_id = ?");
            $result = $stmt->execute([$user_id, $total_amount, $order_date, $status, $shipping_address, $username, $order_id]);

            if ($result) {
                header("Location: orders.php");
                exit;
            } else {
                $errorMessage = "Failed to update the order.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Error updating order: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order - Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 15px;
        }
        h2 {
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
            text-align: center;
            background-color: #2c3e50;
            padding: 15px;
            border-radius: 0;
        }
        h3 {
            font-size: 20px;
            font-weight: 500;
            color: #2c3e50;
            margin: 10px 0;
            text-align: center;
            background: linear-gradient(90deg, #2c3e50, #3498db);
            padding: 10px;
            color: white;
        }
        p {
            font-size: 14px;
            color: #6c757d;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 0;
            box-sizing: border-box;
            font-size: 14px;
        }
        .form-control.status-select {
            padding: 4px 20px 4px 8px;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="%23333" viewBox="0 0 16 16"><path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 10px;
        }
        .mb-3 {
            margin-bottom: 15px;
        }
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #2c3e50;
        }
        .add-new-product-button {
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 0;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            border: none;
        }
        .back-button {
            padding: 8px 12px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 0;
            margin-left: 10px;
            cursor: pointer;
        }
        .alert {
            display: none;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            padding: 10px 20px;
            border-radius: 0;
            color: white;
        }
        .alert-danger {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Orders</h2>
    <p>Edit the order details below.</p>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger" id="errorAlert"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <h3>Edit Order</h3>
    <form method="POST" action="edit-order.php?id=<?php echo htmlspecialchars($order_id); ?>">
        <div class="mb-3">
            <label for="order_id" class="form-label">Order ID</label>
            <input type="text" id="order_id" name="order_id" class="form-control" value="<?php echo htmlspecialchars($order['order_id']); ?>" disabled>
        </div>
        <div class="mb-3">
            <label for="user_id" class="form-label">User ID</label>
            <input type="number" id="user_id" name="user_id" class="form-control" value="<?php echo htmlspecialchars($order['user_id']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($order['username'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="total_amount" class="form-label">Total Amount</label>
            <input type="number" step="0.01" id="total_amount" name="total_amount" class="form-control" value="<?php echo htmlspecialchars($order['total_amount']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="order_date" class="form-label">Order Date</label>
            <input type="datetime-local" id="order_date" name="order_date" class="form-control" value="<?php echo str_replace(' ', 'T', htmlspecialchars($order['order_date'])); ?>" required>
        </div>
        <div class="mb-3">
            <label for="shipping_address" class="form-label">Shipping Address</label>
            <textarea id="shipping_address" name="shipping_address" class="form-control" rows="3" required><?php echo htmlspecialchars($order['shipping_address'] ?? ''); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-control status-select" aria-label="Order status">
                <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
            </select>
        </div>
        <button type="submit" class="add-new-product-button">Update Order</button>
        <a href="/admin/index.php?page=orders" class="back-button">Back</a>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Handle error alerts
    const errorAlert = document.querySelector('#errorAlert');
    if (errorAlert) {
        errorAlert.style.display = "block";
        setTimeout(() => errorAlert.style.display = "none", 5000);
    }
});
</script>
</body>
</html>