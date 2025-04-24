<?php
include_once 'includes/db_config.php';
?>
<section class="discount-coupon py-2 my-2 py-md-5 my-md-5">
    <div class="container">
        <?php
        $stmt = $pdo->prepare("SELECT discount_percentage, title, description, link_url FROM discounts ORDER BY created_at DESC LIMIT 1");
        $stmt->execute();
        $discount = $stmt->fetch();
        if ($discount) {
            echo '<div class="bg-gray coupon position-relative p-5">';
            echo '<div class="bold-text position-absolute">' . htmlspecialchars($discount['discount_percentage']) . '</div>';
            echo '<div class="row justify-content-between align-items-center">';
            echo '<div class="col-lg-7 col-md-12 mb-3">';
            echo '<div class="coupon-header">';
            echo '<h2 class="display-7">' . htmlspecialchars($discount['title']) . '</h2>';
            echo '<p class="m-0">' . htmlspecialchars($discount['description']) . '</p>';
            echo '</div>';
            echo '</div>';
            echo '<div class="col-lg-3 col-md-12">';
            echo '<div class="btn-wrap">';
            echo '<a href="' . htmlspecialchars($discount['link_url'] ?? 'https://t.me/kasimstore') . '" class="btn btn-black btn-medium text-uppercase hvr-sweep-to-right">Email me</a>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<p>No discount available at this time.</p>';
        }
        ?>
    </div>
</section>