<?php
include_once '../includes/db_config.php';
include_once '../includes/header.php';
include_once '../includes/svg_symbols.php';
include_once '../includes/modals.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
    <link rel="icon" href="../assets/images/logo/favicon.png" type="image/png">
    <link rel="stylesheet" href="../assets/css/vendor.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,900;1,900&family=Source+Sans+Pro:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        /* Notification Styling */
        .notification { 
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
            background-color: #28a745; color: white; padding: 10px 20px; border-radius: 5px;
            z-index: 1000; display: none; text-align: center; width: 90%; max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        .notification.show { display: block; }
        .notification.error { background-color: #dc3545; }
        
        /* Product Card Styling */
        .product-card { position: relative; max-width: 300px; margin: auto; }
        .product-badge { 
            position: absolute; top: 10px; left: 10px; background-color: #dc3545; color: white;
            padding: 5px 10px; border-radius: 5px; font-size: 0.9em; font-weight: bold;
            text-transform: uppercase; z-index: 1; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .card-img { position: relative; }
        .card-img img { width: 100%; height: 250px; object-fit: cover; }
        .cart-concern { 
            position: absolute; bottom: 10px; left: 0; right: 0; 
            display: flex; justify-content: center; opacity: 0; transition: opacity 0.3s;
        }
        .product-card:hover .cart-concern { opacity: 1; }
        .cart-button { gap: 10px; }
        .cart-button button { padding: 8px; }
        .card-detail { margin-top: 10px; }
        .original-price { text-decoration: line-through; color: #888; margin-right: 5px; }
        .discounted-price { color: #dc3545; font-weight: bold; }
        .category-text, .color-text, .description-text { font-size: 0.9rem; color: #666; margin-top: 5px; }
        .color-swatches { margin-top: 5px; }
        .color-swatches span { 
            display: inline-block; width: 20px; height: 20px; border-radius: 50%; 
            margin-right: 5px; cursor: pointer; border: 2px solid #fff; box-shadow: 0 0 2px rgba(0,0,0,0.3);
        }
        
        @media (max-width: 768px) {
            .product-card { max-width: 100%; }
            .card-img img { height: 200px; }
            .product-badge { font-size: 0.8em; padding: 4px 8px; }
            .notification { width: 80%; }
        }
    </style>
</head>
<body>
    <div class="notification" id="notification"></div>

    <?php
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $originalPrice = $product['price'];
                $discount = $product['discount'] ?: 0;
                $discountedPrice = $originalPrice * (1 - $discount / 100);

                echo "<section class='product-store'>";
                echo "<div class='container-md mt-5'>";
                echo "<h1 class='section-title text-uppercase mb-4 text-center'>" . htmlspecialchars($product['title']) . "</h1>";
                echo "<div class='row justify-content-center'>";
                echo "<div class='col-12 col-md-4 mb-4 product-item' data-id='" . $product['id'] . "'>";
                echo "<div class='product-card position-relative'>";

                // Discount Badge
                if ($discount > 0) {
                    echo "<div class='product-badge'>" . number_format($discount, 0) . "% OFF</div>";
                }

                // Product Image with Buttons
                echo "<div class='card-img'>";
                echo "<img src='../assets/images/products/" . htmlspecialchars($product['image'] ?? 'default.jpg') . "' alt='" . htmlspecialchars($product['title']) . "' class='img-fluid'>";
                echo "<div class='cart-concern position-absolute d-flex justify-content-center'>";
                echo "<div class='cart-button d-flex gap-2 justify-content-center align-items-center'>";
                echo "<button type='button' class='btn btn-light add-to-cart' data-bs-toggle='modal' data-bs-target='#productModalSizeColor'>";
                echo "<svg class='shopping-carriage'><use xlink:href='#shopping-carriage'></use></svg>";
                echo "</button>";
                echo "<button type='button' class='btn btn-light quick-view' data-bs-toggle='modal' data-bs-target='#productModalSizeColor'>";
                echo "<svg class='quick-view'><use xlink:href='#quick-view'></use></svg>";
                echo "</button>";
                echo "</div></div></div>";

                // Product Details
                echo "<div class='card-detail d-flex justify-content-between align-items-center mt-3'>";
                echo "<h3 class='card-title fs-6 fw-normal m-0'>" . htmlspecialchars($product['title']) . "</h3>";
                echo "<span class='price'>";
                if ($discount > 0) {
                    echo "<span class='original-price'>$" . number_format($originalPrice, 2) . "</span>";
                    echo "<span class='discounted-price'>$" . number_format($discountedPrice, 2) . "</span>";
                } else {
                    echo "<span class='card-price fw-bold'>$" . number_format($originalPrice, 2) . "</span>";
                }
                echo "</span></div>";
                echo "<p class='category-text'>Category: " . htmlspecialchars($product['category']) . "</p>";
                echo "<p class='description-text'>Description: " . htmlspecialchars($product['description']) . "</p>";
                echo "<p class='category-text'>Sizes: " . htmlspecialchars($product['sizes']) . "</p>";
                
                // Color Display
                $colors = $product['colors'] ? explode(',', $product['colors']) : ['Red'];
                echo "<p class='color-text'>Colors:</p>";
                echo "<div class='color-swatches'>";
                foreach ($colors as $color) {
                    $color = trim($color);
                    $colorStyle = strtolower($color) === 'red' ? '#ff0000' : (strtolower($color) === 'black' ? '#000000' : '#888888'); // Default colors, adjust as needed
                    echo "<span style='background-color: $colorStyle;' data-color='$color'></span>";
                }
                echo "</div>";

                echo "</div></div>";
                echo "<div class='text-center'><a href='../index.php' class='btn btn-secondary mt-3'>Back to Home</a></div>";
                echo "</div></div></section>";
            } else {
                echo "<p class='text-center'>Product not found.</p>";
            }
        } catch (PDOException $e) {
            echo "<p class='text-center'>Error loading product: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p class='text-center'>Invalid product ID.</p>";
    }
    ?>

    <!-- Size and Color Selection Modal -->
    <div class="modal fade" id="productModalSizeColor" tabindex="-1" aria-labelledby="productModalSizeColorLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="modal-image"><img src="" alt="Product Image" id="modalImageSizeColor"></div>
                    <div class="modal-details">
                        <h4 class="modal-title" id="modalTitleSizeColor"></h4>
                        <p class="modal-price" id="modalPriceSizeColor"></p>
                        <p class="modal-description" id="modalDescriptionSizeColor"></p>
                        <div class="selection-bar">
                            <select id="modalColorSizeColor" class="form-select" required><option value="">Select Color</option></select>
                            <select id="modalSizeSizeColor" class="form-select" required><option value="">Select Size</option></select>
                        </div>
                        <div class="quantity-controls">
                            <button type="button" class="btn btn-light" id="decreaseQtySizeColor">-</button>
                            <input type="text" id="modalQuantitySizeColor" value="1" readonly class="form-control text-center">
                            <button type="button" class="btn btn-light" id="increaseQtySizeColor">+</button>
                        </div>
                        <button type="button" class="modal-add-to-cart mt-3" id="addToCartModalSizeColor">Add to Cart</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function isUserLoggedIn() {
            <?php echo 'return ' . (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) ? 'true' : 'false') . ';'; ?>
        }

        const product = <?php echo json_encode($product); ?>;

        document.querySelectorAll('.add-to-cart, .quick-view').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('modalImageSizeColor').src = '../assets/images/products/' + product.image;
                document.getElementById('modalTitleSizeColor').textContent = product.title;
                document.getElementById('modalDescriptionSizeColor').textContent = product.description || 'No description available.';
                const originalPrice = product.price;
                const discount = product.discount || 0;
                const discountedPrice = originalPrice * (1 - discount / 100);
                document.getElementById('modalPriceSizeColor').innerHTML = discount > 0
                    ? `<span class="original-price">$${number_format(originalPrice, 2)}</span> <span class="discounted-price">$${number_format(discountedPrice, 2)}</span>`
                    : `<span class="card-price fw-bold">$${number_format(originalPrice, 2)}</span>`;

                const colorSelect = document.getElementById('modalColorSizeColor');
                const sizeSelect = document.getElementById('modalSizeSizeColor');
                colorSelect.innerHTML = '<option value="">Select Color</option>';
                sizeSelect.innerHTML = '<option value="">Select Size</option>';

                const colors = product.colors ? product.colors.split(',').map(c => c.trim()) : ['Red'];
                const sizes = product.sizes ? product.sizes.split(',').map(s => s.trim()) : ['US 7'];

                colors.forEach(color => {
                    const option = document.createElement('option');
                    option.value = color;
                    option.textContent = color.charAt(0).toUpperCase() + color.slice(1);
                    colorSelect.appendChild(option);
                });

                sizes.forEach(size => {
                    const option = document.createElement('option');
                    option.value = size;
                    option.textContent = size;
                    sizeSelect.appendChild(option);
                });

                document.getElementById('addToCartModalSizeColor').onclick = function() {
                    if (!isUserLoggedIn()) {
                        Swal.fire({
                            title: 'Login Required',
                            text: 'Please login or create an account to continue.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Login',
                            cancelButtonText: 'Register'
                        }).then((result) => {
                            if (result.isConfirmed) window.location.href = '/users/login.php?returnUrl=' + encodeURIComponent(window.location.href);
                            else if (result.dismiss === Swal.DismissReason.cancel) window.location.href = '/users/register.php';
                        });
                        return;
                    }

                    const selectedColor = document.getElementById('modalColorSizeColor').value;
                    const selectedSize = document.getElementById('modalSizeSizeColor').value;
                    const quantity = parseInt(document.getElementById('modalQuantitySizeColor').value);

                    if (!selectedColor || !selectedSize) {
                        Swal.fire({ title: 'Error!', text: 'Please select both size and color!', icon: 'error', confirmButtonText: 'Ok' });
                        return;
                    }

                    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                    let existingItem = cart.find(x => x.id === product.id && x.size === selectedSize && x.color === selectedColor);
                    let priceToAdd = discount > 0 ? discountedPrice : originalPrice;

                    if (existingItem) {
                        existingItem.quantity += quantity;
                        existingItem.price = priceToAdd;
                    } else {
                        cart.push({
                            id: product.id,
                            image: product.image,
                            title: product.title,
                            price: priceToAdd,
                            quantity: quantity,
                            discount: discount,
                            category: product.category || 'Unknown',
                            color: selectedColor,
                            size: selectedSize
                        });
                    }
                    localStorage.setItem('cart', JSON.stringify(cart));
                    window.dispatchEvent(new Event('cartUpdated'));

                    Swal.fire({
                        title: 'Added to Cart!',
                        text: product.title + ' has been added to your cart.',
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    });

                    bootstrap.Modal.getInstance(document.getElementById('productModalSizeColor')).hide();
                };

                document.getElementById('increaseQtySizeColor').onclick = function() {
                    let qty = parseInt(document.getElementById('modalQuantitySizeColor').value);
                    document.getElementById('modalQuantitySizeColor').value = qty + 1;
                };
                document.getElementById('decreaseQtySizeColor').onclick = function() {
                    let qty = parseInt(document.getElementById('modalQuantitySizeColor').value);
                    if (qty > 1) document.getElementById('modalQuantitySizeColor').value = qty - 1;
                };
            });
        });

        function number_format(number, decimals, dec_point, thousands_sep) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            let n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function(n, prec) { let k = Math.pow(10, prec); return '' + Math.round(n * k) / k; };
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            if ((s[1] || '').length < prec) s[1] = s[1] || '', s[1] += new Array(prec - s[1].length + 1).join('0');
            return s.join(dec);
        }
    </script>
</body>
</html>