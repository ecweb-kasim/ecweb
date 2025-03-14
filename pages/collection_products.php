<?php
include_once 'includes/db_config.php';
?>
<section id="collection-products" class="py-2 my-2 py-md-5 my-md-5">
    <div class="container-md">
        <div class="row">
            <?php
            $stmt = $pdo->prepare("SELECT image, title FROM collections");
            $stmt->execute();
            $collections = $stmt->fetchAll();
            foreach ($collections as $collection) {
                echo '<div class="col-lg-6 col-md-6 mb-4">';
                echo '<div class="collection-card card border-0 d-flex flex-row align-items-end jarallax-keep-img">';
                echo '<img src="assets/images/collections/' . htmlspecialchars($collection['image']) . '" alt="product-item" class="border-rounded-10 img-fluid jarallax-img">';
                echo '<div class="card-detail p-3 m-3 p-lg-5 m-lg-5">';
                echo '<h3 class="card-title display-3"><a href="#">' . htmlspecialchars($collection['title']) . '</a></h3>';
                echo '<a href="index.php" class="text-uppercase mt-3 d-inline-block text-hover fw-bold">Shop Now</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>