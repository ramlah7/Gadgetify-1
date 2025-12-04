<?php 
include("../../includes/header.php");
include("../../includes/db.php");

$name = $email = $password = $confirm = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check if email exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            // Register user
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed);

            if ($stmt->execute()) {
                header("Location: login.php?success=1");
                exit;
            } else {
                $error = "Something went wrong.";
            }
        }
    }
}
?>

<div class="container" style="max-width:500px; margin-top:40px;">
    <h2>Create Account</h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" required class="form-input">

        <label>Email</label>
        <input type="email" name="email" required class="form-input">

        <label>Password</label>
        <input type="password" name="password" required class="form-input">

        <label>Confirm Password</label>
        <input type="password" name="confirm" required class="form-input">

        <button type="submit" class="btn-primary" style="width:100%; margin-top:15px;">Register</button>
    </form>

    <p style="text-align:center; margin-top:10px;">
        Already have an account? <a href="login.php">Login</a>
    </p>
</div>

<?php include("../../includes/footer.php"); ?>
