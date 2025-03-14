<div class="table-container">
    <h2>Manage Special Offers</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Key</th>
                <th>Value</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require_once 'includes/config.php';

            $action = isset($_GET['action']) ? $_GET['action'] : '';
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

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
                $stmt = $pdo->query("SELECT id, special_key, special_value FROM special_offer");
                if ($stmt->rowCount() === 0) {
                    echo "<tr><td colspan='4'>No special offers found.</td></tr>";
                } else {
                    while ($offer = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $id = isset($offer['id']) ? intval($offer['id']) : 'N/A';
                        $key = isset($offer['special_key']) ? htmlspecialchars($offer['special_key'], ENT_QUOTES, 'UTF-8') : 'N/A';
                        $value = isset($offer['special_value']) ? htmlspecialchars($offer['special_value'], ENT_QUOTES, 'UTF-8') : 'N/A';

                        echo "<tr>
                            <td>{$id}</td>
                            <td>{$key}</td>
                            <td>{$value}</td>
                            <td>
                                <a href='?page=special_offer&action=edit&id={$id}' class='btn btn-primary'>Edit</a>
                                <a href='?page=special_offer&action=delete&id={$id}' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this special offer?\")'>Delete</a>
                            </td>
                        </tr>";
                    }
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='4'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>