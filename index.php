<?php
include 'includes/header.php';
include 'includes/svg_symbols.php';
include 'includes/preloader.php';
include 'includes/modals.php';
include 'pages/intro.php';
include 'pages/discount_coupon.php';
include 'pages/featured_products.php';
include 'pages/collection_products.php';
include 'pages/latest_products.php';
include 'includes/footer.php';

// Fetch favicon from database
require_once 'includes/db_config.php';
$stmt = $pdo->prepare("SELECT logo_value FROM logo WHERE logo_key = 'favicon' LIMIT 1");
$stmt->execute();
$favicon = $stmt->fetchColumn() ?: 'favicon.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>KaSim Store</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="icon" href="assets/images/logo/<?php echo htmlspecialchars($favicon); ?>" type="image/png">
    <link rel="stylesheet" href="assets/css/vendor.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,900;1,900&family=Source+Sans+Pro:wght@400;600;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <!-- The header, SVG symbols, preloader, modals, and page content are included via PHP -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>