<div class="table-container">
    <h2>Manage Social Links</h2>
    <a href="?page=social_links&action=add" class="btn btn-primary">Add Social Link</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Platform</th>
                <th>Link</th>
                <th>Icon Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require_once 'includes/config.php';

            $action = isset($_GET['action']) ? $_GET['action'] : '';
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

            if ($action === 'add') {
                include 'add.php';
                exit;
            }
            if ($action === 'edit' && $id > 0) {
                include 'edit.php';
                exit;
            }
            if ($action === 'delete' && $id > 0) {
                include 'delete.php';
                exit;
            }

            if (isset($_SESSION['success'])) {
                echo "<p style='color: green;'>" . htmlspecialchars($_SESSION['success']) . "</p>";
                unset($_SESSION['success']);
            }

            try {
                $stmt = $pdo->query("SELECT id, platform, link, icon_name, status FROM social_links");
                if ($stmt->rowCount() === 0) {
                    echo "<tr><td colspan='6'>No social links found.</td></tr>";
                } else {
                    while ($link = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $id = isset($link['id']) ? intval($link['id']) : 'N/A';
                        $platform = isset($link['platform']) ? htmlspecialchars($link['platform'], ENT_QUOTES, 'UTF-8') : 'Unknown';
                        $linkValue = isset($link['link']) ? htmlspecialchars($link['link'], ENT_QUOTES, 'UTF-8') : 'N/A';
                        $iconName = isset($link['icon_name']) ? htmlspecialchars($link['icon_name'], ENT_QUOTES, 'UTF-8') : 'N/A';
                        $status = isset($link['status']) ? ($link['status'] ? 'Active' : 'Inactive') : 'Inactive';

                        echo "<tr>
                            <td>{$id}</td>
                            <td>{$platform}</td>
                            <td>{$linkValue}</td>
                            <td>{$iconName}</td>
                            <td>{$status}</td>
                            <td>
                                <a href='?page=social_links&action=edit&id={$id}' class='btn btn-primary'>Edit</a>
                                <a href='?page=social_links&action=delete&id={$id}' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this social link?\")'>Delete</a>
                            </td>
                        </tr>";
                    }
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='6'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>