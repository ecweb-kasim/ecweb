<!-- Modals -->
<div class="modal fade" id="modaltoggle" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-fullscreen-md-down modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-lg-12 col-md-12 me-3">
                    <div class="image-holder">
                        <img src="assets/images/summary-item1.jpg" alt="Shoes">
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="summary">
                        <div class="summary-content fs-6">
                            <div class="product-header d-flex justify-content-between mt-4">
                                <h3 class="display-7">Running Shoes For Men</h3>
                                <div class="modal-close-btn">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                            </div>
                            <span class="product-price fs-3">$99</span>
                            <div class="product-details">
                                <p class="fs-7">Buy good shoes and a good mattress, because when you're not in one you're in the other.</p>
                            </div>
                            <ul class="select">
                                <li><strong>Colour Shown:</strong> Red, White, Black</li>
                                <li><strong>Style:</strong> SM3018-100</li>
                            </ul>
                            <div class="variations-form shopify-cart">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="quantity d-flex pb-4">
                                            <div class="qty-number qty-number-plus d-flex justify-content-center align-items-center text-center">
                                                <span class="increase-qty plus"><svg class="plus"><use xlink:href="#plus"></use></svg></span>
                                            </div>
                                            <input type="number" id="quantity_001" class="input-text text-center" step="1" min="1" name="quantity" value="1" title="Qty">
                                            <div class="qty-number qty-number-minus d-flex justify-content-center align-items-center text-center">
                                                <span class="increase-qty minus"><svg class="minus"><use xlink:href="#minus"></use></svg></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-medium btn-black hvr-sweep-to-right">Add to cart</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modallong" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-fullscreen-md-down modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5">Cart</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="shopping-cart">
                    <div class="shopping-cart-content">
                        <div class="mini-cart cart-list p-0 mt-3">
                            <div class="mini-cart-item d-flex border-bottom pb-3">
                                <div class="col-lg-2 col-md-3 col-sm-2 me-4">
                                    <a href="#" title="product-image"><img src="assets/images/single-product-thumb1.jpg" class="img-fluid" alt="single-product-item"></a>
                                </div>
                                <div class="col-lg-9 col-md-8 col-sm-8">
                                    <div class="product-header d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="product-title fs-6 me-5">Sport Shoes For Men</h4>
                                        <a href="" class="remove" aria-label="Remove this item"><svg class="close"><use xlink:href="#close"></use></svg></a>
                                    </div>
                                    <div class="quantity-price d-flex justify-content-between align-items-center">
                                        <div class="input-group product-qty">
                                            <button type="button" class="quantity-left-minus btn btn-light rounded-0 rounded-start btn-number" data-type="minus">
                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                            </button>
                                            <input type="text" name="quantity" class="form-control input-number quantity" value="1">
                                            <button type="button" class="quantity-right-plus btn btn-light rounded-0 rounded-end btn-number" data-type="plus">
                                                <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                            </button>
                                        </div>
                                        <div class="price-code"><span class="product-price fs-6">$99</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mini-cart-total d-flex justify-content-between py-4">
                            <span class="fs-6">Subtotal:</span>
                            <span class="special-price-code"><span class="price-amount amount fs-6">$99.00</span></span>
                        </div>
                        <div class="modal-footer my-4 justify-content-center">
                            <button type="button" class="btn btn-red hvr-sweep-to-right dark-sweep">View Cart</button>
                            <button type="button" class="btn btn-outline-gray hvr-sweep-to-right dark-sweep">Checkout</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modallogin" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-fullscreen-md-down modal-md modal-dialog-centered" role="document">
        <div class="modal-content p-4">
            <div class="modal-header mx-auto border-0">
                <h2 class="modal-title fs-3 fw-normal">Login</h2>
            </div>
            <div class="modal-body">
                <div class="login-detail">
                    <div class="login-form p-0">
                        <div class="col-lg-12 mx-auto">
                            <form id="login-form">
                                <input type="text" name="username" placeholder="Username or Email Address *" class="mb-3 ps-3 text-input">
                                <input type="password" name="password" placeholder="Password" class="ps-3 text-input">
                                <div class="checkbox d-flex justify-content-between mt-4">
                                    <p class="checkbox-form">
                                        <label><input name="rememberme" type="checkbox" id="remember-me" value="forever"> Remember me </label>
                                    </p>
                                    <p class="lost-password"><a href="#">Forgot your password?</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer mt-5 d-flex justify-content-center">
                        <button type="button" class="btn btn-red hvr-sweep-to-right dark-sweep" id="loginButton">Login</button>
                        <button type="button" class="btn btn-outline-gray hvr-sweep-to-right dark-sweep" data-bs-toggle="modal" data-bs-target="#modalregister">Register</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Modal for Registration -->
