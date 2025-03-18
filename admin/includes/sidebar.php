<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
require_once __DIR__ . '/config.php';

// Debug: Check if config.php is loaded
if (!class_exists('Database')) {
    die("Database class not found. Check if config.php is correctly included in sidebar.php.");
}

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Debug: Check if PDO connection is successful
if ($pdo === null) {
    die("Failed to connect to the database in sidebar.php. Check config.php.");
}
?>

<!-- Add a hamburger toggle button for small screens -->
<button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>

<nav class="sidebar">
    <ul class="sidebar-menu">
        <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'dashboard') ? 'active' : ''; ?>">
            <a href="?page=dashboard" class="menu-item"><i class="bi bi-speedometer2"></i> Dashboard</a>
        </li>
        <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'products') ? 'active' : ''; ?>">
            <a href="?page=products" class="menu-item"><i class="bi bi-box"></i> Products</a>
        </li>
        <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'orders') ? 'active' : ''; ?>">
            <a href="?page=orders" class="menu-item"><i class="bi bi-cart"></i> Orders</a>
        </li>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle menu-item d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
                <span><i class="bi bi-gear"></i> Settings</span>
                <i class="bi bi-caret-down-fill"></i>
            </a>
            <ul class="dropdown-menu">
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'logo') ? 'active' : ''; ?>">
                    <a href="?page=logo" class="submenu-item"><i class="bi bi-image"></i> Logo</a>
                </li>
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'slides') ? 'active' : ''; ?>">
                    <a href="?page=slides" class="submenu-item"><i class="bi bi-images"></i> Slides</a>
                </li>
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'collections') ? 'active' : ''; ?>">
                    <a href="?page=collections" class="submenu-item"><i class="bi bi-collection"></i> Collections</a>
                </li>
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'discounts') ? 'active' : ''; ?>">
                    <a href="?page=discounts" class="submenu-item"><i class="bi bi-percent"></i> Discounts</a>
                </li>
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'special_offer') ? 'active' : ''; ?>">
                    <a href="?page=special_offer" class="submenu-item"><i class="bi bi-gift"></i> Special Offer</a>
                </li>
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'social_links') ? 'active' : ''; ?>">
                    <a href="?page=social_links" class="submenu-item"><i class="bi bi-link-45deg"></i> Social Links</a>
                </li>
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'users') ? 'active' : ''; ?>">
                    <a href="?page=users" class="submenu-item"><i class="bi bi-people"></i> Users</a>
                </li>
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'about_us') ? 'active' : ''; ?>">
                    <a href="?page=about_us" class="submenu-item"><i class="bi bi-info-circle"></i> About Us</a>
                </li>
                <li class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'contact') ? 'active' : ''; ?>">
                    <a href="?page=contact" class="submenu-item"><i class="bi bi-link-45deg"></i> Manage Contact</a>
                </li>
            </ul>
        </li>
    </ul>
</nav>

<style>
/* Base styles for large screens */
.sidebar {
    width: 250px;
    height: 100vh;
    background: linear-gradient(135deg, #2c3e50, #3498db);
    color: #fff;
    position: fixed;
    left: 0;
    top: 0;
    padding-top: 80px; /* Space for header */
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    z-index: 900; /* Below header (1000) */
}

.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-item {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    color: #fff;
    text-decoration: none;
    font-size: 16px;
    transition: all 0.3s ease;
}

.menu-item i {
    margin-right: 10px;
    font-size: 18px;
}

.menu-item:hover, .active .menu-item {
    background: rgba(255, 255, 255, 0.2);
    border-left: 4px solid #e74c3c;
    padding-left: 16px;
}

.dropdown-toggle {
    width: 100%;
}

.dropdown-menu {
    background: #34495e;
    border: none;
    border-radius: 0;
    padding: 0;
    margin: 0;
    z-index: 950; /* Below header but above sidebar */
}

.submenu-item {
    padding: 10px 20px;
    color: #fff;
    text-decoration: none;
    display: block;
    font-size: 14px;
    transition: all 0.3s ease;
}

.submenu-item i {
    margin-right: 10px;
}

.submenu-item:hover, .active .submenu-item {
    background: rgba(255, 255, 255, 0.1);
    padding-left: 25px;
}

/* Toggle button for small screens */
.sidebar-toggle {
    display: none; /* Hidden on large screens */
    position: fixed;
    top: 10px;
    left: 10px;
    z-index: 1100; /* Above header (1000) */
    background: #3498db;
    color: #fff;
    border: none;
    padding: 10px 15px;
    font-size: 20px;
    cursor: pointer;
    border-radius: 5px;
}

/* Responsive adjustments */
@media (max-width: 991px) {
    .sidebar {
        width: 200px; /* Slightly narrower for medium screens */
        left: -200px; /* Hidden by default */
        padding-top: 60px; /* Space for header */
    }

    .sidebar.active {
        left: 0; /* Slide in when active */
    }

    .sidebar-toggle {
        display: block; /* Show toggle button */
    }

    .menu-item {
        padding: 12px 15px; /* Slightly smaller padding */
        font-size: 14px;
    }

    .submenu-item {
        font-size: 13px;
        padding: 8px 15px;
    }
}

@media (max-width: 576px) {
    .sidebar {
        width: 100%; /* Full width on very small screens */
        left: -100%; /* Hidden by default */
        top: 0;
        padding-top: 60px; /* Space for header */
    }

    .sidebar.active {
        left: 0;
    }
}
</style>

<script>
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('active');
}
</script>