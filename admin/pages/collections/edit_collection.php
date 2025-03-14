<?php
require_once 'includes/config.php';

try {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $stmt = $pdo->prepare("SELECT * FROM collections WHERE id = ?");
    $stmt->execute([$id]);
    $collection = $stmt->fetch();

    if (!$collection) {
        echo "<h2>Collection Not Found</h2>";
        echo "<p><a href='?page=collections'>Back to Collections</a></p>";
        exit;
    }
} catch (PDOException $e) {
    die("Error fetching collection: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title'] ?? $collection['title']);
    $image = $collection['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetDir = '../assets/images/collections/';
        $targetFile = $targetDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = $imageName;

            if (!empty($collection['image']) && file_exists($targetDir . $collection['image'])) {
                unlink($targetDir . $collection['image']);
            }
        } else {
            echo "<p>Error uploading image. Please check permissions and try again.</p>";
            exit;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            echo "<p>Invalid file type. Please upload an image (JPG, PNG, JPEG).</p>";
            unlink($targetFile);
            exit;
        }
        if ($_FILES['image']['size'] > 2000000) { 
            echo "<p>File too large. Maximum size is 2MB.</p>";
            unlink($targetFile);
            exit;
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE collections SET title = ?, image = ?, created_at = ? WHERE id = ?");
        $stmt->execute([$title, $image, $collection['created_at'], $id]);

        echo "<div class='success-message'>";
        echo "<h2>Collection Updated</h2>";
        echo "<p>The collection '{$title}' has been updated successfully.</p>";
        echo "<p><a href='?page=collections'>Back to Collections</a></p>";
        echo "</div>";
        exit;
    } catch (PDOException $e) {
        die("Error updating collection: " . $e->getMessage());
    }
}
?>

<h2>Edit Collection - <?php echo htmlspecialchars($collection['title']); ?></h2>
<p>Update the collection details below.</p>

<form method="POST" action="" class="product-form" enctype="multipart/form-data">
    <label for="title">Collection Title:</label><br>
    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($collection['title']); ?>" required><br><br>

    <label for="image">Collection Image:</label><br>
    <input type="file" id="image" name="image" accept="image/*"><br>
    <?php if (!empty($collection['image'])): ?>
        <img src="../assets/images/collections/<?php echo htmlspecialchars($collection['image']); ?>" alt="<?php echo htmlspecialchars($collection['title']); ?>" class="product-image" style="max-width: 100px; height: auto;">
        <input type="hidden" name="image" value="<?php echo htmlspecialchars($collection['image']); ?>">
    <?php endif; ?><br><br>

    <input type="submit" value="Update Collection">
    <a href="?page=collections" class="back-button">Back</a>
</form>