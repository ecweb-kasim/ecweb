<?php
// pages/slides/add_slide.php
// No need for session_start() here since it's already started in index.php

// Include database connection
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title'] ?? '');
    $image = '';

    // Handle image upload (automatically save to admin/assets/images/slides/)
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageName = uniqid() . '_' . basename($_FILES['image']['name']); // Unique filename to prevent overwriting
        $targetDir = '../assets/images/slides/'; // Relative to pages/slides/
        $targetFile = $targetDir . $imageName;

        // Actual upload to the directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = $imageName; // Store the filename for the database
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
    } else {
        echo "<p>No image uploaded or upload failed. Please try again.</p>";
        exit;
    }

    try {
        // Insert new slide into the database with the image filename
        $stmt = $pdo->prepare("INSERT INTO slides (title, image, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$title, $image]);

        // Show success message instead of redirecting immediately
        echo "<div class='success-message'>";
        echo "<h2>Slide Added</h2>";
        echo "<p>The slide '{$title}' has been added successfully.</p>";
        echo "<p><a href='?page=slides'>Back to Slides</a></p>";
        echo "</div>";
        exit;
    } catch (PDOException $e) {
        die("Error adding slide: " . $e->getMessage());
    }
}
?>

<h2>Add Slide</h2>
<p>Fill out the form to add a new slide.</p>

<form method="POST" action="" class="product-form" enctype="multipart/form-data">
    <label for="title">Slide Title:</label><br>
    <input type="text" id="title" name="title" value="" required><br><br>

    <label for="image">Slide Image:</label><br>
    <input type="file" id="image" name="image" accept="image/*" required><br><br>

    <input type="submit" value="Add Slide">
    <a href="?page=slides" class="back-button">Back</a>
</form>