<?php
session_start();
include 'functions.php';
include 'header.php';
include 'nav.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'seller') {
    header('Location: login.php');
    exit();
}

$seller = $_SESSION['username'];
$user = getUserByUsername($seller);
$seller_id = $user['id'];

$transactions = getTransactionsByUser($seller_id, 'seller');
?>

<h1>Seller Page</h1>
<h2>My Transactions</h2>
<ul>
    <?php foreach ($transactions as $transaction): ?>
        <li>Transaction with <?= htmlspecialchars($transaction['buyer_id']) ?> - Amount: <?= htmlspecialchars($transaction['amount']) ?> - Status: <?= htmlspecialchars($transaction['status']) ?></li>
    <?php endforeach; ?>
</ul>

<?php include 'footer.php'; ?>
