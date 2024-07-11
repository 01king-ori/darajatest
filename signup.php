<?php
session_start();
include 'functions.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("sss", $username, $password, $role);
    if ($stmt->execute() === false) {
        die("Error executing statement: " . $stmt->error);
    }
    $stmt->execute();

    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;

    if ($role == 'buyer') {
        header('Location: buyer.php');
    } else {
        header('Location: seller.php');
    }
    exit();
}
?>

<h1>Signup</h1>
<form method="post" action="signup.php">
    <label>Username: <input type="text" name="username" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <label>Role:
        <select name="role" required>
            <option value="buyer">Buyer</option>
            <option value="seller">Seller</option>
        </select>
    </label><br>
    <button type="submit">Signup</button>
</form>

<?php include 'footer.php'; ?>
