<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/config.php'; // Adjust path as needed

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$successMessage = '';
$errorMessage = '';

$action = $_GET['action'] ?? 'view';
$id = $_GET['id'] ?? null;

// Handle order addition or editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'add_order' || $action === 'edit_order')) {
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
            if ($action === 'add_order') {
                $stmt = $pdo->prepare("INSERT INTO ecweb.orders (user_id, total_amount, order_date, status, shipping_address, username) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$user_id, $total_amount, $order_date, $status, $shipping_address, $username])) {
                    $successMessage = "Order added successfully.";
                    header("Location: index.php?page=orders&success=1");
                    exit;
                }
            } elseif ($action === 'edit_order' && $id) {
                $stmt = $pdo->prepare("UPDATE ecweb.orders SET user_id = ?, total_amount = ?, order_date = ?, status = ?, shipping_address = ?, username = ? WHERE order_id = ?");
                if ($stmt->execute([$user_id, $total_amount, $order_date, $status, $shipping_address, $username, $id])) {
                    $successMessage = "Order updated successfully.";
                    header("Location: index.php?page=orders&success=1");
                    exit;
                }
            }
            $errorMessage = "Failed to process the request.";
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}

// Handle order deletion
if ($action === 'delete_order') {
    echo "<pre>";
    echo "Debugging Order Deletion:\n";
    echo "All GET parameters: " . print_r($_GET, true) . "\n";
    $id = isset($_GET['id']) ? trim($_GET['id']) : null;
    echo "Processed ID: " . ($id ?: 'null') . "\n";
    $isValid = ($id !== null && ctype_digit($id) && (int)$id > 0);
    echo "Validation: id is " . ($isValid ? 'valid' : 'invalid') . "\n";
    echo "ctype_digit($id): " . (ctype_digit($id) ? 'yes' : 'no') . "\n";
    echo "(int)$id: " . ((int)$id) . "\n";
    echo "</pre>";
    exit; // Stop execution to see the debug output

    $id = (int)$id; // Cast to integer

    try {
        $pdo->beginTransaction();

        // Delete related order items (optional if ON DELETE CASCADE is set)
        $stmt = $pdo->prepare("DELETE FROM ecweb.order_items WHERE order_id = ?");
        $stmt->execute([$id]);

        // Delete the order
        $stmt = $pdo->prepare("DELETE FROM ecweb.orders WHERE order_id = ?");
        $stmt->execute([$id]);

        $pdo->commit();

        $successMessage = "Order deleted successfully.";
        header("Location: index.php?page=orders&success=1");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $errorMessage = "Error deleting order: " . $e->getMessage();
        header("Location: index.php?page=orders&error=" . urlencode($errorMessage));
        exit;
    }
}

// Handle search functionality
$searchTerm = $_GET['search'] ?? '';
$orders = [];

try {
    if ($searchTerm) {
        $stmt = $pdo->prepare("SELECT order_id, user_id, total_amount, order_date, status, shipping_address, username FROM ecweb.orders WHERE order_id = ? OR user_id = ? OR status LIKE ? OR shipping_address LIKE ? OR username LIKE ?");
        $stmt->execute([$searchTerm, $searchTerm, "%$searchTerm%", "%$searchTerm%", "%$searchTerm%"]);
    } else {
        $stmt = $pdo->query("SELECT order_id, user_id, total_amount, order_date, status, shipping_address, username FROM ecweb.orders");
    }
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = "Error fetching orders: " . $e->getMessage();
}

