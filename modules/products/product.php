

<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';



if (!isset($_GET['id'])) {
    echo "<p>Invalid product ID</p>";
    include 'includes/footer.php';
    exit();
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM products WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<p>Product not found</p>";
} else {
    $p = $result->fetch_assoc();
}
?>
<section class="product-page container">
    <div class="product">
        <img src="assets/images/<?php echo $p['image']; ?>" />

        <div class="details">
            <h2><?php echo $p['name']; ?></h2>
            <p class="price">$<?php echo number_format($p['price'],2); ?></p>
            <p><?php echo $p['description']; ?></p>

            <a href="cart/add.php?id=<?php echo $p['id']; ?>" class="btn btn-add-cart">
                Add to Cart
            </a>
        </div>
    </div>
</section>

<?php include '../../includes/footer.php'; ?>
