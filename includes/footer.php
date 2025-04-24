<?php
// Include the database configuration
require_once 'db_config.php'; // Adjust the path if needed
?>

<footer id="footer" class="py-5 border-top">
    <div class="container-lg">
        <div class="row">
            <?php
            try {
                // Fetch footer sections using the $pdo connection from config.php
                $stmt = $pdo->query("SELECT * FROM footer_sections");
                $footer_sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Error fetching footer sections: " . $e->getMessage();
                $footer_sections = [];
            }

            // Loop through footer sections (empty array if no data)
            $footer_sections = $footer_sections ?? [];
            foreach ($footer_sections as $section):
            ?>
                <div class="col-6 col-md-4 col-lg-2 pb-3">
                    <div class="footer-menu">
                        <h5 class="widget-title pb-2"><?php echo htmlspecialchars($section['section_title']); ?></h5>
                        <?php
                        $items = explode(',', $section['items']);
                        $links = !empty($section['links']) ? explode(',', $section['links']) : array_fill(0, count($items), ''); // Create an array of empty links if none provided

                        if ($section['section_title'] === 'Get In Touch') {
                            echo '<div class="footer-contact-text">';
                            foreach ($items as $index => $item) {
                                $item = trim($item);
                                if (strpos($item, '@') !== false) {
                                    echo '<span class="text-hover fw-bold light-border"><a href="mailto:' . htmlspecialchars($item) . '">' . htmlspecialchars($item) . '</a></span>';
                                } else {
                                    echo '<span>' . htmlspecialchars($item) . '</span>';
                                }
                            }
                            echo '</div>';
                        } else {
                            echo '<ul class="menu-list list-unstyled">';
                            foreach ($items as $index => $item) {
                                $item = trim($item);
                                $link = isset($links[$index]) ? trim($links[$index]) : '';
                                if (!empty($link)) {
                                    echo '<li class="pb-2"><a href="' . htmlspecialchars($link) . '">' . htmlspecialchars($item) . '</a></li>';
                                } else {
                                    echo '<li class="pb-2">' . htmlspecialchars($item) . '</li>'; // No link, just display the item
                                }
                            }
                            echo '</ul>';
                        }
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</footer>
<!-- Scripts -->
<script src="assets/js/jquery-1.11.0.min.js"></script>
<script src="assets/js/plugins.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>