<?php
require_once 'includes/config.php';

try {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM logo WHERE id = ?");
        $stmt->execute([$id]);
        $logo = $stmt->fetch();

        if (!$logo) {
            echo "<h2>Logo Not Found</h2>";
            echo "<p><a href='?page=logo'>Back to Logo</a></p>";
            exit;
        }
    } else {
        $logo = ['id' => 0, 'title' => '', 'logo_value' => '', 'created_at' => date('Y-m-d H:i:s')];
    }
} catch (PDOException $e) {
    die("Error fetching logo: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Do not use htmlspecialchars() for storage; only for display
    $title = trim($_POST['title'] ?? '');
    $logo_value = $logo['logo_value'] ?? '';

    // Basic validation
    if (empty($title)) {
        echo "<p>Please provide a title for the logo.</p>";
        exit;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetDir = '../assets/images/logo/';
        $targetFile = $targetDir . $imageName;

        // Check image dimensions
        list($width, $height) = getimagesize($_FILES['image']['tmp_name']);
        if ($width != 136 || $height != 73) {
            echo "<p>Invalid image size. Logo must be exactly 136x73 pixels.</p>";
            exit;
        }

        // Upload the image
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $logo_value = $imageName; // Update with new filename
            // Optionally delete the old image if it exists and is not NULL/empty
            if (!empty($logo['logo_value']) && file_exists($targetDir . $logo['logo_value'])) {
                unlink($targetDir . $logo['logo_value']);
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
        if ($id > 0) {
            // Update existing logo
            $stmt = $pdo->prepare("UPDATE logo SET title = ?, logo_value = ?, created_at = ? WHERE id = ?");
            $stmt->execute([$title, $logo_value, $logo['created_at'], $id]);
        } else {
            // Insert new logo
            $stmt = $pdo->prepare("INSERT INTO logo (title, logo_value, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$title, $logo_value]);
        }

        // Show success message instead of redirecting immediately
        echo "<div class='success-message'>";
        echo "<h2>Logo " . ($id > 0 ? "Updated" : "Added") . "</h2>";
        echo "<p>The logo '{$title}' has been " . ($id > 0 ? "updated" : "added") . " successfully.</p>";
        echo "<p><a href='?page=logo'>Back to Logo</a></p>";
        echo "</div>";
        exit;
    } catch (PDOException $e) {
        die("Error " . ($id > 0 ? "updating" : "adding") . " logo: " . $e->getMessage());
    }
}
?>

<h2><?php echo $logo['id'] > 0 ? "Edit Logo" : "Add New Logo"; ?> - <?php echo htmlspecialchars($logo['title'] ?? 'New Logo'); ?></h2>
<p>Update or add the logo details below.</p>

<form method="POST" action="" class="product-form" enctype="multipart/form-data">
    <label for="title">Logo Title:</label><br>
    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($logo['title'] ?? ''); ?>" required><br><br>

    <label for="image">Logo Image (136x73 pixels):</label><br>
    <input type="file" id="image" name="image" accept="image/*" <?php echo $logo['id'] == 0 ? 'required' : ''; ?>><br>
    <?php if (!empty($logo['logo_value'])): ?>
        <img src="../assets/images/logo/<?php echo htmlspecialchars($logo['logo_value']); ?>" alt="<?php echo htmlspecialchars($logo['title'] ?? 'Logo'); ?>" class="product-image" style="max-width: 136px; height: auto;">
        <input type="hidden" name="image" value="<?php echo htmlspecialchars($logo['logo_value']); ?>">
    <?php endif; ?><br><br>

    <input type="submit" value="<?php echo $logo['id'] > 0 ? "Update Logo" : "Add Logo"; ?>">
    <a href="?page=logo" class="back-button">Back</a>
</form>