<?php
include_once '../includes/db_config.php';
include_once '../includes/header.php';
include_once '../includes/preloader.php';
include_once '../includes/svg_symbols.php';
include_once '../includes/modals.php';

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Fetch all about us content
    $stmt = $pdo->query("SELECT * FROM about_us ORDER BY section, id");
    $aboutData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage(), 3, "../logs/errors.log");
    $aboutData = [];
    $errorMessage = "Oops! We couldn’t load the About Us content right now.";
}

// Scan the logo folder for images
$logoFolder = '../assets/images/logo/';
$logoFiles = glob($logoFolder . '*.{png,jpg,jpeg}', GLOB_BRACE);

// Get the most recent image or the first one
$logoPath = !empty($logoFiles) ? $logoFiles[0] : '../assets/images/logo/default-logo.png'; // Fallback image
if (!empty($logoFiles)) {
    usort($logoFiles, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    $logoPath = $logoFiles[0];
}
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
    <title>About Us - KaSim Store</title>
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
        .about-container {
            padding: 60px 20px;
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.85), rgba(224, 231, 255, 0.85)), 
                        url('https://images.unsplash.com/photo-1555529669-226fbbf0f422?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80') center/cover no-repeat;
            background-blend-mode: overlay;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        .about-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.6);
            z-index: 0;
        }
        .about-header, .profile-section, .no-image-content, .error-message, .profile-card, .cta-button {
            position: relative;
            z-index: 1;
        }
        .logo {
            max-width: 200px;
            margin: 0 auto 20px;
            display: block;
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.2));
        }
        .about-header {
            margin-bottom: 50px;
        }
        .about-header h1 {
            font-size: 3rem;
            color: #1a2e44;
            font-family: 'Playfair Display', serif;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.8);
        }
        .about-header p {
            font-size: 1.3rem;
            color: #2c3e50;
            font-family: 'Source Sans Pro', sans-serif;
            max-width: 700px;
            margin: 0 auto;
            text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.8);
        }
        .profile-section {
            margin-bottom: 60px;
        }
        .profile-section h2 {
            font-size: 2.2rem;
            color: #1a2e44;
            margin-bottom: 40px;
            font-family: 'Inter', sans-serif;
            position: relative;
            text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.8);
        }
        .profile-section h2::after {
            content: '';
            width: 50px;
            height: 3px;
            background: #3498db;
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }
        .profile-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
            justify-items: center;
            padding: 0 20px;
        }
        .profile-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        .profile-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(52, 152, 219, 0.3);
        }
        .profile-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(52, 152, 219, 0.15) 0%, transparent 70%);
            z-index: 0;
            animation: rotate 15s linear infinite;
        }
        .profile-card img {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 4px solid transparent;
            background: linear-gradient(45deg, #3498db, #8e44ad) border-box;
            -webkit-background-clip: padding-box;
            background-clip: padding-box;
            box-shadow: 0 0 12px rgba(52, 152, 219, 0.5);
            transition: transform 0.3s ease;
            position: relative;
            z-index: 1;
        }
        .profile-card:hover img {
            transform: scale(1.1);
        }
        .profile-card h4 {
            font-size: 1.6rem;
            color: #1a2e44;
            margin-bottom: 10px;
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.8);
        }
        .profile-card .role {
            font-size: 1.2rem;
            color: #7f8c8d;
            margin-bottom: 15px;
            font-family: 'Source Sans Pro', sans-serif;
            font-style: italic;
            text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.8);
        }
        .profile-card .content {
            font-size: 1rem;
            color: #666;
            line-height: 1.7;
            font-family: 'Source Sans Pro', sans-serif;
            padding: 0 10px;
            text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.8);
        }
        .no-image-content {
            margin-bottom: 50px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
        }
        .no-image-content h4 {
            font-size: 2.2rem;
            color: #1a2e44;
            font-family: 'Inter', sans-serif;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.8);
        }
        .no-image-content .role {
            font-size: 1.3rem;
            color: #34495e;
            font-family: 'Source Sans Pro', sans-serif;
            margin-bottom: 15px;
            font-style: italic;
            text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.8);
        }
        .no-image-content .content {
            font-size: 1.3rem;
            color: #34495e;
            font-family: 'Source Sans Pro', sans-serif;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.7;
            text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.8);
        }
        .error-message {
            font-size: 1.2rem;
            color: #e74c3c;
            margin: 20px 0;
            font-weight: bold;
            text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.8);
        }
        .cta-button {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            margin-top: 40px;
            transition: background 0.3s ease;
            text-shadow: none;
        }
        .cta-button:hover {
            background: #2980b9;
            color: white;
        }
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @media (min-width: 768px) {
            .profile-card img { width: 200px; height: 200px; }
        }
        @media (max-width: 768px) {
            .about-container { padding: 40px 15px; }
            .about-header h1 { font-size: 2.2rem; }
            .about-header p { font-size: 1.1rem; }
            .profile-section h2 { font-size: 1.8rem; }
            .profile-section h2::after { width: 30px; }
            .profile-card { padding: 20px; }
            .profile-card img { width: 120px; height: 120px; }
            .profile-card h4 { font-size: 1.3rem; }
            .profile-card .role { font-size: 1rem; }
            .profile-card .content { font-size: 0.9rem; }
            .profile-cards { gap: 25px; padding: 0 10px; }
            .no-image-content h4 { font-size: 1.8rem; }
            .no-image-content .role { font-size: 1.1rem; }
            .no-image-content .content { font-size: 1.1rem; }
        }
        @media (max-width: 480px) {
            .profile-cards { grid-template-columns: 1fr; }
            .about-header h1 { font-size: 1.8rem; }
            .profile-section h2 { font-size: 1.5rem; }
            .no-image-content h4 { font-size: 1.5rem; }
            .logo { max-width: 150px; }
        }
    </style>
