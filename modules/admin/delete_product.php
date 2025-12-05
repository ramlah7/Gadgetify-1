<?php
require_once __DIR__ . '/../../includes/admin_check.php';
require_once __DIR__ . '/../../includes/db.php';

// Check if product ID exists in URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: product_list.php?msg=invalid_id");
    exit;
}

$product_id = intval($_GET['id']);

// 1️⃣ Fetch product to get image name
$sql = "SELECT image_url FROM products WHERE id = $product_id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    header("Location: product_list.php?msg=not_found");
    exit;
}

$product = $result->fetch_assoc();
$image_name = $product['image_url'];

// 2️⃣ Delete product from database
$delete_sql = "DELETE FROM products WHERE id = $product_id";

if ($conn->query($delete_sql)) {

    // 3️⃣ Delete image file if it exists
    $image_path = __DIR__ . '/../../assets/images/' . $image_name;

    if (file_exists($image_path)) {
        unlink($image_path);
    }

    header("Location: product_list.php?msg=deleted");
} else {
    header("Location: product_list.php?msg=error");
}

exit;
?>
