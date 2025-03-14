<?php
require_once 'includes/config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$offer = null;

try {
    $stmt = $pdo->prepare("SELECT id, special_key, special_value FROM special_offer WHERE id = ?");
    $stmt->execute([$id]);
    $offer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$offer) {
        die("Special offer not found.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $special_value = $_POST['special_value'] ?? '';

        $special_value = htmlspecialchars(strip_tags($special_value), ENT_QUOTES, 'UTF-8');

        if (empty($special_value)) {
            $error = "Value is required.";
        } else {
            $stmt = $pdo->prepare("UPDATE special_offer SET special_value = ? WHERE id = ?");
            $stmt->execute([$special_value, $id]);
            $_SESSION['success'] = "Special offer with ID {$id} updated successfully!";
            header("Location: ?page=special_offer");
            exit;
        }
    }
} catch (PDOException $e) {
    die("Error: " . htmlspecialchars($e->getMessage()));
}
?>

<div class="edit-container">
    <h2>Edit Special Offer</h2>
    <?php if (isset($error)) echo "<p style='color: red;'>{$error}</p>"; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label for="special_value">Value:</label>
            <textarea name="special_value" id="special_value" class="form-control" required><?php echo htmlspecialchars($offer['special_value'], ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
        <button type="submit" name="update" class="btn btn-primary">Update Special Offer</button>
        <a href="?page=special_offer" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<style>
.edit-container { max-width: 600px; margin: 20px auto; padding: 20px; background: #f9f9f9; border-radius: 5px; }
.form-group { margin-bottom: 15px; }
label { display: block; margin-bottom: 5px; }
.form-control { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
.btn { padding: 10px 20px; margin-right: 10px; }
</style>