<?php
session_start();
require_once '../includes/db_config.php';
include_once '../includes/header.php';
include_once '../includes/preloader.php';
include_once '../includes/svg_symbols.php';
include_once '../includes/modals.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
try {
    $stmt = $pdo->prepare("SELECT full_name, username, email, phone_number, birth_date, gender FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_destroy();
        header("Location: ../users/login.php");
        exit;
    }

    // Set full_name in session for use across the site
    $_SESSION['full_name'] = $user['full_name'] ?? 'N/A';

} catch (Exception $e) {
    error_log("Profile fetch failed: " . $e->getMessage());
    $user = [
        'full_name' => 'Unknown User',
        'username' => 'N/A',
        'email' => 'N/A',
        'phone_number' => 'N/A',
        'birth_date' => 'N/A',
        'gender' => 'N/A'
    ];
    // Set fallback full_name in session
    $_SESSION['full_name'] = $user['full_name'];
}
?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Hide the preloader when the page has fully loaded
        document.querySelector(".preloader").style.display = "none";
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelector(".preloader").style.display = "none";
    });
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - ECWEB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="icon" href="../assets/images/logo/favicon.png" type="image/png">
    <link rel="stylesheet" href="../assets/css/vendor.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,900;1,900&family=Source+Sans+Pro:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        .btn-primary {
            background-color: rgb(235, 51, 79);
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            transition: background-color 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        .btn-primary:hover {
            background-color: rgb(24, 161, 220);
        }
        .btn-primary:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgb(136, 5, 13);
            transition: left 0.3s ease;
            z-index: -1;
        }
        .btn-primary:hover:before {
            left: 0;
        }
        @media (max-width: 768px) {
            .btn-primary {
                padding: 10px 20px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">User Profile</h2>
        <div class="card w-50 mx-auto">
            <div class="card-body">
                <h5>Welcome <?php echo htmlspecialchars($user['full_name']); ?></h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Username: <?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></li>
                    <li class="list-group-item">Email: <?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></li>
                    <li class="list-group-item">Phone: <?php echo htmlspecialchars($user['phone_number'] ?? 'N/A'); ?></li>
                    <li class="list-group-item">Birth Date: <?php echo htmlspecialchars($user['birth_date'] ?? 'N/A'); ?></li>
                    <li class="list-group-item">Gender: <?php echo htmlspecialchars($user['gender'] ?? 'N/A'); ?></li>
                </ul>
                <div class="mt-3">
                    <a href="../users/logout.php" class="btn btn-primary">Logout</a>
                    <a href="update_profile.php" class="btn btn-primary ms-2">Update Profile</a>
                </div>
                <?php if (isset($_GET['updated'])): ?>
                    <div class="alert alert-success mt-3">Profile updated successfully!</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include_once '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>