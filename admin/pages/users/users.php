<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/config.php';

// Determine the action from the query string
$action = $_GET['action'] ?? 'view';
$id = $_GET['id'] ?? null;

// Initialize success/error messages
$successMessage = '';
$errorMessage = '';

// Handle user addition or editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'add_user' || $action === 'edit_user')) {
    $full_name = $_POST['full_name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $birth_date = $_POST['birth_date'] ?? '';
    $gender = $_POST['gender'] ?? '';

    try {
        if ($action === 'add_user') {
            $stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, phone_number, birth_date, gender) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$full_name, $username, $email, $phone_number, $birth_date, $gender])) {
                $successMessage = "The user '{$full_name}' has been added successfully.";
                header("Location: users.php");
                exit;
            }
        } elseif ($action === 'edit_user' && $id) {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, phone_number = ?, birth_date = ?, gender = ? WHERE id = ?");
            if ($stmt->execute([$full_name, $username, $email, $phone_number, $birth_date, $gender, $id])) {
                $successMessage = "The user '{$full_name}' has been updated successfully.";
                header("Location: users.php");
                exit;
            }
        }
        $errorMessage = "Failed to process the request. Please try again.";
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

// Handle user deletion
if ($action === 'delete_user' && $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$id])) {
            $successMessage = "User deleted successfully.";
        } else {
            $errorMessage = "Failed to delete the user.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

// Handle search functionality
$searchTerm = $_GET['search'] ?? '';
$users = [];

try {
    if ($searchTerm) {
        $stmt = $pdo->prepare("SELECT id, full_name, username, email, phone_number, birth_date, gender, created_at FROM users WHERE id = ? OR full_name LIKE ?");
        $stmt->execute([$searchTerm, "%$searchTerm%"]);
    } else {
        $stmt = $pdo->query("SELECT id, full_name, username, email, phone_number, birth_date, gender, created_at FROM users");
    }
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = "Error fetching users: " . $e->getMessage();
}

// Fetch user data for editing (if editing)
$user = null;
if ($action === 'edit_user' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        header("Location: users.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* Header Styling */
        h2 {
            font-size: 32px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 15px;
            text-align: center;
            background: linear-gradient(135deg, #2c3e50, #3498db);
            padding: 20px;
            border-radius: 10px 10px 0 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        p {
            font-size: 16px;
            color: #7f8c8d;
            text-align: center;
            margin-bottom: 30px;
        }

        .action-bar {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .search-form {
            display: flex;
            align-items: center;
            flex: 1;
            margin-right: 10px;
        }
        .search-form input[type="text"] {
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid #ced4da;
            border-right: none;
            border-radius: 20px 0 0 20px;
            outline: none;
            width: 100%;
            box-sizing: border-box;
        }
        .search-form button {
            padding: 8px 12px;
            font-size: 14px;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-left: none;
            border-radius: 0 20px 20px 0;
            cursor: pointer;
            color: #007bff;
        }
        .search-form button:hover {
            background-color: #e9ecef;
        }
        .back-button {
            display: inline-block;
            padding: 6px 12px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-left: 10px;
            cursor: pointer;
        }
        .back-button:hover {
            background-color: #5a6268;
            color: white;
        }
        .btn-primary {
            display: inline-block;
            padding: 6px 12px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .table-responsive {
            margin-top: 20px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
        }
        table thead th {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #0056b3;
        }
        table tbody td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            display: inline-block;
            padding: 5px 10px;
            font-size: 12px;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background-color: #e0a800;
        }
        .btn-custom {
            background-color: #dc3545;
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background-color: #c82333;
            color: white;
        }
        .alert {
            display: none;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            padding: 10px 20px;
            border-radius: 4px;
            color: white;
        }
        .alert-success {
            background-color: #28a745;
        }
        .alert-danger {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
<div class="container">
        <h2 >Users</h2>
        <p >Manage your user list here.</p>

        <!-- Success/Error Messages -->
        <?php if ($successMessage): ?>
            <div id="successAlert" class="alert alert-success"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <?php if ($action === 'view'): ?>
            <div class="action-bar">
                <div class="search-form">
                    <form method="GET" action="users.php" style="display: flex; align-items: center; width: 100%;">
                        <input type="hidden" name="page" value="users">
                        <input type="text" name="search" placeholder="Search by ID or Name" value="<?php echo htmlspecialchars($searchTerm); ?>" aria-label="Search">
                        <button type="submit"><i class="fas fa-search"></i></button>
                        <a href="users.php?page=users" class="back-button">Clear</a>
                    </form>
                </div>
                <a href="index.php?page=users&action=add_user" class="btn-primary">Add New User</a>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Birth Date</th>
                            <th>Gender</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center;">No users found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                                    <td><?php echo htmlspecialchars($user['birth_date']); ?></td>
                                    <td><?php echo htmlspecialchars($user['gender']); ?></td>
                                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                    <td>
                                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-warning">Edit</a>
                                        <a href="?page=users&action=delete_user&id=<?php echo $user['id']; ?>" class="btn btn-custom" onclick="return confirm('Are you sure?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($action === 'add_user'): ?>
            <h2>Add New User</h2>
            <p>Add a new user to the system.</p>
            <form method="POST">
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
                <button type="submit" class="btn btn-primary">Add User</button>
                <a href="users.php" class="back-button">Back</a>
            </form>
        <?php elseif ($action === 'edit_user' && $user): ?>
            <h2>Edit User</h2>
            <form method="POST">
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
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="users.php" class="back-button">Back</a>
            </form>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const successAlert = document.getElementById("successAlert");
            if (successAlert) {
                successAlert.style.display = "block";
                setTimeout(function() {
                    window.location.href = "users.php";
                }, 100);
            }
        });
    </script>
</body>
</html>