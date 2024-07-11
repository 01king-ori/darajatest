<?php
session_start();
include 'functions.php';
include 'header.php';
include 'nav.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'buyer') {
    header('Location: login.php');
    exit();
}

$buyer = $_SESSION['username'];
$user = getUserByUsername($buyer);
$buyer_id = $user['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $seller = $_POST['seller'];
    $amount = $_POST['amount'];
    $partyA = '254715088731';
    $partyB = '147349';
    $seller_user = getUserByUsername($seller);
    $seller_id = $seller_user['id'];
    $transaction = createTransaction($buyer_id, $seller_id, $amount);

    // Initiate STK Push Payment
    $phoneNumber = '254715088731'; // Replace with the phone number to debit
    $callBackURL = 'https://d7ce-41-203-214-9.ngrok-free.app/callback.php'; // Replace with your callback URL
    $response = initiateSTKPush($transaction['id'], $amount, $partyA, $partyB, $phoneNumber, $callBackURL);
    if (isset($_SESSION['error_message'])) {
        echo "<p>Error: {$_SESSION['error_message']}</p>";
        unset($_SESSION['error_message']); // Clear error message after displaying
    }
}
$transactions = getTransactionsByUser($buyer_id, 'buyer');
?>

<h1>Buyer Page</h1>
<form method="post" action="buyer.php">
    <label>Seller: <input type="text" name="seller" required></label><br>
    <label>Amount: <input type="number" name="amount" required></label><br>
    <!-- <label>Phoneno: <input type="number" name="phoneNumber" required></label><br> -->
    
    <button type="submit">Create Transaction</button>
</form>

<h2>My Transactions</h2>
<ul>
    <?php foreach ($transactions as $transaction): ?>
        <li>Transaction with <?= htmlspecialchars($transaction['seller_id']) ?> - Amount: <?= htmlspecialchars($transaction['amount']) ?> - Status: <?= htmlspecialchars($transaction['status']) ?></li>
    <?php endforeach; ?>
</ul>

<?php include 'footer.php'; ?>
