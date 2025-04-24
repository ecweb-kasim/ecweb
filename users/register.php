<?php
session_start();

// Process form submission before any output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/db_config.php'; // Include database config only when needed

    $errors = [];
    $form_data = [
        'full_name' => htmlspecialchars(trim($_POST['full_name'] ?? '')),
        'username' => htmlspecialchars(trim($_POST['username'] ?? '')),
        'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '',
        'phone_number' => htmlspecialchars(trim($_POST['phone_number'] ?? '')),
        'birth_date' => htmlspecialchars(trim($_POST['birth_date'] ?? '')),
        'gender' => htmlspecialchars(trim($_POST['gender'] ?? ''))
    ];

    try {
        $full_name = htmlspecialchars(trim($_POST['full_name']));
        $username = htmlspecialchars(trim($_POST['username']));
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $phone_number = htmlspecialchars(trim($_POST['phone_number']));
        $birth_date = htmlspecialchars(trim($_POST['birth_date']));
        $gender = htmlspecialchars(trim($_POST['gender']));
        $password = trim($_POST['password']);
        $created_at = date('Y-m-d H:i:s');

        // Validation logic
        if (empty($full_name) || empty($username) || empty($email) || empty($phone_number) || empty($birth_date) || empty($gender) || empty($password)) {
            $errors[] = "All fields are required.";
        }
        if (!preg_match("/^[a-zA-Z\s]+$/", $full_name)) {
            $errors[] = "Full name can only contain letters and spaces.";
        }
        if (!preg_match("/^[a-zA-Z0-9]{3,20}$/", $username)) {
            $errors[] = "Username must be 3-20 characters long and can only contain letters and numbers.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address.";
        }
        if (!preg_match("/^[0-9]{10,15}$/", $phone_number)) {
            $errors[] = "Phone number must be 10-15 digits.";
        }
        $birth_date_obj = DateTime::createFromFormat('Y-m-d', $birth_date);
        $min_age_date = (new DateTime())->modify('-13 years');
        if (!$birth_date_obj || $birth_date_obj > $min_age_date) {
            $errors[] = "You must be at least 13 years old to register.";
        }
        $valid_genders = ['male', 'female'];
        if (!in_array($gender, $valid_genders)) {
            $errors[] = "Invalid gender selection. Choose 'male' or 'female'.";
        }
        if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
            $errors[] = "Password must be at least 8 characters long and contain at least one letter and one number.";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            error_log("Registration failed: " . implode(", ", $errors));
            header("Location: register.php");
            exit;
        }

        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $_SESSION['errors'] = ["Username or email already exists."];
            error_log("Registration failed: user exists");
            header("Location: register.php");
            exit;
        }

        // Hash the password and insert new user
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, phone_number, birth_date, gender, password_hash, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $username, $email, $phone_number, $birth_date, $gender, $password_hash, $created_at]);

        $user_id = $pdo->lastInsertId();
        error_log("Registration successful for user_id=$user_id");

        // Redirect to login.php instead of logging in
        header("Location: login.php");
        exit;

    } catch (Exception $e) {
        error_log("Registration failed: " . $e->getMessage());
        $_SESSION['errors'] = ["An unexpected error occurred. Please try again later."];
        header("Location: register.php");
        exit;
    }
}

// Load includes and form data for GET requests
require_once '../includes/db_config.php';
include '../includes/svg_symbols.php';

$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
if (!empty($errors)) {
    unset($_SESSION['errors']);
}
$form_data = [
    'full_name' => htmlspecialchars(trim($_POST['full_name'] ?? '')),
    'username' => htmlspecialchars(trim($_POST['username'] ?? '')),
    'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '',
    'phone_number' => htmlspecialchars(trim($_POST['phone_number'] ?? '')),
    'birth_date' => htmlspecialchars(trim($_POST['birth_date'] ?? '')),
    'gender' => htmlspecialchars(trim($_POST['gender'] ?? ''))
];
include_once '../includes/header.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ECWEB</title>
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
        .error-message {
            color: red;
            font-size: 0.9em;
        }
        /* Adding the button style from login.php */
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
            background: rgb(227, 31, 44); /* Red sweep effect */
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
    <!-- Your header HTML remains unchanged -->
    <div class="container mt-5">
        <h2 class="text-center">Register</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="w-50 mx-auto">
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($form_data['full_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($form_data['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($form_data['phone_number']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="birth_date" class="form-label">Birth Date</label>
                <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($form_data['birth_date']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="male" <?php echo $form_data['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo $form_data['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
    </div>
    <?php include_once '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>