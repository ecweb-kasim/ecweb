<?php

// Include database connection
require_once 'includes/config.php';

try {
    // Fetch the product from the database
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo "<h2>Product Not Found</h2>";
        echo "<p><a href='?page=products'>Back to Products</a></p>";
        exit;
    }
} catch (PDOException $e) {
    die("Error fetching product: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Do not use htmlspecialchars() for storage; only for display
    $title = trim($_POST['title'] ?? $product['title']);
    $description = trim($_POST['description'] ?? $product['description']);
    $price = floatval($_POST['price'] ?? $product['price']);
    $discount = intval($_POST['discount'] ?? $product['discount']);
    $category = trim($_POST['category'] ?? $product['category']);
    $sizes = isset($_POST['sizes']) ? implode(',', array_filter(explode(',', str_replace(' ', '', $_POST['sizes'])))) : $product['sizes'];
    $colors = isset($_POST['colors']) ? implode(',', array_filter(explode(',', str_replace(' ', '', $_POST['colors'])))) : $product['colors'];
    $stock = intval($_POST['stock'] ?? $product['stock']);

    // Basic validation (optional but recommended)
    if (empty($title) || empty($description) || empty($category) || empty($sizes) || empty($colors)) {
        echo "<p>Please fill in all required fields.</p>";
        exit;
    }

    // Handle image upload (optional update)
    $image = $product['image']; // Default to existing image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetDir = '../assets/images/products/';
        $targetFile = $targetDir . $imageName;

        // Actual upload
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = $imageName; // Update image filename if upload succeeds
            // Optionally delete the old image if it exists
            if (!empty($product['image']) && file_exists($targetDir . $product['image'])) {
                unlink($targetDir . $product['image']);
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
        // Update product in the database
        $stmt = $pdo->prepare("UPDATE products SET title = ?, description = ?, price = ?, discount = ?, category = ?, image = ?, sizes = ?, colors = ?, stock = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$title, $description, $price, $discount, $category, $image, $sizes, $colors, $stock, $id]);

        // Redirect to product list to show the updated product
        header("Location: ?page=products");
        exit;
    } catch (PDOException $e) {
        die("Error updating product: " . $e->getMessage());
    }
}
?>

<h2>Edit Product - <?php echo htmlspecialchars($product['title']); ?></h2>
<p>Update the product details below.</p>

<form method="POST" action="" class="product-form" enctype="multipart/form-data">
    <label for="title">Product Title:</label><br>
    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required><br><br>

    <label for="description">Description:</label><br>
    <textarea id="description" name="description" rows="4" cols="50" required><?php echo htmlspecialchars($product['description']); ?></textarea><br><br>

    <label for="price">Price ($):</label><br>
    <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required><br><br>

    <label for="discount">Discount (%):</label><br>
    <input type="number" id="discount" name="discount" min="0" max="100" value="<?php echo $product['discount']; ?>" required><br><br>

    <label for="category">Category:</label><br>
    <select id="category" name="category" required>
        <option value="">Select Category</option>
        <option value="Men" <?php echo $product['category'] === 'Men' ? 'selected' : ''; ?>>Men</option>
        <option value="Women" <?php echo $product['category'] === 'Women' ? 'selected' : ''; ?>>Women</option>
    </select><br><br>

    <label for="image">Product Image:</label><br>
    <input type="file" id="image" name="image" accept="image/*"><br>
    <?php if (!empty($product['image'])): ?>
        <img src="../assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="product-image" style="max-width: 100px; height: auto;">
        <input type="hidden" name="image" value="<?php echo htmlspecialchars($product['image']); ?>">
    <?php endif; ?><br><br>

    <label for="sizes">Sizes (comma-separated, e.g., S,M,L):</label><br>
    <input type="text" id="sizes" name="sizes" value="<?php echo implode(',', explode(',', $product['sizes'])); ?>" required><br><br>

    <label for="colors">Colors (comma-separated, e.g., White,Black):</label><br>
    <input type="text" id="colors" name="colors" value="<?php echo implode(',', explode(',', $product['colors'])); ?>" required><br><br>

    <label for="stock">Stock:</label><br>
    <input type="number" id="stock" name="stock" min="0" value="<?php echo $product['stock']; ?>" required><br><br>

    <input type="submit" value="Update Product">
    <a href="?page=products" class="back-button">Back</a>
</form>