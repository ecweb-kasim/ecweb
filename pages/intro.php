<?php
include_once 'includes/db_config.php';
?>
<section id="intro" class="position-relative mt-4">
    <div class="container-lg">
        <div class="swiper main-swiper">
            <div class="swiper-wrapper">
                <?php
                $stmt = $pdo->prepare("SELECT image, title FROM slides");
                $stmt->execute();
                $slides = $stmt->fetchAll();
                foreach ($slides as $slide) {
                    echo '<div class="swiper-slide">';
                    echo '<div class="card d-flex flex-row align-items-end border-0 large jarallax-keep-img">';
                    echo '<img src="../assets/images/slides/' . htmlspecialchars($slide['image']) . '" alt="shoes" class="img-fluid jarallax-img">';
                    echo '<div class="cart-concern p-3 m-3 p-lg-5 m-lg-5">';
                    echo '<h2 class="card-title display-3 light">' . htmlspecialchars($slide['title']) . '</h2>';
                    echo '<a href="index.php" class="text-uppercase light mt-3 d-inline-block text-hover fw-bold light-border">Shop Now</a>';
                    echo '</div></div></div>';
                }
                ?>
            </div>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>