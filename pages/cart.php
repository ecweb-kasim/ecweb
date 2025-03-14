<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Start session if not already started

include_once '../includes/header.php'; 
include_once '../includes/preloader.php';
include_once '../includes/svg_symbols.php'; 
include_once '../includes/modals.php'; 
?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Hide the preloader when the page has fully loaded
        document.querySelector(".preloader").style.display = "none";
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
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
                    // Redirect to login page
                    window.location.href = '/users/login.php?returnUrl=" . urlencode($_SERVER['REQUEST_URI']) . "';
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // Redirect to registration page
                    window.location.href = '/users/register.php';
                }
            });
        });
    </script>";
}
?>

<!DOCTYPE html>
<html lang="en">


<head>
    <title>Cart - KaSim Store</title>
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
        .cart-content { padding: 40px 0; background-color: #f9f9f9; }
        .cart-item { 
            border: 1px solid #e0e0e0; 
            padding: 20px; 
            border-radius: 12px; 
            background-color: #fff; 
            margin-bottom: 20px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .cart-item:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); 
        }
        .cart-item-image img { 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            padding: 10px; 
            max-width: 120px; 
            height: auto; 
        }
        .cart-item-details { flex-grow: 1; padding-left: 20px; }
        .cart-item-title { font-size: 1.25rem; font-weight: 700; color: #333; margin-bottom: 10px; }
        .cart-item-info { font-size: 1rem; color: #666; margin-bottom: 10px; }
        .quantity-price { 
            align-items: center; 
            gap: 10px; 
            display: flex; 
            flex-wrap: wrap; 
        }
        .input-group.product-qty { width: 120px; display: flex; align-items: center; }
        .input-number { 
            text-align: center; 
            width: 60px; 
            border-radius: 6px; 
            border: 1px solid #ddd; 
            padding: 5px; 
        }
        .btn-number { 
            border-radius: 6px; 
            padding: 8px 12px; 
            background-color: #f1f1f1; 
            border: 1px solid #ddd; 
            color: #333; 
        }
        .btn-number:hover { 
            background-color: #e0e0e0; 
            border-color: #ccc; 
        }
        .cart-item-price { 
            font-size: 1.1rem; 
            font-weight: 600; 
            color: #28a745; 
        }
        .remove-item { 
            padding: 8px 12px; 
            font-size: 1rem; 
            color: #dc3545; 
            background-color: transparent; 
            border: 1px solid #dc3545; 
            border-radius: 6px; 
            cursor: pointer; 
            text-decoration: none; 
            font-weight: 600; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            width: 40px; 
            height: 40px; 
        }
        .remove-item:hover { 
            color: #c82333; 
            background-color: #fee; 
            border-color: #c82333; 
        }
        .cart-total { 
            background-color: #fff; 
            padding: 30px; 
            border: 1px solid #e0e0e0; 
            border-radius: 12px; 
            margin-top: 20px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); 
        }
        .cart-total h3 { 
            font-size: 1.75rem; 
            margin-bottom: 20px; 
            color: #333; 
        }
        .btn-proceed { /* New class for Proceed to Checkout button */
            background-color: #28a745; /* Green background to match Return to Home */
            color: #fff;
            width: 100%;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s ease;
            display: inline-block; /* Ensure it behaves like a button */
        }
        .btn-proceed:hover {
            background-color: #218838; /* Darker green on hover */
        }
        @media (max-width: 768px) {
            .cart-item { flex-direction: column; text-align: center; }
            .cart-item-details { padding-left: 0; margin-top: 15px; }
            .quantity-price { justify-content: center; flex-wrap: wrap; }
            .cart-item-image img { max-width: 100px; }
            .remove-item { margin-top: 10px; }
        }
    </style>
</head>
<body>
    <section class="product-store">
        <div class="container-md">
            <div class="display-header">
                <h2 class="section-title text-uppercase">Shopping Cart</h2>
            </div>
            <div class="cart-content">
                <div class="row">
                    <div class="col-12">
                        <div class="cart-items" id="cart-items">
                            <!-- Cart items populated by JavaScript -->
                        </div>
                        <div class="cart-total mt-4">
                            <h3 class="text-uppercase">Cart Total</h3>
                            <div class="d-flex justify-content-between">
                                <span class="fs-6">Subtotal:</span>
                                <span class="price-amount amount fs-6" id="cart-total">$0.00</span>
                            </div>
                            <a href="checkout.php" class="btn-proceed hvr-sweep-to-right mt-3">Proceed to Checkout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateCartDisplay() {
                let cart;
                try {
                    cart = JSON.parse(localStorage.getItem('cart') || '[]');
                } catch (e) {
                    console.error('Error parsing cart data:', e);
                    cart = [];
                }
                console.log('Cart data in cart.php:', cart); // Debug log
                let cartHtml = '';
                let total = 0;

                if (!Array.isArray(cart) || cart.length === 0) {
                    cartHtml = '<p class="text-center">Your cart is empty.</p>';
                } else {
                    cart.forEach((item, index) => {
                        if (!item.title || !item.price || !item.quantity) {
                            console.warn(`Invalid cart item at index ${index}:`, item);
                            return; // Skip invalid items
                        }
                        cartHtml += `<div class="cart-item d-flex justify-content-between align-items-center mb-3">`;
                        cartHtml += `<div class="cart-item-image">`;
                        cartHtml += `<img src="../assets/images/products/${item.image}" alt="${item.title}" class="img-fluid" style="max-width: 120px;">`;
                        cartHtml += `</div>`;
                        cartHtml += `<div class="cart-item-details d-flex flex-column justify-content-between">`;
                        cartHtml += `<h4 class="cart-item-title">${item.title}</h4>`;
                        cartHtml += `<div class="cart-item-info">Size: ${item.size} | Color: ${item.color}</div>`;
                        cartHtml += `<div class="quantity-price d-flex align-items-center">`;
                        cartHtml += `<div class="input-group product-qty me-3">`;
                        cartHtml += `<button type="button" class="quantity-left-minus btn btn-light rounded-0 rounded-start btn-number" data-index="${index}" data-type="minus">`;
                        cartHtml += `<svg width="16" height="16"><use xlink:href="#minus"></use></svg>`;
                        cartHtml += `</button>`;
                        cartHtml += `<input type="text" class="form-control input-number quantity" value="${item.quantity}" data-index="${index}" readonly>`;
                        cartHtml += `<button type="button" class="quantity-right-plus btn btn-light rounded-0 rounded-end btn-number" data-index="${index}" data-type="plus">`;
                        cartHtml += `<svg width="16" height="16"><use xlink:href="#plus"></use></svg>`;
                        cartHtml += `</button>`;
                        cartHtml += `</div>`;
                        cartHtml += `<span class="cart-item-price">$${(item.price * item.quantity).toFixed(2)}</span>`;
                        cartHtml += `<a href="#" class="remove-item" data-index="${index}">X</a>`;
                        cartHtml += `</div>`;
                        cartHtml += `</div>`;
                        cartHtml += `</div>`;
                        total += item.price * item.quantity;
                    });
                }

                document.getElementById('cart-items').innerHTML = cartHtml;
                document.getElementById('cart-total').textContent = `$${total.toFixed(2)}`;

                // Quantity update handler
                document.querySelectorAll('.btn-number').forEach(button => {
                    button.addEventListener('click', function() {
                        const index = parseInt(this.getAttribute('data-index'));
                        const type = this.getAttribute('data-type');
                        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                        if (index >= 0 && index < cart.length) {
                            cart[index].quantity = Math.max(1, cart[index].quantity + (type === 'plus' ? 1 : -1));
                            localStorage.setItem('cart', JSON.stringify(cart));
                            updateCartDisplay();
                            window.dispatchEvent(new Event('cartUpdated'));
                        }
                    });
                });

                // Remove item handler
                document.querySelectorAll('.remove-item').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const index = parseInt(this.getAttribute('data-index'));
                        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                        if (index >= 0 && index < cart.length) {
                            cart.splice(index, 1);
                            localStorage.setItem('cart', JSON.stringify(cart));
                            updateCartDisplay();
                            window.dispatchEvent(new Event('cartUpdated'));
                        }
                    });
                });
            }

            updateCartDisplay();
        });
    </script>


</body>
</html>