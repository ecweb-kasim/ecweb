<?php
require_once 'includes/config.php';


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $discount_percentage = $_POST['discount_percentage'] ?? '';
    $description = $_POST['description'] ?? '';
    $link_url = $_POST['link_url'] ?? '';

    try {
        $stmt = $pdo->prepare("UPDATE discounts SET title = ?, discount_percentage = ?, description = ?, link_url = ? WHERE id = ?");
        $stmt->execute([$title, $discount_percentage, $description, $link_url, $id]);
        $_SESSION['success'] = "Discount updated successfully!";
        header("Location: ?page=discounts");
        exit;
    } catch (PDOException $e) {
        echo "Error: " . htmlspecialchars($e->getMessage());
    }
}

$stmt = $pdo->prepare("SELECT title, discount_percentage, description, link_url FROM discounts WHERE id = ?");
$stmt->execute([$id]);
$discount = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$discount) {
    echo "Discount not found.";
    exit;
}
?>

<h2>Edit Discount</h2>
<form method="post">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <label>Discount Name:</label><br>
    <input type="text" name="title" value="<?php echo htmlspecialchars($discount['title']); ?>" required><br>
    <label>Percentage:</label><br>
    <input type="text" name="discount_percentage" value="<?php echo htmlspecialchars($discount['discount_percentage']); ?>" required><br>
    <label>Description:</label><br>
    <textarea name="description" required><?php echo htmlspecialchars($discount['description']); ?></textarea><br>
    <label>Link/Email (e.g., mailto:example@email.com or URL):</label><br>
    <input type="text" name="link_url" value="<?php echo htmlspecialchars($discount['link_url'] ?? ''); ?>" required><br>
    <button type="submit">Update Discount</button>
</form>
<a href="?page=discounts">Back to Discounts</a>