<?php
session_start(); // Start session if not already started

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    // Redirect to login page with a return URL
    header("Location: /users/login.php?returnUrl=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

include_once '../includes/header.php'; 
include_once '../includes/preloader.php';
include_once '../includes/svg_symbols.php'; 
include_once '../includes/modals.php'; 



// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
// Fetch username from session (assuming it's set during login)
$username = $_SESSION['username'] ?? 'N/A';
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
    <title>Checkout - KaSim Store</title>
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
        .checkout-content { padding: 40px 0; background-color: #f9f9f9; }
        .checkout-item { 
            border: 1px solid #e0e0e0; 
            padding: 20px; 
            border-radius: 12px; 
            background-color: #fff; 
            margin-bottom: 20px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .checkout-item-image img { 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            padding: 10px; 
            max-width: 100px; 
            height: auto; 
        }
        .checkout-item-details { flex-grow: 1; padding-left: 20px; }
        .checkout-item-title { font-size: 1.25rem; font-weight: 700; color: #333; margin-bottom: 10px; }
        .checkout-item-info { font-size: 1rem; color: #666; margin-bottom: 10px; }
        .checkout-item-price { font-size: 1.1rem; font-weight: 600; color: #28a745; }
        .checkout-total { 
            background-color: #fff; 
            padding: 30px; 
            border: 1px solid #e0e0e0; 
            border-radius: 12px; 
            margin-top: 30px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); 
        }
        .checkout-total h3 { font-size: 1.75rem; margin-bottom: 20px; color: #333; }
        .checkout-shipping { 
            background-color: #fff; 
            padding: 30px; 
            border: 1px solid #e0e0e0; 
            border-radius: 12px; 
            margin-top: 30px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); 
        }
        #paypal-button-container { margin-top: 20px; }
        @media (max-width: 768px) {
            .checkout-item { flex-direction: column; text-align: center; }
            .checkout-item-details { padding-left: 0; margin-top: 15px; }
            .checkout-item-image img { max-width: 80px; }
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
    <!-- Notification Div -->
<div class="notification" id="notification">
    <span id="notificationText"></span>
    <button id="closeNotification">&times;</button>
</div>

    <section class="product-store">
        <div class="container-md">
            <div class="display-header">
                <h2 class="section-title text-uppercase">Checkout</h2>
            </div>
            <div class="checkout-content">
                <div class="row">
                    <div class="col-12">
                        <div class="checkout-items" id="checkout-items">
                            <!-- Checkout items populated by JavaScript -->
                        </div>
                        <div class="checkout-shipping mt-4">
                            <h3 class="text-uppercase">Shipping Information</h3>
                            <form id="shipping-form">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="shipping-address" class="form-label">Shipping Address</label>
                                    <textarea class="form-control" id="shipping-address" rows="3" required></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="checkout-total mt-4">
                            <h3 class="text-uppercase">Order Summary</h3>
                            <div class="d-flex justify-content-between">
                                <span class="fs-6">Total:</span>
                                <span class="price-amount amount fs-6" id="checkout-total">$0.00</span>
                            </div>
                            <div id="paypal-button-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- PayPal SDK with your Sandbox Client ID -->
    <script src="https://www.paypal.com/sdk/js?client-id=AbmatpXWHCOrfXmFzMR_O4Zeyr1D2oyyAeEbgg4Z_31sMUkXm7QM3rWD9ewoDe4BYhh_rLLfR-BlsF6C&currency=USD"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateCheckoutDisplay() {
                let cart;
                try {
                    cart = JSON.parse(localStorage.getItem('cart') || '[]');
                } catch (e) {
                    console.error('Error parsing cart data:', e);
                    cart = [];
                }
                console.log('Cart data in checkout.php:', cart); // Debug log
                let checkoutHtml = '';
                let total = 0;

                if (!Array.isArray(cart) || cart.length === 0) {
                    checkoutHtml = '<p class="text-center">Your cart is empty. Please add items to proceed.</p>';
                    document.getElementById('paypal-button-container').style.display = 'none';
                } else {
                    cart.forEach((item, index) => {
                        if (!item.title || !item.price || !item.quantity) {
                            console.warn(`Invalid cart item at index ${index}:`, item);
                            return; // Skip invalid items
                        }
                        checkoutHtml += `<div class="checkout-item d-flex justify-content-between align-items-center mb-3">`;
                        checkoutHtml += `<div class="checkout-item-image">`;
                        checkoutHtml += `<img src="../assets/images/products/${item.image}" alt="${item.title}" class="img-fluid" style="max-width: 100px;">`;
                        checkoutHtml += `</div>`;
                        checkoutHtml += `<div class="checkout-item-details d-flex flex-column justify-content-between">`;
                        checkoutHtml += `<h4 class="checkout-item-title">${item.title}</h4>`;
                        checkoutHtml += `<div class="checkout-item-info">Size: ${item.size} | Color: ${item.color} | Quantity: ${item.quantity}</div>`;
                        checkoutHtml += `<span class="checkout-item-price">$${(item.price * item.quantity).toFixed(2)}</span>`;
                        checkoutHtml += `</div>`;
                        checkoutHtml += `</div>`;
                        total += item.price * item.quantity;
                    });
                    document.getElementById('paypal-button-container').style.display = 'block';
                }

                document.getElementById('checkout-items').innerHTML = checkoutHtml;
                document.getElementById('checkout-total').textContent = `$${total.toFixed(2)}`;
                return total;
            }

            const totalAmount = updateCheckoutDisplay();

            // Render PayPal buttons only if there are items in the cart
            if (totalAmount > 0) {
                paypal.Buttons({
                    createOrder: function(data, actions) {
                        // Set up the transaction
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    value: totalAmount.toFixed(2), // Total amount from cart
                                    currency_code: 'USD'
                                },
                                description: 'Purchase from KaSim Store'
                            }]
                        });
                    },
                    onApprove: function(data, actions) {
                        // Capture the funds from the transaction
                        return actions.order.capture().then(function(details) {
                            // Validate shipping address
                            const shippingAddress = document.getElementById('shipping-address').value;
                            if (!shippingAddress) {
                                alert('Please provide a shipping address.');
                                return;
                            }
                            const username = document.getElementById('username').value;

                            // Send order data to the server to save in the database
                            fetch('save_order.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    cart: JSON.parse(localStorage.getItem('cart') || '[]'),
                                    payer: details.payer,
                                    total: totalAmount.toFixed(2),
                                    username: username,
                                    shippingAddress: shippingAddress
                                })
                            }).then(response => response.json()).then(data => {
                                if (data.success) {
                                    alert('Transaction completed by ' + details.payer.name.given_name + '!');
                                    localStorage.removeItem('cart');
                                    window.dispatchEvent(new Event('cartUpdated'));
                                    window.location.href = 'thank-you.php';
                                } else {
                                    alert('Error saving order: ' + data.message);
                                }
                            }).catch(err => {
                                console.error('Error saving order:', err);
                                alert('An error occurred while saving your order. Please contact support.');
                            });
                        });
                    },
                    onError: function(err) {
                        console.error('PayPal error:', err);
                        alert('An error occurred during the transaction. Please try again.');
                    }
                }).render('#paypal-button-container');
            }
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function isUserLoggedIn() {
            return <?php echo isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        }

        if (!isUserLoggedIn()) {
            const notification = document.getElementById('notification');
            notification.innerHTML = 'Please login to view your cart. <a href="/users/login.php?returnUrl=' + encodeURIComponent(window.location.href) + '">Login here</a>';
            notification.classList.add('show', 'error');

            // Close notification on button click
            document.getElementById('closeNotification').addEventListener('click', function() {
                notification.classList.remove('show');
            });

            setTimeout(() => {
                notification.classList.remove('show');
            }, 5000); // Hide after 5 seconds
        }
    });
</script>

</body>
</html>