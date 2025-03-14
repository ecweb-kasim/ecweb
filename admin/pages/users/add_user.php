<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/config.php';

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = htmlspecialchars($_POST['full_name'] ?? '');
    $username = htmlspecialchars($_POST['username'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $phone_number = htmlspecialchars($_POST['phone_number'] ?? '');
    $birth_date = htmlspecialchars($_POST['birth_date'] ?? '');
    $gender = htmlspecialchars($_POST['gender'] ?? '');
    $password = $_POST['password'] ?? ''; // New password field

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Insert new user into the database
        $stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, phone_number, birth_date, gender, password_hash, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $success = $stmt->execute([$full_name, $username, $email, $phone_number, $birth_date, $gender, $password_hash]);

        if ($success) {
            $successMessage = "The user '{$full_name}' has been added successfully.";
            // For debugging
            // echo "<pre>"; var_dump($success); echo "</pre>";
        } else {
            $errorInfo = $stmt->errorInfo();
            $errorMessage = "Failed to add the user. Error: " . htmlspecialchars($errorInfo[2]);
        }
    } catch (PDOException $e) {
        $errorMessage = "Error: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .alert {
            display: none;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
        }
        .back-button {
            display: inline-block;
            padding: 6px 12px;
            background-color: #6c757d; /* Bootstrap secondary color */
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-left: 10px;
        }
        .back-button:hover {
            background-color: #5a6268;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Add New User</h2>
        <p>Add a new user to the system.</p>

        <!-- Success/Error Messages -->
        <?php if ($successMessage): ?>
            <div id="successAlert" class="alert alert-success"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone_number" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Birth Date</label>
                <input type="date" name="birth_date" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-control">
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Add User</button>
            <a href="?page=users" class="back-button">Back</a>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const successAlert = document.getElementById("successAlert");
            if (successAlert) {
                successAlert.style.display = "block"; // Show the alert
                setTimeout(function() {
                    window.location.href = "?page=users"; // Redirect after 0.1 seconds
                }, 100); // 100 milliseconds = 0.1 seconds
            }
        });
    </script>
</body>
</html>