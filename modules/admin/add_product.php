<?php
require_once __DIR__ . '/../../includes/admin_check.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';


$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];


    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    $target_dir = __DIR__ . '/../../assets/images/';
    $target_file = $target_dir . basename($image);

    if (move_uploaded_file($tmp, $target_file)) {
        $message = "Product added successfully!";
    } else {
        $message = "Image upload failed!";
    }


    $sql = "INSERT INTO products (name, price, description, image_url) 
            VALUES ('$name', '$price', '$description', '$image')";

    if ($conn->query($sql)) {
        $message = "Product added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<style>
.container {
    max-width: 600px;
    margin: 40px auto;
    background: #111;
    padding: 20px;
    border-radius: 10px;
    color: white;
}

.product-form label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
}

.product-form input,
.product-form textarea {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    background: #222;
    border: 1px solid #444;
    color: white;
    border-radius: 5px;
}

.product-form button {
    margin-top: 20px;
    width: 100%;
    padding: 12px;
    background: #00bcd4;
    color: black;
    border: none;
    font-weight: bold;
    cursor: pointer;
    border-radius: 5px;
}
</style>


<h2 class="container">Add Product</h2>

<div class="container">
    <p><?php echo $message; ?></p>

    <form method="POST" enctype="multipart/form-data" class="product-form">

        <label>Product Name</label>
        <input type="text" name="name" required>

        <label>Price</label>
        <input type="number" step="0.01" name="price" required>

        <label>Description</label>
        <textarea name="description" required></textarea>

        <label>Product Image</label>
        <input type="file" name="image" required>

        <button type="submit">Add Product</button>
    </form>
</div>


<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
