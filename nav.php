<?php if (isset($_SESSION['username'])): ?>
    <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> | <a href="logout.php">Logout</a></p>
<?php else: ?>
    <p><a href="login.php">Login</a> | <a href="signup.php">Signup</a></p>
<?php endif; ?>
