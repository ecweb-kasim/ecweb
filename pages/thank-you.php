<?php 
include '../includes/header.php'; 
include_once '../includes/preloader.php';
include '../includes/svg_symbols.php'; 
include '../includes/modals.php'; 
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
    <title>Thank You - KaSim Store</title>
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
        .thank-you-content {
            padding: 60px 0;
            background-color: #f9f9f9;
            text-align: center;
        }
        .thank-you-content h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
        }
        .thank-you-content p {
            font-size: 1.25rem;
            color: #666;
            margin-bottom: 30px;
        }
        .btn-primary {
            background-color: #28a745;
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
        .thank-you-icon {
            margin-bottom: 20px;
        }
        .thank-you-icon svg {
            width: 60px;
            height: 60px;
            fill: #28a745;
        }
        @media (max-width: 768px) {
            .thank-you-content h2 {
                font-size: 2rem;
            }
            .thank-you-content p {
                font-size: 1rem;
            }
            .btn-primary {
                padding: 10px 20px;
                font-size: 1rem;
            }
            .thank-you-icon svg {
                width: 50px;
                height: 50px;
            }
        }
    </style>
</head>
<body>
    <section class="product-store">
        <div class="container-md">
            <div class="thank-you-content">
                <div class="thank-you-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
                    </svg>
                </div>
                <h2 class="text-uppercase">Thank You for Your Purchase!</h2>
                <p>Your order has been successfully placed. You'll receive a confirmation email shortly with the details of your purchase.</p>
                <a href="orders.php" class="btn btn-primary hvr-sweep-to-right">See Your Orders</a>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>