<?php
// pages/slides/delete_slide.php
// No need for session_start() here since it's already started in index.php

// Include database connection
require_once 'includes/config.php';

try {
    // Get slide ID from URL
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    // Fetch the slide to display in the confirmation and handle image deletion
    $stmt = $pdo->prepare("SELECT title, image FROM slides WHERE id = ?");
    $stmt->execute([$id]);
    $slide = $stmt->fetch();

    if (!$slide) {
        echo "<h2>Slide Not Found</h2>";
        echo "<p><a href='?page=slides'>Back to Slides</a></p>";
        exit;
    }

    // Delete the slide from the database
    $stmt = $pdo->prepare("DELETE FROM slides WHERE id = ?");
    $stmt->execute([$id]);

    // Automatically delete the image file if it exists
    $targetDir = '../assets/images/slides/';
    if (!empty($slide['image']) && file_exists($targetDir . $slide['image'])) {
        unlink($targetDir . $slide['image']);
    }

    echo "<div class='success-message'>";
    echo "<h2>Slide Deleted</h2>";
    echo "<p>The slide '{$slide['title']}' has been deleted.</p>";
    echo "<p><a href='?page=slides'>Back to Slides</a></p>";
    echo "</div>";
} catch (PDOException $e) {
    die("Error deleting slide: " . $e->getMessage());
}
?>

<style>
    .success-message {
        background-color: #d4edda;
        color: #155724;
        padding: 15px;
        margin: 20px auto;
        width: 50%;
        border: 1px solid #c3e6cb;
        border-radius: 4px;
    }
</style>