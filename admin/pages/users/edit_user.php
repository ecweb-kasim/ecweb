<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/config.php';

// Check if the user ID is provided
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: users.php");
    exit;
}

// Fetch user data for editing
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        header("Location: users.php");
        exit;
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}

// Handle form submission for updating user
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

    // Hash the password if provided
    $password_hash = $password ? password_hash($password, PASSWORD_DEFAULT) : $user['password_hash'];

    try {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, phone_number = ?, birth_date = ?, gender = ?, password_hash = ? WHERE id = ?");
        $success = $stmt->execute([$full_name, $username, $email, $phone_number, $birth_date, $gender, $password_hash, $id]);

        if ($success) {
            $successMessage = "The user '{$full_name}' has been updated successfully.";
        } else {
            $errorInfo = $stmt->errorInfo();
            $errorMessage = "Failed to update the user. Error: " . htmlspecialchars($errorInfo[2]);
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
    <title>Edit User</title>
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
        <h2>Edit User</h2>
        <p>Edit the details of the user.</p>

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
                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Birth Date</label>
                <input type="date" name="birth_date" class="form-control" value="<?php echo htmlspecialchars($user['birth_date']); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-control">
                    <option value="">Select Gender</option>
                    <option value="Male" <?php echo $user['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo $user['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo $user['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                <small class="form-text text-muted">Enter a new password to update it, otherwise leave blank.</small>
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="users.php" class="back-button">Back</a>
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