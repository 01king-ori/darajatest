<?php
include 'db.php';

function getAccessToken() {
    $consumerKey = 'E0mdVoNp0abhWvOJvXGuKwvzYzsTadMY3ubh9G1F3gSOGZyl';
    $consumerSecret = 'Lj4n8WGGbrIBh8JSStW9FBfubJnbM1AmHl32PAblAPqhbuV9ZZxPoOtqwNZhrmGQ';
    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials, 'Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response);
    return $result->access_token;
}

function initiateSTKPush($transaction_id, $amount, $partyA, $partyB, $phoneNumber, $callBackURL) {
    $accessToken = getAccessToken();
    $timestamp = date('YmdHis');
    $password = base64_encode('938749' . 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919' . $timestamp);

    $curl_post_data = [
        'BusinessShortCode' => '938749', // Replace with your BusinessShortCode
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $partyA, // Replace with your Shortcode
        'PartyB' => '938749', // Replace with your BusinessShortCode
        'PhoneNumber' => $phoneNumber, // Replace with the phone number to debit
        'CallBackURL' => $callBackURL,
        'AccountReference' => 'Transaction ' . $transaction_id,
        'TransactionDesc' => 'Payment for Transaction ' . $transaction_id
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken, 'Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response);
    print_r($result);
    return $result;
}

function createTransaction($buyer_id, $seller_id, $amount) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO transactions (buyer_id, seller_id, amount) VALUES (?, ?, ?)");
    $stmt->bind_param("iid", $buyer_id, $seller_id, $amount);
    $stmt->execute();
    return ['id' => $stmt->insert_id, 'buyer_id' => $buyer_id, 'seller_id' => $seller_id, 'amount' => $amount, 'status' => 'pending'];
}

function getUserByUsername($username) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getTransactionsByUser($user_id, $role) {
    global $conn;
    if ($role == 'buyer') {
        $stmt = $conn->prepare("SELECT * FROM transactions WHERE buyer_id = ?");
    } else {
        $stmt = $conn->prepare("SELECT * FROM transactions WHERE seller_id = ?");
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getUsernameById($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user['username'];
}
?>