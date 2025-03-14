<nav class="sidebar">
    <ul>
        <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'dashboard') ? 'active' : ''; ?>">
            <a href="?page=dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
        </li>
        <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'products') ? 'active' : ''; ?>">
            <a href="?page=products"><i class="bi bi-box"></i> Products</a>
        </li>
        <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'orders') ? 'active' : ''; ?>">
            <a href="?page=orders"><i class="bi bi-cart"></i> Orders</a>
        </li>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
                <span><i class="bi bi-gear"></i> Settings</span>
                <i class="bi bi-caret-down-fill"></i>
            </a>
            <ul class="dropdown-menu">
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'logo') ? 'active' : ''; ?>">
                    <a href="?page=logo"><i class="bi bi-image"></i> Logo</a>
                </li>
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'slides') ? 'active' : ''; ?>">
                    <a href="?page=slides"><i class="bi bi-images"></i> Slides</a>
                </li>
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'collections') ? 'active' : ''; ?>">
                    <a href="?page=collections"><i class="bi bi-collection"></i> Collections</a>
                </li>
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'discounts') ? 'active' : ''; ?>">
                    <a href="?page=discounts"><i class="bi bi-percent"></i> Discounts</a>
                </li>
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'special_offer') ? 'active' : ''; ?>">
                    <a href="?page=special_offer"><i class="bi bi-gift"></i> Special Offer</a>
                </li>
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'social_links') ? 'active' : ''; ?>">
                    <a href="?page=social_links"><i class="bi bi-link-45deg"></i> Social Links</a>
                </li>
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'users') ? 'active' : ''; ?>">
                    <a href="?page=users"><i class="bi bi-people"></i> Users</a>
                </li>
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'about_us') ? 'active' : ''; ?>">
                    <a href="?page=about_us"><i class="bi bi-info-circle"></i> About Us</a>
                </li>
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'contact') ? 'active' : ''; ?>">
                   <a href="?page=contact"><i class="bi bi-link-45deg"></i> Manage Contact</a>
               </li>
            </ul>
        </li>
    </ul>
</nav>