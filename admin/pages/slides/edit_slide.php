<?php
// pages/slides/edit_slide.php
// No need for session_start() here since it's already started in index.php

// Include database connection
require_once 'includes/config.php';

try {
    // Fetch the slide from the database
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $stmt = $pdo->prepare("SELECT * FROM slides WHERE id = ?");
    $stmt->execute([$id]);
    $slide = $stmt->fetch();

    if (!$slide) {
        echo "<h2>Slide Not Found</h2>";
        echo "<p><a href='?page=slides'>Back to Slides</a></p>";
        exit;
    }
} catch (PDOException $e) {
    die("Error fetching slide: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title'] ?? $slide['title']);
    $image = $slide['image']; // Default to existing image

    // Handle image upload (automatically save to admin/assets/images/slides/ if provided)
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageName = uniqid() . '_' . basename($_FILES['image']['name']); // Unique filename to prevent overwriting
        $targetDir = '../assets/images/slides/'; // Relative to pages/slides/
        $targetFile = $targetDir . $imageName;

        // Actual upload to the directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = $imageName; // Update with new filename
            // Optionally delete the old image if it exists
            if (!empty($slide['image']) && file_exists($targetDir . $slide['image'])) {
                unlink($targetDir . $slide['image']);
            }
        } else {
            echo "<p>Error uploading image. Please check permissions and try again.</p>";
            exit;
        }

        // Validation for file type and size
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            echo "<p>Invalid file type. Please upload an image (JPG, PNG, JPEG).</p>";
            unlink($targetFile); // Remove the uploaded file if invalid
            exit;
        }
        if ($_FILES['image']['size'] > 2000000) { // 2MB limit
            echo "<p>File too large. Maximum size is 2MB.</p>";
            unlink($targetFile); // Remove the uploaded file if too large
            exit;
        }
    }

    try {
        // Update slide in the database with the new or existing image filename
        $stmt = $pdo->prepare("UPDATE slides SET title = ?, image = ?, created_at = ? WHERE id = ?");
        $stmt->execute([$title, $image, $slide['created_at'], $id]);

        // Show success message instead of redirecting immediately
        echo "<div class='success-message'>";
        echo "<h2>Slide Updated</h2>";
        echo "<p>The slide '{$title}' has been updated successfully.</p>";
        echo "<p><a href='?page=slides'>Back to Slides</a></p>";
        echo "</div>";
        exit;
    } catch (PDOException $e) {
        die("Error updating slide: " . $e->getMessage());
    }
}
?>

<h2>Edit Slide - <?php echo htmlspecialchars($slide['title']); ?></h2>
<p>Update the slide details below.</p>

<form method="POST" action="" class="product-form" enctype="multipart/form-data">
    <label for="title">Slide Title:</label><br>
    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($slide['title']); ?>" required><br><br>

    <label for="image">Slide Image:</label><br>
    <input type="file" id="image" name="image" accept="image/*"><br>
    <?php if (!empty($slide['image'])): ?>
        <img src="../assets/images/slides/<?php echo htmlspecialchars($slide['image']); ?>" alt="<?php echo htmlspecialchars($slide['title']); ?>" class="product-image" style="max-width: 100px; height: auto;">
        <input type="hidden" name="image" value="<?php echo htmlspecialchars($slide['image']); ?>">
    <?php endif; ?><br><br>

    <input type="submit" value="Update Slide">
    <a href="?page=slides" class="back-button">Back</a>
</form>