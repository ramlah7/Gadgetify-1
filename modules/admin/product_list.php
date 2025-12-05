<?php
require_once __DIR__ . '/../../includes/admin_check.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Products
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);
?>


<style>
.container-table {
    max-width: 90%;
    margin: 40px auto;
    background: #111;
    padding: 20px;
    border-radius: 10px;
    color: white;
}

.product-table {
    width: 100%;
    border-collapse: collapse;
}

.product-table th,
.product-table td {
    border: 1px solid #444;
    padding: 10px;
    text-align: center;
}

.product-table th {
    background: #222;
}

.product-table img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 5px;
}

.action-btn {
    padding: 6px 12px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
}

.edit-btn {
    background: #00bcd4;
    color: black;
}

.delete-btn {
    background: #d9534f;
    color: white;
}
.add-btn {
    display: inline-block;
    background: #28a745;
    color: white;
    padding: 10px 18px;
    border-radius: 6px;
    text-decoration: none;
    margin-bottom: 20px;
    font-weight: bold;
}

.add-btn:hover {
    background: #218838;
}

<?php if (isset($_GET['msg'])): ?>
    <p style="color: #0f0; text-align:center;">
        <?php
            if ($_GET['msg'] === "deleted") echo "Product deleted successfully!";
            if ($_GET['msg'] === "not_found") echo "Product not found!";
            if ($_GET['msg'] === "invalid_id") echo "Invalid product ID!";
            if ($_GET['msg'] === "error") echo "Failed to delete product!";
        ?>
    </p>
<?php endif; ?>

</style>
<div class="container-table">

    <?php if (isset($_GET['msg'])): ?>
        <p style="color: #0f0; text-align:center; font-weight:bold;">
            <?php
                if ($_GET['msg'] === "deleted") echo "✔ Product deleted successfully!";
                if ($_GET['msg'] === "not_found") echo "✖ Product not found!";
                if ($_GET['msg'] === "invalid_id") echo "✖ Invalid product ID!";
                if ($_GET['msg'] === "error") echo "✖ Failed to delete product!";
            ?>
        </p>
    <?php endif; ?>

    <h2>Manage Products</h2>

    <!-- ADD PRODUCT BUTTON -->
    <a href="add_product.php" class="add-btn">+ Add New Product</a>


    <table class="product-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Name</th>
                <th>Price (PKR)</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php 
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { ?>
                
                <tr>
                    <td><?= $row['id']; ?></td>

                    <td>
                        <img src="/Gadgetify/assets/images/<?= $row['image_url']; ?>" alt="product">
                    </td>

                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= number_format($row['price']); ?></td>
                    <td><?= htmlspecialchars($row['description']); ?></td>

                    <td>
                        <a href="edit_product.php?id=<?= $row['id']; ?>">
                            <button class="action-btn edit-btn">Edit</button>
                        </a>

                        <a href="delete_product.php?id=<?= $row['id']; ?>" 
                           onclick="return confirm('Delete this product?');">
                            <button class="action-btn delete-btn">Delete</button>
                        </a>
                    </td>
                </tr>

        <?php }
        } else {
            echo "<tr><td colspan='6'>No products found.</td></tr>";
        }
        ?>
        </tbody>
    </table>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
