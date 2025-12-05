<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$logged_in = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? 'guest';
$cart_count = 0;
if ($logged_in && $role === 'customer') {
    $cart_count = $_SESSION['cart_item_count'] ?? 0;
}
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

                <!-- Always visible -->
                <li><a href="/Gadgetify/index.php">Home</a></li>
                <li><a href="/Gadgetify/pages/about.php">About</a></li>

                <?php if ($role === 'admin'): ?>

                    <!-- -------------------------- -->
                    <!-- ADMIN NAVIGATION -->
                    <!-- -------------------------- -->
                    <li><a href="/Gadgetify/modules/admin/dashboard.php">Admin Dashboard</a></li>
                    <li><a href="/Gadgetify/modules/admin/add_product.php">Add Product</a></li>
                    <li><a href="/Gadgetify/modules/admin/product_list.php">Manage Products</a></li>
                    <li><a href="/Gadgetify/modules/auth/logout.php">Logout</a></li>

                <?php elseif ($role === 'customer'): ?>

                    <!-- -------------------------- -->
                    <!-- CUSTOMER NAVIGATION -->
                    <!-- -------------------------- -->
                    <li><a href="/Gadgetify/pages/shop.php">Shop</a></li>
                    <li><a href="/Gadgetify/pages/dashboard.php">Dashboard</a></li>
                    <li><a href="/Gadgetify/modules/orders/order_history.php">My Orders</a></li>
                    <li><a href="/Gadgetify/pages/profile.php">Profile</a></li>
                    <li><a href="/Gadgetify/modules/auth/logout.php">Logout</a></li>

                <?php else: ?>

                    <!-- -------------------------- -->
                    <!-- GUEST NAVIGATION -->
                    <!-- -------------------------- -->
                    <li><a href="/Gadgetify/pages/shop.php">Shop</a></li>
                    <li><a href="/Gadgetify/modules/auth/login.php">Login</a></li>
                    <li><a href="/Gadgetify/modules/auth/register.php">Register</a></li>

                <?php endif; ?>

                <li><a href="/Gadgetify/pages/contact.php">Contact</a></li>
            </ul>
        </nav>

        <!-- CART (only for customers) -->
        <div class="header-icons">
            <?php if ($role === 'customer'): ?>
                <a href="/Gadgetify/modules/cart/cart.php" class="cart-icon">
                    🛒 (<?= $cart_count ?>)
                </a>
            <?php endif; ?>

            <button id="dark-mode-toggle" class="mode-toggle">🌑</button>
        </div>

    </div>
</header>

<main>
