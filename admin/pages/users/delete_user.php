<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/config.php';

$successMessage = '';
$errorMessage = '';

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($id === false || $id <= 0) {
        $_SESSION['error_message'] = "Invalid user ID.";
        header("Location: ?page=users");
        exit;
    }

    try {
        // Prepare and execute the delete query
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $success = $stmt->execute([$id]);

        if ($success) {
            $_SESSION['success_message'] = "User with ID {$id} has been deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to delete the user.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error: " . htmlspecialchars($e->getMessage());
    }

    // Redirect back to the user list page
    header("Location: ?page=users");
    exit;
} else {
    $_SESSION['error_message'] = "No user ID provided.";
    header("Location: ?page=users");
    exit;
}
?>