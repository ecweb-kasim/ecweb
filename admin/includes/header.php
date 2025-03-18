<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></title>
    <!-- Main Admin CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">
    <!-- Sidebar Custom CSS -->
    <link href="assets/css/sidebar.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Sidebar JS -->
    <script src="../assets/js/sidebar.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</head>
<body>
    <?php
    // Enable error reporting for debugging
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Include database configuration (assuming config.php is in the same directory or adjust the path)
    require_once __DIR__ . '/config.php';

    // Debug: Check if config.php is loaded
    if (!class_exists('Database')) {
        die("Database class not found. Check if config.php is correctly included in header.php.");
    }

    // Initialize database connection
    $database = new Database();
    $pdo = $database->getConnection();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Debug: Check if PDO connection is successful
    if ($pdo === null) {
        die("Failed to connect to the database in header.php. Check config.php.");
    }

    // Fetch the logo from the database
    $stmt = $pdo->prepare("SELECT logo_value FROM logo WHERE logo_key = 'logo_image' LIMIT 1");
    $stmt->execute();
    $logo = $stmt->fetchColumn();

    // Debugging: Log the raw database value
    error_log("Debug: Raw logo_value from database: " . var_export($logo, true));

    // Determine the logo image path
    $base_logo_path = "../assets/images/logo/"; // Base path for logo files
    if (!empty($logo) && is_string($logo)) {
        // Check if $logo is a filename or full path
        $logo_image = (strpos($logo, '/') === false) ? $base_logo_path . $logo : $logo;
    } else {
        $logo_image = $base_logo_path . 'main-logo.png'; // Fallback
    }
    $fallback_logo = $base_logo_path . 'default_logo.png'; // Fallback path

    // Debugging: Log the final path used
    error_log("Debug: Using logo_image path: " . $logo_image);
    ?>

    <header class="admin-header">
        <div class="header-content d-flex justify-content-between align-items-center">
            <!-- Logo/Brand -->
            <div class="brand-area">
                <a href="?page=dashboard" class="d-flex align-items-center">
                    <img src="<?php echo htmlspecialchars($logo_image); ?>" alt="Logo" class="header-logo" 
                         onerror="this.src='<?php echo htmlspecialchars($fallback_logo); ?>'; console.log('Logo failed to load, using fallback: <?php echo htmlspecialchars($fallback_logo); ?>');">
                    <span class="brand-name">Admin Panel</span>
                </a>
                <!-- Hamburger menu for mobile -->
                <button class="navbar-toggler d-md-none" type="button" onclick="toggleSidebar()">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Search Bar -->
            <div class="search-bar d-none d-md-block">
                <input type="text" class="form-control" placeholder="Search admin features..." aria-label="Search">
            </div>

            <!-- Right-side controls -->
            <div class="controls-area d-flex align-items-center">
                <!-- Notification Area -->
                <div class="notification-area me-3">
                    <div class="notification-icon position-relative">
                        <i class="fas fa-bell"></i>
                        <?php
                        // Example: Fetch dynamic notification count (replace with your logic)
                        $notification_count = 3; // Replace with actual database query
                        echo '<span class="badge">' . htmlspecialchars($notification_count) . '</span>';
                        ?>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i> Admin
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="?page=profile">Profile</a></li>
                        <li><a class="dropdown-item" href="?page=settings">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="?page=logout">Logout</a></li>
                    </ul>
                </div>

                <!-- Theme Switcher -->
                <button class="btn btn-light ms-2 theme-toggle" onclick="toggleTheme()">
                    <i class="fas fa-adjust"></i>
                </button>
            </div>
        </div>
    </header>

    <style>
        /* Core Bootstrap 5.3.3 Styles (extracted and simplified for this context) */
        .d-flex { display: flex !important; }
        .justify-content-between { justify-content: space-between !important; }
        .align-items-center { align-items: center !important; }
        .d-none { display: none !important; }
        .d-md-block { display: none !important; }
        @media (min-width: 768px) { .d-md-block { display: block !important; } }
        .d-md-none { display: block !important; }
        @media (min-width: 768px) { .d-md-none { display: none !important; } }
        .form-control {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .form-control:focus {
            color: #212529;
            background-color: #fff;
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .btn {
            display: inline-block;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: center;
            text-decoration: none;
            vertical-align: middle;
            cursor: pointer;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            border-radius: 0.25rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .btn-secondary {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            color: #fff;
            background-color: #5c636a;
            border-color: #565e64;
        }
        .btn-light {
            color: #212529;
            background-color: #f8f9fa;
            border-color: #f8f9fa;
        }
        .btn-light:hover {
            color: #212529;
            background-color: #e2e6ea;
            border-color: #dae0e5;
        }
        .dropdown-toggle::after {
            display: inline-block;
            margin-left: 0.255em;
            vertical-align: 0.255em;
            content: "";
            border-top: 0.3em solid;
            border-right: 0.3em solid transparent;
            border-bottom: 0;
            border-left: 0.3em solid transparent;
        }
        .dropdown-menu {
            position: absolute;
            z-index: 1000;
            display: none;
            min-width: 10rem;
            padding: 0.5rem 0;
            margin: 0;
            font-size: 1rem;
            color: #212529;
            text-align: left;
            list-style: none;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 0.25rem;
        }
        .dropdown-menu-end { right: 0; left: auto; }
        .dropdown-item {
            display: block;
            width: 100%;
            padding: 0.25rem 1rem;
            clear: both;
            font-weight: 400;
            color: #212529;
            text-align: inherit;
            text-decoration: none;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
        }
        .dropdown-item:hover {
            color: #1e2125;
            background-color: #f8f9fa;
        }
        .dropdown-divider {
            height: 0;
            margin: 0.5rem 0;
            overflow: hidden;
            border-top: 1px solid rgba(0, 0, 0, 0.15);
        }
        .text-danger { color: #dc3545 !important; }
        .me-3 { margin-right: 1rem !important; }
        .ms-2 { margin-left: 0.5rem !important; }

        /* Header Styles */
        .admin-header {
            background: #2c3e50;
            color: #fff;
            padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-area {
            display: flex;
            align-items: center;
        }

        .header-logo {
            height: 40px;
            margin-right: 10px;
            border-radius: 5px;
        }

        .brand-name {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .navbar-toggler {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            padding: 5px;
            cursor: pointer;
        }

        .search-bar {
            max-width: 300px;
        }

        .search-bar input {
            width: 100%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .notification-area .notification-icon {
            position: relative;
        }

        .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #e74c3c;
            color: #fff;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
        }

        .controls-area {
            gap: 10px;
            display: flex;
            align-items: center;
        }

        .theme-toggle {
            padding: 5px 10px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* Responsive Adjustments */
        @media (max-width: 991px) {
            .search-bar {
                display: none;
            }

            .controls-area .dropdown {
                margin-left: 10px;
            }
        }

        @media (max-width: 576px) {
            .brand-name {
                display: none;
            }

            .header-logo {
                height: 30px;
            }
        }

        /* Dark/Light Theme */
        body.dark-theme {
            background: #1a252f;
            color: #fff;
        }

        body.dark-theme .admin-header {
            background: #1a252f;
        }

        body.dark-theme .theme-toggle {
            background: #2c3e50;
            color: #fff;
            border-color: #34495e;
        }
    </style>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const backdrop = document.querySelector('.sidebar-backdrop');
            if (sidebar && backdrop) {
                const isActive = sidebar.classList.toggle('active');
                backdrop.classList.toggle('active', isActive);
                console.log('Sidebar toggled, active:', isActive);
            } else {
                console.error('Sidebar or backdrop not found!');
            }
        }

        function toggleTheme() {
            document.body.classList.toggle('dark-theme');
            localStorage.setItem('theme', document.body.classList.contains('dark-theme') ? 'dark' : 'light');
        }

        // Load saved theme on page load
        window.onload = function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-theme');
            }
        };
    </script>
</body>
</html>