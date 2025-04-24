<?php
require_once '../includes/db_config.php'; 

// Custom sanitization function to replace FILTER_SANITIZE_STRING
function sanitize_string($input) {
    return trim(strip_tags($input));
}

// Initialize error array and form data
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$form_data = [
    'username_or_email' => ''
];

// Process form submission only if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize and validate input
        $username_or_email = sanitize_string($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? ''); // Preserve special characters

        // Repopulate form data on error
        $form_data['username_or_email'] = $username_or_email;

        // Debugging
        error_log("Login attempt: username_or_email=$username_or_email, password=****");

        // Basic validation
        if (empty($username_or_email) || empty($password)) {
            $errors[] = "Username/email and password are required.";
        }

        // If there are validation errors, redirect back
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            error_log("Login failed: " . implode(", ", $errors));
            header("Location: login.php");
            exit;
        }

        // Prepare and execute query to find user by username or email
        $stmt = $pdo->prepare("SELECT id, username, full_name, password_hash FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username_or_email, $username_or_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debugging
        error_log("User found: " . print_r($user, true));

        // Verify password and set session
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username']; // Set username in session
            $_SESSION['full_name'] = $user['full_name'];
            error_log("Login successful for user_id=" . $user['id']);
            header("Location: ../index.php"); // Changed to index.php (since index.html might not handle PHP sessions)
            exit;
        } else {
            $_SESSION['errors'] = ["Invalid username/email or password."];
            error_log("Login failed for username_or_email=$username_or_email");
            header("Location: login.php");
            exit;
        }

    } catch (Exception $e) {
        error_log("Login failed: " . $e->getMessage());
        $_SESSION['errors'] = ["An unexpected error occurred. Please try again later."];
        header("Location: login.php");
        exit;
    }
}

// Include SVG symbols after all logic to prevent early output
include '../includes/svg_symbols.php';
include_once '../includes/header.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ECWEB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .error-message { color: red; font-size: 0.9em; }

        /* Custom btn-primary style to match thank-you.php */
        .btn-primary {
            background-color: #28a745; /* Green background */
            color: #fff; /* White text */
            padding: 12px 30px; /* Padding */
            border: none; /* No border */
            border-radius: 8px; /* Rounded corners */
            font-weight: 700; /* Bold text */
            font-size: 1.1rem; /* Slightly larger font */
            text-decoration: none; /* No underline for links */
            transition: background-color 0.3s ease; /* Smooth background color transition on hover */
            position: relative; /* For custom hover effect */
            overflow: hidden; /* For custom hover effect */
            z-index: 1; /* For custom hover effect */
        }

        .btn-primary:hover {
            background-color: #218838; /* Darker green on hover */
        }

        /* Custom sweep-to-right hover effect */
        .btn-primary:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background:rgb(227, 31, 44); /* Matches the hover background color */
            transition: left 0.3s ease;
            z-index: -1;
        }

        .btn-primary:hover:before {
            left: 0;
        }

        @media (max-width: 768px) {
            .btn-primary {
                padding: 10px 20px; /* Smaller padding on mobile */
                font-size: 1rem; /* Smaller font on mobile */
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Login</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="login.php" method="POST" class="w-50 mx-auto">
            <div class="mb-3">
                <label for="username" class="form-label">Username or Email</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($form_data['username_or_email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
    <?php include_once '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>