// Fetch order data for editing
$order = null;
if ($action === 'edit_order' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM ecweb.orders WHERE order_id = ?");
    $stmt->execute([$id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) {
        header("Location: index.php?page=orders");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin Panel</title>
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
        .product-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 0;
            overflow: hidden;
        }
        .product-table thead th {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 14px;
        }
        .product-table tbody td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
            font-size: 14px;
        }
        .product-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .product-table th:nth-child(1),
        .product-table td:nth-child(1) {
            width: 5%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .product-table th:nth-child(2),
        .product-table td:nth-child(2) {
            width: 8%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .product-table th:nth-child(3),
        .product-table td:nth-child(3) {
            width: 12%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .product-table th:nth-child(4),
        .product-table td:nth-child(4) {
            width: 10%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .product-table th:nth-child(5),
        .product-table td:nth-child(5) {
            width: 15%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .product-table th:nth-child(6),
        .product-table td:nth-child(6) {
            width: 20%;
            white-space: normal;
            overflow-wrap: break-word;
        }
        .product-table th:nth-child(7),
        .product-table td:nth-child(7) {
            width: 20%;
        }
        .product-table th:nth-child(8),
        .product-table td:nth-child(8) {
            width: 10%;
        }
        .action-bar {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            background-color: #fff;
            padding: 10px;
            border-radius: 0;
        }
        .search-form {
            display: flex;
            align-items: center;
            flex: 1;
            margin-right: 10px;
        }
        .search-form input[type="text"] {
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid #ced4da;
            border-right: none;
            border-radius: 0;
            outline: none;
            width: 100%;
            box-sizing: border-box;
        }
        .search-form button {
            padding: 8px 12px;
            font-size: 14px;
            background-color: #007bff;
            border: 1px solid #007bff;
            border-left: none;
            border-radius: 0;
            cursor: pointer;
            color: white;
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
        }
        .btn {
            display: inline-block;
            padding: 5px 10px;
            font-size: 12px;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            line-height: normal;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
            border: none;
        }
        .btn-custom {
            background-color: #dc3545;
            color: white;
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
        .status-select {
            padding: 4px 20px 4px 8px;
            border-radius: 0;
            border: 1px solid #ced4da;
            background-color: #fff;
            cursor: pointer;
            font-size: 14px;
            height: 28px;
            line-height: 1;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="%23333" viewBox="0 0 16 16"><path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 10px;
            width: 100px;
            display: inline-block;
            vertical-align: middle;
        }
        .update-btn {
            padding: 4px 8px;
            color: white;
            border: none;
            border-radius: 0;
            cursor: pointer;
            font-size: 14px;
            height: 28px;
            line-height: 1;
            background-color: #007bff;
            display: inline-block;
            vertical-align: middle;
            margin-left: 5px;
        }
        .update-btn.loading::after {
            content: "\f110";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            display: inline-block;
            margin-left: 6px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .action-buttons {
            white-space: nowrap;
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
        .alert-success {
            background-color: #28a745;
        }
        .alert-danger {
            background-color: #dc3545;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .status-select option {
            color: black;
            padding: 8px;
        }
        .status-select option[value="pending"] {
            color: #007bff !important;
        }
        .status-select option[value="shipped"] {
            color: #28a745 !important;
        }
        .status-select option[value="delivered"] {
            color: #ffc107 !important;
        }
        .status-select:invalid {
            color: #888;
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
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            border-radius: 0;
            color: white;
            z-index: 1050;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .toast.show {
            opacity: 1;
        }
        .toast.success {
            background-color: #28a745;
        }
        .toast.error {
            background-color: #dc3545;
        }
        @media (max-width: 768px) {
            .product-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            .product-table th:nth-child(7),
            .product-table td:nth-child(7) {
                min-width: 180px;
            }
            .status-select {
                width: 100%;
                margin-bottom: 5px;
            }
            .update-btn {
                width: 100%;
                margin-left: 0;
            }
            .product-table th:nth-child(8),
            .product-table td:nth-child(8) {
                min-width: 120px;
            }
            .action-buttons {
                white-space: normal;
            }
            .action-buttons .btn {
                display: block;
                margin-bottom: 5px;
                width: 100%;
            }
            .action-bar {
                flex-direction: column;
                gap: 10px;
            }
            .search-form {
                margin-right: 0;
                width: 100%;
            }
            .add-new-product-button {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<div class="container">
        <h2 >Orders</h2>
    <p>Manage your order list here.</p>

    <?php if (isset($_GET['success']) && $_GET['success']): ?>
        <div class="alert alert-success" id="successAlert"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger" id="errorAlert"><?php echo htmlspecialchars(urldecode($_GET['error'])); ?></div>
    <?php endif; ?>
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger" id="errorAlert"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <?php if ($action === 'view'): ?>
        <div class="action-bar">
            <div class="search-form">
                <form method="GET" action="index.php" style="display: flex; align-items: center; width: 100%;">
                    <input type="hidden" name="page" value="orders">
                    <input type="text" name="search" placeholder="Search by ID, User ID, Status, Address, or Username" value="<?php echo htmlspecialchars($searchTerm); ?>" aria-label="Search orders">
                    <button type="submit" aria-label="Search"><i class="fas fa-search"></i></button>
                    <a href="index.php?page=orders" class="back-button" aria-label="Clear search">Clear</a>
                </form>
            </div>
            <a href="index.php?page=orders&action=add_order" class="add-new-product-button" aria-label="Add new order">Add New Order</a>
        </div>

        <table class="product-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Total Amount</th>
                    <th>Order Date</th>
                    <th>Shipping Address</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="8">No orders found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['username'] ?? 'N/A'); ?></td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></td>
                            <td><?php echo htmlspecialchars($order['shipping_address'] ?? 'Not provided'); ?></td>
                            <td>
                                <select class="status-select" data-order-id="<?php echo htmlspecialchars($order['order_id']); ?>" aria-label="Order status for order <?php echo htmlspecialchars($order['order_id']); ?>">
                                    <option value="pending" <?php echo ($order['status'] === 'pending' || !$order['status']) ? 'selected' : ''; ?>>Pending</option>
                                    <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                </select>
                                <button class="update-btn" data-order-id="<?php echo htmlspecialchars($order['order_id']); ?>" data-current-status="<?php echo htmlspecialchars($order['status'] ?? 'pending'); ?>" aria-label="Update status for order <?php echo htmlspecialchars($order['order_id']); ?>">Update</button>
                            </td>
                            <td class="action-buttons">
                                <a href="index.php?page=orders&action=edit_order&id=<?php echo $order['order_id']; ?>" class="btn btn-warning" aria-label="Edit order <?php echo htmlspecialchars($order['order_id']); ?>">Edit</a>
                                <a href="index.php?page=orders&action=delete_order&id=<?php echo $order['order_id']; ?>" class="btn btn-custom" onclick="return confirm('Are you sure you want to delete this order?');" aria-label="Delete order <?php echo htmlspecialchars($order['order_id']); ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php elseif ($action === 'add_order'): ?>
        <h3>Add New Order</h3>
        <form method="POST" action="index.php?page=orders&action=add_order">
            <div class="mb-3">
                <label for="user_id" class="form-label">User ID</label>
                <input type="number" id="user_id" name="user_id" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="total_amount" class="form-label">Total Amount</label>
                <input type="number" step="0.01" id="total_amount" name="total_amount" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="order_date" class="form-label">Order Date</label>
                <input type="datetime-local" id="order_date" name="order_date" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
            </div>
            <div class="mb-3">
                <label for="shipping_address" class="form-label">Shipping Address</label>
                <textarea id="shipping_address" name="shipping_address" class="form-control" rows="3" placeholder="Enter shipping address" required></textarea>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-control status-select" aria-label="Order status">
                    <option value="pending">Pending</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                </select>
            </div>
            <button type="submit" class="add-new-product-button">Add Order</button>
            <a href="index.php?page=orders" class="back-button">Back</a>
        </form>
    <?php elseif ($action === 'edit_order' && $order): ?>
        <h3>Edit Order</h3>
        <form method="POST" action="index.php?page=orders&action=edit_order&id=<?php echo $order['order_id']; ?>">
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
            <a href="index.php?page=orders" class="back-button">Back</a>
        </form>
    <?php endif; ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Handle success/error alerts
    const successAlert = document.querySelector('#successAlert');
    if (successAlert) {
        successAlert.style.display = "block";
        setTimeout(() => successAlert.style.display = "none", 3000);
    }
    const errorAlert = document.querySelector('#errorAlert');
    if (errorAlert) {
        errorAlert.style.display = "block";
        setTimeout(() => errorAlert.style.display = "none", 5000);
    }

    // Function to show toast notifications
    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 100);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Handle status updates
    const updateButtons = document.querySelectorAll('.update-btn');
    updateButtons.forEach(button => {
        const statusSelect = button.parentElement.querySelector('.status-select');
        const currentStatus = button.getAttribute('data-current-status');

        statusSelect.addEventListener('change', function() {
            const newStatus = this.value;
            button.setAttribute('data-current-status', newStatus);
            statusSelect.style.color = getStatusColor(newStatus);
        });

        statusSelect.style.color = getStatusColor(currentStatus);

        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const newStatus = statusSelect.value;

            // Show loading state
            button.classList.add('loading');
            button.disabled = true;

            fetch('/admin/pages/orders/update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `order_id=${encodeURIComponent(orderId)}&status=${encodeURIComponent(newStatus)}`
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                button.classList.remove('loading');
                button.disabled = false;
                if (data.success) {
                    showToast('Status updated successfully!', 'success');
                    button.setAttribute('data-current-status', newStatus);
                } else {
                    showToast('Error: ' + data.message, 'error');
                    statusSelect.value = currentStatus;
                    statusSelect.style.color = getStatusColor(currentStatus);
                }
            })
            .catch(error => {
                button.classList.remove('loading');
                button.disabled = false;
                showToast('An error occurred: ' + error.message, 'error');
                statusSelect.value = currentStatus;
                statusSelect.style.color = getStatusColor(currentStatus);
            });
        });
    });

    function getStatusColor(status) {
        switch (status.toLowerCase()) {
            case 'pending': return '#007bff';
            case 'shipped': return '#28a745';
            case 'delivered': return '#ffc107';
            default: return '#888';
        }
    }
});
</script>
</body>
</html>