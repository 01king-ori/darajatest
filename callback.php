

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'functions.php';
include 'db.php'; // Ensure this is included for database access

$data = file_get_contents('php://input');
error_log("Callback received: " . $data, 3, "C:\xampp3\htdocs\Escrow\error\error.txt"); // Log raw callback data
$response = json_decode($data, true);

if (isset($response['Body']['stkCallback']['ResultCode'])) {
    $resultCode = $response['Body']['stkCallback']['ResultCode'];
    $transactionId = $response['Body']['stkCallback']['CheckoutRequestID'];
    $mpesaReceiptNumber = $response['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];
    $amount = $response['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];

    if ($resultCode == 0) {
        // Payment successful
        updateTransactionStatus($transactionId, 'completed');
        // Store payment details in database
        $stmt = $conn->prepare("INSERT INTO payments (transaction_id, mpesa_receipt_number, amount, status) VALUES (?, ?, ?, 'completed')");
        $stmt->bind_param("isd", $transactionId, $mpesaReceiptNumber, $amount);
        if ($stmt->execute()) {
            $_SESSION['error_message'] = " stored payment successfully: " . $stmt->error;
        }
        $_SESSION['error_message'] = "Failed to store payment: " . $stmt->error;
    }
} else {
    // Payment failed
    updateTransactionStatus($transactionId, 'failed');
    $_SESSION['error_message'] = "Payment failed: $transactionId - ResultCode: $resultCode";
}
 else {
$_SESSION['error_message'] = "Invalid callback received: " . $data;
}

