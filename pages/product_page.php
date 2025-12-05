<?php
// In C:\xampp\htdocs\Gadgetify\pages\product_page.php
require_once __DIR__ . '/../includes/db.php';    // Line 2
require_once __DIR__ . '/../includes/header.php'; // Line 3

if (!isset($_GET['id'])) {
    echo "<p>Invalid product.</p>";
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM products WHERE id = $id";
$result = $conn->query($sql);

if ($result && $result->num_rows == 1) {
    $product = $result->fetch_assoc();
} else {
    echo "<p>Product not found.</p>";
    exit;
}
?>

<section class="product-details container">
    <img src="../assets/images/<?php echo htmlspecialchars($product['image_url']); ?>" width="300">

    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
    <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

    <a href="../modules/cart/add.php?id=<?php echo $product['id']; ?>&qty=1" class="btn btn-add">Add to Cart</a>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
