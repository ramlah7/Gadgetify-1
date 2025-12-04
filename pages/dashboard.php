<?php include "../includes/auth_check.php"; ?>
<?php 
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero-section">
    <div class="container">
        <h1>Upgrade Your Tech Lifestyle</h1>
        <p>Discover the latest smartwatches, headphones, and gaming gear.</p>
        <a href="shop.php" class="btn btn-primary">SHOP NOW</a>
    </div>
</section>

<section class="categories-section container">
    <h2>Explore Categories</h2>
    <div class="category-grid">
        <div class="category-card">🎧 Headphones</div>
        <div class="category-card">⌚ Smartwatches</div>
        <div class="category-card">📱 Phone Cases</div>
        <div class="category-card">🎮 Gaming Gear</div>
    </div>
</section>

<section class="featured-products container">
    <h2>Featured Gadgets</h2>
    <div class="product-grid">

        <?php
        $sql = "SELECT id, name, price, image FROM products ORDER BY id DESC LIMIT 4";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="product-card">';
                echo '    <img src="assets/images/' . htmlspecialchars($row["image"]) . '" alt="' . htmlspecialchars($row["name"]) . '">';
                echo '    <div class="product-info">';
                echo '        <h3>' . htmlspecialchars($row["name"]) . '</h3>';
                echo '        <p class="price">$' . number_format($row["price"], 2) . '</p>';
                echo '        <a href="product.php?id=' . $row["id"] . '" class="btn btn-add-cart">View Details</a>'; 
                echo '    </div>';
                echo '</div>';
            }
        } else {
            echo '<p>No featured products found.</p>';
        }

        $conn->close();
        ?>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
