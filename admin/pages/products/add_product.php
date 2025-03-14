<?php

// Include database connection
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Do not use htmlspecialchars() for storage; only for display
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $discount = intval($_POST['discount'] ?? 0);
    $category = trim($_POST['category'] ?? '');
    $sizes = isset($_POST['sizes']) ? implode(',', array_filter(explode(',', str_replace(' ', '', $_POST['sizes'])))) : '';
    $colors = isset($_POST['colors']) ? implode(',', array_filter(explode(',', str_replace(' ', '', $_POST['colors'])))) : '';
    $stock = intval($_POST['stock'] ?? 0);

    // Basic validation (optional but recommended)
    if (empty($title) || empty($description) || empty($category) || empty($sizes) || empty($colors)) {
        echo "<p>Please fill in all required fields.</p>";
        exit;
    }

    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageName = uniqid() . '_' . basename($_FILES['image']['name']); // Unique filename to prevent overwriting
        $targetDir = '../assets/images/products/';
        $targetFile = $targetDir . $imageName;

        // Actual upload
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = $imageName; // Store the unique filename
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
        // Insert new product into the database
        $stmt = $pdo->prepare("INSERT INTO products (title, description, price, discount, category, image, sizes, colors, stock, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$title, $description, $price, $discount, $category, $image, $sizes, $colors, $stock]);

        // Redirect to product list to show the new product
        header("Location: ?page=products");
        exit;
    } catch (PDOException $e) {
        die("Error adding product: " . $e->getMessage());
    }
}
?>

<h2>Add Product</h2>
<p>Fill out the form to add a new product.</p>

<form method="POST" action="" class="product-form" enctype="multipart/form-data">
    <label for="title">Product Title:</label><br>
    <input type="text" id="title" name="title" value="" required><br><br>

    <label for="description">Description:</label><br>
    <textarea id="description" name="description" rows="4" cols="50" required></textarea><br><br>

    <label for="price">Price ($):</label><br>
    <input type="number" id="price" name="price" step="0.01" min="0" value="0" required><br><br>

    <label for="discount">Discount (%):</label><br>
    <input type="number" id="discount" name="discount" min="0" max="100" value="0" required><br><br>

    <label for="category">Category:</label><br>
    <select id="category" name="category" required>
        <option value="">Select Category</option>
        <option value="Men">Men</option>
        <option value="Women">Women</option>
    </select><br><br>

    <label for="image">Product Image:</label><br>
    <input type="file" id="image" name="image" accept="image/*" required><br><br>

    <label for="sizes">Sizes (comma-separated, e.g., S,M,L):</label><br>
    <input type="text" id="sizes" name="sizes" value="" required><br><br>

    <label for="colors">Colors (comma-separated, e.g., White,Black):</label><br>
    <input type="text" id="colors" name="colors" value="" required><br><br>

    <label for="stock">Stock:</label><br>
    <input type="number" id="stock" name="stock" min="0" value="0" required><br><br>

    <input type="submit" value="Add Product">
    <a href="?page=products" class="back-button">Back</a>
</form>