<div class="modal fade" id="modalregister" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-fullscreen-md-down modal-md modal-dialog-centered" role="document">
        <div class="modal-content p-4">
            <div class="modal-header mx-auto border-0">
                <h2 class="modal-title fs-3 fw-normal">Register</h2>
            </div>
            <div class="modal-body">
                <div class="login-detail">
                    <div class="login-form p-0">
                        <div class="col-lg-12 mx-auto">
                            <form id="register-form">
                                <input type="text" name="username" placeholder="Username *" class="mb-3 ps-3 text-input" required>
                                <input type="email" name="email" placeholder="Email Address *" class="mb-3 ps-3 text-input" required>
                                <input type="password" name="password" placeholder="Password *" class="ps-3 text-input" required>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer mt-5 d-flex justify-content-center">
                        <button type="button" class="btn btn-red hvr-sweep-to-right dark-sweep" id="registerButton">Register</button>
                        <button type="button" class="btn btn-outline-gray hvr-sweep-to-right dark-sweep" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Login Modal -->
<div class="modal fade" id="modallogin" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials'): ?>
                    <div class="alert alert-danger">Invalid username/email or password.</div>
                <?php endif; ?>
                <form id="login-form" action="users/login.php" method="POST">
                    <input type="text" name="username" placeholder="Username or Email Address *" class="mb-3 ps-3 text-input" required>
                    <input type="password" name="password" placeholder="Password *" class="ps-3 text-input" required>
                    <div class="checkbox d-flex justify-content-between mt-4">
                        <p class="checkbox-form">
                            <label><input name="rememberme" type="checkbox" id="remember-me" value="forever"> Remember me </label>
                        </p>
                        <p class="lost-password"><a href="#">Forgot your password?</a></p>
                    </div>
                    <button type="submit" class="btn btn-red hvr-sweep-to-right dark-sweep">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="modalregister" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Register</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (isset($_GET['error'])): ?>
                    <?php if ($_GET['error'] === 'missing_fields'): ?>
                        <div class="alert alert-danger">Please fill in all fields.</div>
                    <?php elseif ($_GET['error'] === 'invalid_email'): ?>
                        <div class="alert alert-danger">Invalid email address.</div>
                    <?php elseif ($_GET['error'] === 'user_exists'): ?>
                        <div class="alert alert-danger">Username or email already exists.</div>
                    <?php endif; ?>
                <?php endif; ?>
                <form id="register-form" action="users/register.php" method="POST">
                    <input type="text" name="full_name" placeholder="Full Name *" class="mb-3 ps-3 text-input" required>
                    <input type="text" name="username" placeholder="Username *" class="mb-3 ps-3 text-input" required>
                    <input type="email" name="email" placeholder="Email Address *" class="mb-3 ps-3 text-input" required>
                    <input type="text" name="phone_number" placeholder="Phone Number *" class="mb-3 ps-3 text-input" required>
                    <input type="date" name="birth_date" placeholder="Birth Date *" class="mb-3 ps-3 text-input" required>
                    <select name="gender" class="mb-3 ps-3 text-input" required>
                        <option value="" disabled selected>Select Gender *</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="password" name="password" placeholder="Password *" class="ps-3 text-input" required>
                    <button type="submit" class="btn btn-red hvr-sweep-to-right dark-sweep">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>