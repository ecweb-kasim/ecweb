<?php
include_once 'includes/db_config.php'; // Required for database connection
include_once 'includes/header.php';
include_once 'includes/svg_symbols.php';
include_once 'includes/modals.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Latest - KaSim Store</title>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #28a745 !important; /* Green background for success */
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            z-index: 1000;
            display: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .notification.show {
            display: block;
        }
        .notification.error {
            background-color: #dc3545 !important; /* Red background for errors */
        }
        .discounted-price {
            color: green;
            font-weight: bold;
        }
        .original-price {
            text-decoration: line-through;
            color: #888;
            margin-right: 10px;
        }
        .product-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #dc3545; /* Red background */
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9em;
            font-weight: bold;
            text-transform: uppercase;
            z-index: 1;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            display: inline-block;
        }
        @media (max-width: 768px) {
            .product-badge {
                font-size: 0.8em;
                padding: 4px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="notification" id="notification"></div> <!-- Notification div -->

    <!-- Latest Products Section -->
    <section id="latest-products" class="product-store">
        <div class="container-md">
            <div class="display-header d-flex align-items-center justify-content-between">
                <h2 class="section-title text-uppercase">Latest Products</h2>
                <div class="btn-right">
                    <a href="pages/shop.php" class="d-inline-block text-uppercase text-hover fw-bold">View all</a>
                </div>
            </div>
            <div class="product-content padding-small">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5">
                    <?php
                    try {
                        // Fetch the 5 latest products based on created_at, including discount
                        $stmt = $pdo->prepare("SELECT image, title, price, discount FROM products ORDER BY created_at DESC LIMIT 5");
                        $stmt->execute();
                        $latest_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (empty($latest_products)) {
                            echo '<p class="text-center">No latest products available.</p>';
                        } else {
                            foreach ($latest_products as $index => $product) {
                                $originalPrice = $product['price'];
                                $discount = $product['discount'] ?: 0;
                                $discountedPrice = $discount > 0 ? $originalPrice * (1 - $discount / 100) : $originalPrice;

                                echo '<div class="col mb-4">';
                                echo '<div class="product-card position-relative">';
                                // Add badge with dynamic discount percentage
                                if ($discount > 0) {
                                    echo '<div class="product-badge">' . number_format($discount, 0) . '% OFF</div>';
                                }
                                echo '<div class="card-img">';
                                echo '<img src="../assets/images/products/' . htmlspecialchars($product['image']) . '" alt="product-item" class="product-image img-fluid">';
                                echo '<div class="cart-concern position-absolute d-flex justify-content-center">';
                                echo '<div class="cart-button d-flex gap-2 justify-content-center align-items-center">';
                                echo '<button type="button" class="btn btn-light add-to-cart" data-index="' . $index . '">';
                                echo '<svg class="shopping-carriage"><use xlink:href="#shopping-carriage"></use></svg>';
                                echo '</button>';
                                echo '<button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modaltoggle">';
                                echo '<svg class="quick-view"><use xlink:href="#quick-view"></use></svg>';
                                echo '</button>';
                                echo '</div></div></div>';
                                echo '<div class="card-detail d-flex justify-content-between align-items-center mt-3">';
                                echo '<h3 class="card-title fs-6 fw-normal m-0"><a href="index.php">' . htmlspecialchars($product['title']) . '</a></h3>';
                                echo '<span class="price">';
                                if ($discount > 0) {
                                    echo '<span class="original-price">$' . number_format($originalPrice, 2) . '</span>';
                                    echo '<span class="discounted-price">$' . number_format($discountedPrice, 2) . '</span>';
                                } else {
                                    echo '<span class="card-price fw-bold">$' . number_format($originalPrice, 2) . '</span>';
                                }
                                echo '</span>';
                                echo '</div></div></div>';
                            }
                        }
                    } catch (PDOException $e) {
                        echo '<p class="text-danger">Error fetching latest products: ' . htmlspecialchars($e->getMessage()) . '</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('#latest-products').addEventListener('click', function(e) {
                if (e.target.closest('.add-to-cart')) {
                    const button = e.target.closest('.add-to-cart');
                    const index = button.getAttribute('data-index');
                    const products = <?php echo json_encode($latest_products); ?>;
                    const product = products[index];

                    const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>; // PHP variable to check if logged in

                    // If user is not logged in, show login prompt
                    if (!isLoggedIn) {
                        Swal.fire({
                            title: 'Login Required',
                            text: 'Please login or create an account to continue.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Login',
                            cancelButtonText: 'Register',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '/users/login.php?returnUrl=' + encodeURIComponent(window.location.href);
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                window.location.href = '/users/register.php';
                            }
                        });
                        return;
                    }

                    // If logged in, proceed with adding product to cart
                    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                    let existingItem = cart.find(x => x.title === product.title && x.price === product.price);
                    let priceToAdd = product.discount > 0 ? (product.price * (1 - product.discount / 100)) : product.price;
                    if (existingItem) {
                        existingItem.quantity += 1;
                        existingItem.price = priceToAdd; // Update price if discounted
                    } else {
                        cart.push({
                            image: product.image,
                            title: product.title,
                            price: priceToAdd,
                            quantity: 1,
                            discount: product.discount || 0
                        });
                    }
                    localStorage.setItem('cart', JSON.stringify(cart));

                    // Custom notification for successful addition to cart
                    Swal.fire({
                        title: 'Added to Cart!',
                        text: product.title + ' has been added to your cart.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        timer: 3000, // Automatically close after 3 seconds
                        timerProgressBar: true, // Shows a progress bar for the timer
                        willClose: () => {
                            window.dispatchEvent(new Event('cartUpdated')); // Update the cart count in header
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
