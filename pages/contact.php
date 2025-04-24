<?php
// Enable error reporting for debugging (optional, remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../includes/db_config.php'; // Contains $pdo
include_once '../includes/header.php';
include_once '../includes/preloader.php';
include_once '../includes/svg_symbols.php';
include_once '../includes/modals.php';

// Fetch social media links from database
$stmt = $pdo->query("SELECT platform, url FROM social_media_links");
$social_links = $stmt->fetchAll(PDO::FETCH_ASSOC);
$links = array_column($social_links, 'url', 'platform');

// Fetch contact details from database
$stmt = $pdo->query("SELECT email, phone, map_link FROM contact_details LIMIT 1");
$contact_details = $stmt->fetch(PDO::FETCH_ASSOC);
$email = $contact_details['email'] ?? 'sales@shoestore.com';
$phone = $contact_details['phone'] ?? '+1 (123) 456-7890';
$map_link = $contact_details['map_link'] ?? '#';
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
    <title>Contact to Sale - KASIM Shoe Store</title>
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
    <!-- Add Font Awesome CDN for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #fff;
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .contact-section {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 300px);
            padding: 40px 20px;
        }

        .contact-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5em;
            color: #333;
            margin-bottom: 10px;
        }

        .tagline {
            font-family: 'Source Sans Pro', sans-serif;
            font-size: 1.2em;
            color: #777;
            margin-bottom: 30px;
        }

        .contact-details {
            margin-bottom: 20px;
            text-align: left;
        }

        .contact-details p {
            font-family: 'Inter', sans-serif;
            font-size: 1em;
            color: #555;
            margin-bottom: 10px;
        }

        .contact-details a {
            color: #ff5733;
            text-decoration: none;
        }

        .contact-details a:hover {
            text-decoration: underline;
        }

        .contact-info {
            margin-top: 30px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9em;
            color: #666;
        }

        .contact-info a {
            color: #ff5733;
            text-decoration: none;
            margin: 0 10px;
            font-size: 1.5em;
            transition: color 0.3s;
        }

        .contact-info a:hover {
            color: #e04e2d;
        }

        @media (max-width: 600px) {
            .contact-container {
                padding: 20px;
                margin: 20px;
            }
            h1 {
                font-size: 2em;
            }
            .tagline {
                font-size: 1em;
            }
            .contact-info a {
                font-size: 1.2em;
                margin: 0 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Contact Section -->
    <div class="contact-section">
        <div class="contact-container">
            <h1>Contact to Sale</h1>
            <p class="tagline">Get in touch with our sales team for exclusive offers!</p>

            <div class="contact-details">
                <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($email); ?>"><?php echo htmlspecialchars($email); ?></a></p>
                <p><strong>Phone:</strong> <a href="tel:<?php echo htmlspecialchars(preg_replace('/\s+|\(|\)/', '', $phone)); ?>"><?php echo htmlspecialchars($phone); ?></a></p>
                <p><strong>Map:</strong> <a href="<?php echo htmlspecialchars($map_link); ?>" target="_blank">View on Map</a></p>
                <p><strong>Sales Page:</strong> <a href="/sale">Visit Our Sale Page</a></p>
            </div>

            <div class="contact-info">
                <p>Follow us:</p>
                <a href="<?php echo isset($links['Instagram']) ? htmlspecialchars($links['Instagram']) : '#'; ?>" target="_blank" title="Follow us on Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="<?php echo isset($links['X']) ? htmlspecialchars($links['X']) : '#'; ?>" target="_blank" title="Follow us on X">
                    <i class="fab fa-x-twitter"></i>
                </a>
                <a href="<?php echo isset($links['Telegram']) ? htmlspecialchars($links['Telegram']) : '#'; ?>" target="_blank" title="Chat with us on Telegram">
                    <i class="fab fa-telegram-plane"></i>
                </a>
                <a href="<?php echo isset($links['Facebook']) ? htmlspecialchars($links['Facebook']) : '#'; ?>" target="_blank" title="Follow us on Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="<?php echo isset($links['WhatsApp']) ? htmlspecialchars($links['WhatsApp']) : '#'; ?>" target="_blank" title="Chat with us on WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="<?php echo isset($links['TikTok']) ? htmlspecialchars($links['TikTok']) : '#'; ?>" target="_blank" title="Follow us on TikTok">
                    <i class="fab fa-tiktok"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include_once '../includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>