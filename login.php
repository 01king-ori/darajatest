<?php
session_start();
include 'functions.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user = getUserByUsername($username);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'buyer') {
            header('Location: buyer.php');
        } else {
            header('Location: seller.php');
        }
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<h1>Login</h1>
<form method="post" action="login.php">
    <label>Username: <input type="text" name="username" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <button type="submit">Login</button>
</form>
<?php if (isset($error)) echo "<p>$error</p>"; ?>

<?php include 'footer.php'; ?>