</head>
<body>
    <div class="notification" id="notification"></div>

    <!-- About Us Section -->
    <section class="about-container">
        <?php
        // Dynamic logo section
        echo '<div class="about-header">';
        echo '<img src="' . htmlspecialchars($logoPath) . '" alt="KaSim Store Logo" class="logo">';
        echo '<h1 class="section-title text-uppercase">About Us</h1>';
        echo '<p class="lead">Welcome to KaSim Store – Your Destination for Quality Footwear!</p>';
        echo '</div>';
        ?>

        <?php
        if (!empty($errorMessage)) {
            echo "<p class='error-message'>" . htmlspecialchars($errorMessage) . "</p>";
        } else {
            // Group data by section
            $sections = [];
            foreach ($aboutData as $item) {
                $sections[$item['section']][] = $item;
            }

            // Display each section
            foreach ($sections as $sectionName => $items) {
                echo "<div class='profile-section'>";
                echo "<h2>" . htmlspecialchars($sectionName) . "</h2>";
                
                // Check if any items in this section have images
                $hasImages = false;
                foreach ($items as $item) {
                    if (!empty($item['image_path']) && file_exists($item['image_path'])) {
                        $hasImages = true;
                        break;
                    }
                }

                // If there are images in this section, use profile cards
                if ($hasImages) {
                    echo "<div class='profile-cards'>";
                    foreach ($items as $item) {
                        $imagePath = !empty($item['image_path']) && file_exists($item['image_path'])
                            ? htmlspecialchars($item['image_path'])
                            : null;

                        if ($imagePath) {
                            echo "<div class='profile-card'>";
                            echo "<img src='$imagePath' alt='" . htmlspecialchars($item['name'] ?? $sectionName) . "'>";
                            echo "<h4>" . htmlspecialchars($item['name'] ?? $sectionName) . "</h4>";
                            if (!empty($item['role']) && $item['role'] !== 'N/A') {
                                echo "<p class='role'>" . htmlspecialchars($item['role']) . "</p>";
                            }
                            echo "<p class='content'>" . htmlspecialchars($item['content'] ?? 'No content available.') . "</p>";
                            echo "</div>";
                        } else {
                            echo "</div>";
                            echo "<div class='no-image-content'>";
                            echo "<h4>" . htmlspecialchars($item['name'] ?? $sectionName) . "</h4>";
                            if (!empty($item['role']) && $item['role'] !== 'N/A') {
                                echo "<p class='role'>" . htmlspecialchars($item['role']) . "</p>";
                            }
                            echo "<p class='content'>" . htmlspecialchars($item['content'] ?? 'No content available.') . "</p>";
                            echo "</div>";
                            echo "<div class='profile-cards'>";
                        }
                    }
                    echo "</div>";
                } else {
                    foreach ($items as $item) {
                        echo "<div class='no-image-content'>";
                        echo "<h4>" . htmlspecialchars($item['name'] ?? $sectionName) . "</h4>";
                        if (!empty($item['role']) && $item['role'] !== 'N/A') {
                            echo "<p class='role'>" . htmlspecialchars($item['role']) . "</p>";
                        }
                        echo "<p class='content'>" . htmlspecialchars($item['content'] ?? 'No content available.') . "</p>";
                        echo "</div>";
                    }
                }
                echo "</div>";
            }
            // Add CTA button
            echo "<a href='shop.php' class='cta-button'>Explore Our Products</a>";
        }
        ?>
    </section>

    <?php include_once '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const preloader = document.querySelector(".preloader");
            if (preloader) preloader.style.display = "none";

            Swal.fire({
                title: "Welcome to KaSim Store!",
                text: "Learn more about our story, mission, and team.",
                icon: "info",
                confirmButtonText: "Explore",
                confirmButtonColor: "#3498db",
                timer: 5000,
                timerProgressBar: true
            });

            const images = document.querySelectorAll('.profile-card img');
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src || img.src;
                            observer.unobserve(img);
                        }
                    });
                });
                images.forEach(img => {
                    img.dataset.src = img.src;
                    img.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==';
                    observer.observe(img);
                });
            }
        });
    </script>
</body>
</html>