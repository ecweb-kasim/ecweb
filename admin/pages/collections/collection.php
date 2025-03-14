<?php
require_once 'includes/config.php';

try {
    $stmt = $pdo->query("SELECT * FROM collections ORDER BY id ASC");
    $collections = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching collections: " . $e->getMessage());
}
?>
<div class="container">
<h2>Collections</h2>
<p>Manage your collection list here.</p>

<div class="product-actions-bar">
    <a href="?page=collections&action=add_collection" class="add-new-product-button">Add New Collection</a>
</div>

<table class="product-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Image</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($collections)): ?>
            <tr>
                <td colspan="5">No collections found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($collections as $collection): ?>
                <tr>
                    <td><?php echo $collection['id']; ?></td>
                    <td><?php echo htmlspecialchars($collection['title']); ?></td>
                    <td>
                        <?php if (!empty($collection['image'])): ?>
                            <img src="../assets/images/collections/<?php echo htmlspecialchars($collection['image']); ?>" alt="<?php echo htmlspecialchars($collection['title']); ?>" class="product-image">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('Y-m-d H:i', strtotime($collection['created_at'])); ?></td>
                    <td>
                        <a href="?page=collections&action=edit_collection&id=<?php echo $collection['id']; ?>">Edit</a> |
                        <a href="?page=collections&action=delete_collection&id=<?php echo $collection['id']; ?>" onclick="return confirm('Are you sure you want to delete this collection?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
</div>