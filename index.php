<?php
session_start();
?>

<?php include "includes/header.php"; ?>

<section class="hero">
    <div class="container">
        <h1>Welcome to Gadgetify</h1>
        <p>Your one-stop store for smart gadgets & accessories!</p>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="/Gadgetify/modules/auth/login.php" class="btn">Login</a>
            <a href="/Gadgetify/modules/auth/register.php" class="btn">Sign Up</a>
        <?php else: ?>
            <a href="/Gadgetify/pages/dashboard.php" class="btn">Go to Dashboard</a>
            <a href="/Gadgetify/pages/shop.php" class="btn">Visit Shop</a>
        <?php endif; ?>
    </div>
</section>

<section class="featured">
    <div class="container">
        <h2>Featured Gadgets</h2>
        <p>Explore our best-selling items.</p>

        <a href="/Gadgetify/pages/shop.php" class="btn">Browse Shop</a>
    </div>
</section>

<?php include "includes/footer.php"; ?>
