<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include_once '../includes/db_config.php';
include_once '../includes/header.php'; 
include_once '../includes/preloader.php';
include_once '../includes/svg_symbols.php'; 
include_once '../includes/modals.php'; 

// Fetch orders if user is logged in
$orders = [];
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching orders: " . $e->getMessage());
        $orders = []; // Fallback to empty array on error
    }
} else {
    // Show login prompt if not logged in
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Login Required',
                text: 'Please login or register to view your order.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Login',
                cancelButtonText: 'Register',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/users/login.php?returnUrl=" . urlencode($_SERVER['REQUEST_URI']) . "';
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    window.location.href = '/users/register.php';
                }
            });
        });
    </script>";
}
?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Hide the preloader when the page has fully loaded
        document.querySelector(".preloader").style.display = "none";
    });
</script>

<!DOCTYPE html>
<html lang="en">
<head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Your Orders - KaSim Store</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="icon" href="../assets/images/logo/favicon.png" type="image/png">
    <link rel="stylesheet" href="../assets/css/vendor.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,900;1,900&family=Source+Sans+Pro:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        .orders-content { padding: 40px 0; background-color: #f9f9f9; }
        .order-card { border: 1px solid #e0e0e0; padding: 20px; border-radius: 12px; background-color: #fff; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); transition: opacity 0.3s ease; }
        .order-card.old-order { opacity: 0.7; background-color: #f5f5f5; } /* Visual cue for older orders */
        .order-header { border-bottom: 1px solid #e0e0e0; padding-bottom: 10px; margin-bottom: 20px; }
        .order-header h4 { font-size: 1.5rem; font-weight: 700; color: #333; }
        .order-header p { font-size: 1rem; color: #666; margin: 5px 0; }
        .status-badge { display: inline-block; padding: 5px 10px; border-radius: 12px; font-size: 0.9rem; font-weight: 600; }
        .status-pending { background-color: #ffeeba; color: #856404; }
        .status-shipped { background-color: #cce5ff; color: #004085; }
        .status-delivered { background-color: #d4edda; color: #155724; }
        .order-item { display: flex; align-items: center; margin-bottom: 15px; }
        .order-item-image img { border: 1px solid #ddd; border-radius: 8px; padding: 10px; max-width: 80px; height: auto; }
        .order-item-details { flex-grow: 1; padding-left: 20px; }
        .order-item-title { font-size: 1.1rem; font-weight: 600; color: #333; margin-bottom: 5px; }
        .order-item-info { font-size: 0.9rem; color: #666; margin-bottom: 5px; }
        .order-item-price { font-size: 1rem; font-weight: 600; color: #28a745; }
        .order-total { text-align: right; font-size: 1.25rem; font-weight: 700; color: #333; margin-top: 15px; }
        .no-orders { text-align: center; font-size: 1.25rem; color: #666; padding: 40px 0; }
        .btn-primary { background-color: #28a745; color: #fff; padding: 12px 30px; border: none; border-radius: 8px; font-weight: 700; font-size: 1.1rem; text-decoration: none; transition: background-color 0.3s ease; display: inline-block; margin-top: 20px; }
        .btn-primary:hover { background-color: #218838; }
        @media (max-width: 768px) { 
            .order-item { flex-direction: column; text-align: center; } 
            .order-item-details { padding-left: 0; margin-top: 15px; } 
            .order-item-image img { max-width: 60px; } 
            .order-header h4 { font-size: 1.25rem; } 
            .order-total { text-align: center; font-size: 1.1rem; } 
            .btn-primary { padding: 10px 20px; font-size: 1rem; } 
        }
        .notification { 
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #dc3545; /* Red for errors */
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    z-index: 10000;
    display: none;
    text-align: center;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}
.notification.show { display: block; }
.notification button {
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
    position: absolute;
    top: 5px;
    right: 10px;
}

    </style>
</head>
<body>
    <section class="product-store">
        <div class="container-md">
            <div class="display-header">
                <h2 class="section-title text-uppercase">Your Orders</h2>
            </div>
            <div class="orders-content">
                <?php if (empty($orders)): ?>
                    <p class="no-orders">You have no orders yet. Start shopping now!</p>
                <?php else: ?>
                    <?php 
                    $currentDate = new DateTime();
                    foreach ($orders as $order): 
                        $orderDate = new DateTime($order['order_date']);
                        $interval = $currentDate->diff($orderDate);
                        $isOldOrder = $interval->days > 30; // Consider orders older than 30 days as "old"
                    ?>
                        <div class="order-card <?php echo $isOldOrder ? 'old-order' : ''; ?>">
                            <div class="order-header">
                                <h4>Order #<?php echo htmlspecialchars($order['order_id']); ?></h4>
                                <p>Placed on: <?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>
                                <p>Username: <?php echo htmlspecialchars($order['username'] ?? 'N/A'); ?></p>
                                <p>Shipping Address: <?php echo htmlspecialchars($order['shipping_address'] ?? 'Not provided'); ?></p>
                                <p>Status: <span class="status-badge status-<?php echo strtolower($order['status'] ?? 'pending'); ?>">
                                    <?php echo htmlspecialchars($order['status'] ?? 'Pending'); ?>
                                </span></p>
                            </div>
                            <?php
                            try {
                                $stmt = $pdo->prepare("
                                    SELECT oi.product_id, oi.quantity, oi.price, oi.size, oi.color, p.title, p.image 
                                    FROM order_items oi 
                                    LEFT JOIN products p ON oi.product_id = p.id 
                                    WHERE oi.order_id = ?
                                ");
                                $stmt->execute([$order['order_id']]);
                                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                error_log("Error fetching order items: " . $e->getMessage());
                                $items = [];
                            }

                            if (empty($items)) {
                                echo '<p class="order-item-info">No items found for this order.</p>';
                            } else {
                                foreach ($items as $item): ?>
                                    <div class="order-item">
                                        <div class="order-item-image">
                                            <img src="../assets/images/products/<?php echo htmlspecialchars($item['image'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($item['title'] ?? 'No title'); ?>" class="img-fluid">
                                        </div>
                                        <div class="order-item-details">
                                            <h5 class="order-item-title"><?php echo htmlspecialchars($item['title'] ?? 'No title'); ?></h5>
                                            <div class="order-item-info">
                                                Size: <?php echo htmlspecialchars($item['size'] ?? 'N/A'); ?> | 
                                                Color: <?php echo htmlspecialchars($item['color'] ?? 'N/A'); ?> | 
                                                Quantity: <?php echo htmlspecialchars($item['quantity'] ?? 0); ?>
                                            </div>
                                            <span class="order-item-price">$<?php echo number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 0), 2); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach;
                            }
                            ?>
                            <div class="order-total">
                                Total: $<?php echo number_format($order['total_amount'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <a href="/" class="btn-primary">Back to Home</a>
            </div>
        </div>
    </section>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>