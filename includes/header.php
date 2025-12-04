<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gadgetify | Smart Gadgets Store</title>

    <link rel="stylesheet" href="/Gadgetify/assets/css/style.css">
</head>

<body>
<header class="main-header">
    <div class="container header-content">

        <!-- BRAND -->
        <a href="/Gadgetify/index.php" class="logo">GADGETIFY</a>

        <!-- NAVIGATION -->
        <nav class="main-nav">
            <ul>
                <li><a href="/Gadgetify/index.php">Home</a></li>
                <li><a href="/Gadgetify/pages/about.php">About</a></li>
                <li><a href="/Gadgetify/pages/shop.php">Shop</a></li>

                <?php if ($logged_in): ?>
                    <li><a href="/Gadgetify/pages/dashboard.php">Dashboard</a></li>
                    <li><a href="/Gadgetify/modules/orders/order_history.php">My Orders</a></li>
                    <li><a href="/Gadgetify/pages/profile.php">Profile</a></li>
                    <li><a href="/Gadgetify/modules/auth/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="/Gadgetify/modules/auth/login.php">Login</a></li>
                    <li><a href="/Gadgetify/modules/auth/register.php">Register</a></li>
                <?php endif; ?>

                <li><a href="/Gadgetify/pages/contact.php">Contact</a></li>
            </ul>
        </nav>

        <!-- CART & DARK MODE -->
        <div class="header-icons">
            <a href="/Gadgetify/modules/cart/cart.php" class="cart-icon">
                🛒 (<?= $cart_count ?>)
            </a>

            <button id="dark-mode-toggle" class="mode-toggle">🌑</button>
        </div>

    </div>
</header>

<main>
