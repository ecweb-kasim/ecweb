<?php 
include_once '../includes/header.php'; 
include_once '../includes/preloader.php';
include_once '../includes/svg_symbols.php'; 
include_once '../includes/modals.php'; 

$conn = new mysqli("localhost", "your_username", "your_password", "ecweb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$order_id = $_GET['order_id'];
$sql = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
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
    <title>Order Details - KaSim Store</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/images/logo/favicon.png" type="image/png">
    <link rel="stylesheet" href="../assets/css/vendor.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,900;1,900&family=Source+Sans+Pro:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        .order-details-content { padding: 40px 0; background-color: #f9f9f9; }
        .order-item-detail { 
            border: 1px solid #e0e0e0; 
            padding: 15px; 
            border-radius: 12px; 
            background-color: #fff; 
            margin-bottom: 15px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .order-item-detail p { margin: 5px 0; }
        .order-total { font-size: 1.1rem; font-weight: 600; color: #28a745; }
        @media (max-width: 768px) {
            .order-item-detail { text-align: center; }
        }
    </style>
</head>
<body>
    <section class="product-store">
        <div class="container-md">
            <div class="display-header">
                <h2 class="section-title text-uppercase">Order Details</h2>
            </div>
            <div class="order-details-content">
                <div class="row">
                    <div class="col-12">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($item = $result->fetch_assoc()): ?>
                                <div class="order-item-detail">
                                    <p>Product: <?php echo $item['product_title']; ?></p>
                                    <p>Size: <?php echo $item['size']; ?></p>
                                    <p>Color: <?php echo $item['color']; ?></p>
                                    <p>Quantity: <?php echo $item['quantity']; ?></p>
                                    <p>Price: <span class="order-total">$<?php echo number_format($item['price'], 2); ?></span></p>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-center">No items found for this order.</p>
                        <?php endif; ?>
                        <a href="orders.php" class="btn btn-primary" style="background-color: #28a745; color: #fff; padding: 8px 15px; border: none; border-radius: 8px;">Back to Orders</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
    <?php $stmt->close(); $conn->close(); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>