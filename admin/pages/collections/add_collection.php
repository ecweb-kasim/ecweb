<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title'] ?? '');
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageName = uniqid() . '_' . basename($_FILES['image']['name']); 
        $targetFile = $targetDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = $imageName; 
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
    } else {
        echo "<p>No image uploaded or upload failed. Please try again.</p>";
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO collections (title, image, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$title, $image]);

        echo "<div class='success-message'>";
        echo "<h2>Collection Added</h2>";
        echo "<p>The collection '{$title}' has been added successfully.</p>";
        echo "<p><a href='?page=collections'>Back to Collections</a></p>";
        echo "</div>";
        exit;
    } catch (PDOException $e) {
        die("Error adding collection: " . $e->getMessage());
    }
}
?>

<h2>Add Collection</h2>
<p>Fill out the form to add a new collection.</p>

<form method="POST" action="" class="product-form" enctype="multipart/form-data">
    <label for="title">Collection Title:</label><br>
    <input type="text" id="title" name="title" value="" required><br><br>

    <label for="image">Collection Image:</label><br>
    <input type="file" id="image" name="image" accept="image/*" required><br><br>

    <input type="submit" value="Add Collection">
    <a href="?page=collections" class="back-button">Back</a>
</form>