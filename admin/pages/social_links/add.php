<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $platform = $_POST['platform'] ?? '';
    $link = $_POST['link'] ?? '';
    $icon_name = $_POST['icon_name'] ?? '';
    $status = isset($_POST['status']) ? 1 : 0;

    $platform = htmlspecialchars(strip_tags($platform), ENT_QUOTES, 'UTF-8');
    $link = htmlspecialchars(strip_tags($link), ENT_QUOTES, 'UTF-8');
    $icon_name = htmlspecialchars(strip_tags($icon_name), ENT_QUOTES, 'UTF-8');

    if (empty($platform) || empty($link) || empty($icon_name)) {
        $error = "All fields are required.";
    } elseif (!filter_var($link, FILTER_VALIDATE_URL)) {
        $error = "Invalid URL format.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO social_links (platform, link, icon_name, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$platform, $link, $icon_name, $status]);
            $_SESSION['success'] = "Social link added successfully!";
            header("Location: ?page=social_links");
            exit;
        } catch (PDOException $e) {
            $error = "Error: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<div class="edit-container">
    <h2>Add New Social Link</h2>
    <?php if (isset($error)) echo "<p style='color: red;'>{$error}</p>"; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label for="platform">Platform:</label>
            <input type="text" name="platform" id="platform" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="link">Link:</label>
            <input type="url" name="link" id="link" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="icon_name">Icon Name:</label>
            <input type="text" name="icon_name" id="icon_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <input type="checkbox" name="status" id="status" value="1" checked> Active
        </div>
        <button type="submit" name="add" class="btn btn-primary">Add Social Link</button>
        <a href="?page=social_links" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<style>
.edit-container { max-width: 600px; margin: 20px auto; padding: 20px; background: #f9f9f9; border-radius: 5px; }
.form-group { margin-bottom: 15px; }
label { display: block; margin-bottom: 5px; }
.form-control { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
.btn { padding: 10px 20px; margin-right: 10px; }
</style>