<div class="table-container">
    <h2>Manage Discounts</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Discount Name</th>
                <th>Percentage</th>
                <th>Link</th> <!-- New column for link_url -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require_once 'includes/config.php';

            // Handle different actions
            $action = isset($_GET['action']) ? $_GET['action'] : '';
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

            // Handle Add
            if ($action === 'add') {
                include 'add.php';
                exit;
            }

            // Handle Edit
            if ($action === 'edit' && $id > 0) {
                include 'edit.php';
                exit;
            }

            // Display success message if set
            if (isset($_SESSION['success'])) {
                echo "<p style='color: green;'>" . htmlspecialchars($_SESSION['success']) . "</p>";
                unset($_SESSION['success']);
            }

            try {
                // Fetch discounts for the table, including link_url
                $stmt = $pdo->query("SELECT id, title, discount_percentage, link_url FROM discounts");

                if ($stmt->rowCount() === 0) {
                    echo "<tr><td colspan='5'>No discounts found.</td></tr>"; // Updated colspan to 5
                } else {
                    while ($discount = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $id = isset($discount['id']) ? intval($discount['id']) : 'N/A';
                        $name = isset($discount['title']) ? htmlspecialchars($discount['title'], ENT_QUOTES, 'UTF-8') : 'Unknown';
                        $percentage = isset($discount['discount_percentage']) ? htmlspecialchars($discount['discount_percentage'], ENT_QUOTES, 'UTF-8') : 'N/A';
                        $link = isset($discount['link_url']) ? htmlspecialchars($discount['link_url'], ENT_QUOTES, 'UTF-8') : 'N/A';

                        echo "<tr>
                            <td>{$id}</td>
                            <td>{$name}</td>
                            <td>{$percentage}</td>
                            <td>{$link}</td> <!-- Display the link_url -->
                            <td>
                                <a href='?page=discounts&action=edit&id={$id}' class='btn btn-primary'>Edit</a>
                            </td>
                        </tr>";
                    }
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='5'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>"; // Updated colspan to 5
            }
            ?>
        </tbody>
    </table>
</div>