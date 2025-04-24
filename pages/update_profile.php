<?php
session_start();
require_once '../includes/db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
        header("Location: login.php");
        exit;
    }
} catch (Exception $e) {
    error_log("Profile fetch failed: " . $e->getMessage());
    $user = ['full_name' => $_SESSION['full_name']]; // Fallback
}

// Handle form submission
$errors = [];
$form_data = $user; // Pre-fill form with current data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $full_name = htmlspecialchars(trim($_POST['full_name'] ?? ''));
        $username = htmlspecialchars(trim($_POST['username'] ?? ''));
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
        $phone_number = htmlspecialchars(trim($_POST['phone_number'] ?? ''));
        $birth_date = htmlspecialchars(trim($_POST['birth_date'] ?? ''));
        $gender = htmlspecialchars(trim($_POST['gender'] ?? ''));

        // Validation
        if (empty($full_name) || empty($username) || empty($email) || empty($phone_number) || empty($birth_date) || empty($gender)) {
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
            $errors[] = "You must be at least 13 years old.";
        }
        $valid_genders = ['male', 'female'];
        if (!in_array($gender, $valid_genders)) {
            $errors[] = "Invalid gender selection. Choose 'male' or 'female'.";
        }

        // Check for duplicates (excluding current user)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->execute([$username, $email, $user_id]);
        if ($stmt->fetch()) {
            $errors[] = "Username or email already taken by another user.";
        }

        // If no errors, update the database
        if (empty($errors)) {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, phone_number = ?, birth_date = ?, gender = ? WHERE id = ?");
            $stmt->execute([$full_name, $username, $email, $phone_number, $birth_date, $gender, $user_id]);

            // Update session
            $_SESSION['full_name'] = $full_name;
            $_SESSION['username'] = $username;

            error_log("Profile updated for user_id=$user_id");
            header("Location: profile.php?updated=1");
            exit;
        } else {
            $form_data = [
                'full_name' => $full_name,
                'username' => $username,
                'email' => $email,
                'phone_number' => $phone_number,
                'birth_date' => $birth_date,
                'gender' => $gender
            ];
        }
    } catch (Exception $e) {
        error_log("Update failed: " . $e->getMessage());
        $errors[] = "An unexpected error occurred. Please try again later.";
    }
}

include_once '../includes/header.php';
include_once '../includes/svg_symbols.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile - ECWEB</title>
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
        .error-message { color: red; font-size: 0.9em; }
        .btn-primary {
            background-color: #28a745;
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
            background-color: #218838;
        }
        .btn-primary:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgb(227, 31, 44);
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
        <h2 class="text-center">Update Profile</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="update_profile.php" method="POST" class="w-50 mx-auto">
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
                    <option value="male" <?php echo $form_data['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo $form_data['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Save Changes</button>
        </form>
        <p class="text-center mt-3"><a href="profile.php">Back to Profile</a></p>
    </div>
    <?php include_once '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>