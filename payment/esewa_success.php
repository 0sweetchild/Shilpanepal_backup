<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/db.php';
require_once '../includes/auth_check.php';

requireLogin();

$encodedData = $_GET['data'] ?? '';
if (!$encodedData) {
    $_SESSION['error'] = "Payment process cancelled or invalid response from eSewa.";
    header("Location: " . SITE_URL . "/cart.php");
    exit();
}

$decoded = json_decode(base64_decode($encodedData), true);
$paymentStatus   = $decoded['status'] ?? '';
$transactionUuid = $decoded['transaction_uuid'] ?? '';
$referenceId     = $decoded['transaction_id'] ?? '';

if ($paymentStatus !== 'COMPLETE') {
    $_SESSION['error'] = "Your payment was not completed. Please try again.";
    header("Location: " . SITE_URL . "/cart.php");
    exit();
}

// Verify eSewa payload signature
$signedFieldNames = $decoded['signed_field_names'] ?? '';
$receivedSig      = $decoded['signature'] ?? '';
$fieldKeys        = explode(',', $signedFieldNames);

$signedParts = [];
foreach ($fieldKeys as $key) {
    $signedParts[] = $key . '=' . ($decoded[$key] ?? '');
}

$rawMessage  = implode(',', $signedParts);
$expectedSig = base64_encode(hash_hmac('sha256', $rawMessage, ESEWA_SECRET, true));

if ($receivedSig !== $expectedSig) {
    $_SESSION['error'] = "Payment verification signature mismatch. Contact support if your account was charged.";
    header("Location: " . SITE_URL . "/cart.php");
    exit();
}

// Locate order record
$paymentQuery = $conn->prepare("SELECT order_id FROM payments WHERE transaction_id = ?");
$paymentQuery->bind_param("s", $transactionUuid);
$paymentQuery->execute();
$paymentRecord = $paymentQuery->get_result()->fetch_assoc();

if (!$paymentRecord) {
    $_SESSION['error'] = "Could not find a matching order for this transaction.";
    header("Location: " . SITE_URL . "/cart.php");
    exit();
}

$orderId = (int)$paymentRecord['order_id'];

// Update order and payment records
$updateOrder = $conn->prepare("UPDATE orders SET payment_status = 'paid', status = 'processing' WHERE id = ?");
$updateOrder->bind_param("i", $orderId);
$updateOrder->execute();

$updatePayment = $conn->prepare("UPDATE payments SET status = 'completed', ref_id = ? WHERE transaction_id = ?");
$updatePayment->bind_param("ss", $referenceId, $transactionUuid);
$updatePayment->execute();

// Reduce inventory stock for ordered items
$itemsQuery = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
$itemsQuery->bind_param("i", $orderId);
$itemsQuery->execute();
$orderItems = $itemsQuery->get_result();

$reduceStock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
while ($item = $orderItems->fetch_assoc()) {
    $reduceStock->bind_param("ii", $item['quantity'], $item['product_id']);
    $reduceStock->execute();
}

// Remove purchased items from cart
$selectedCartIds = $_SESSION['checkout_item_ids'] ?? [];
if (!empty($selectedCartIds)) {
    $placeholders = implode(',', array_fill(0, count($selectedCartIds), '?'));
    $deleteCart = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id IN ($placeholders)");
    $bindTypes  = 'i' . str_repeat('i', count($selectedCartIds));
    $params     = array_merge([$_SESSION['user_id']], $selectedCartIds);
    $deleteCart->bind_param($bindTypes, ...$params);
    $deleteCart->execute();
} else {
    $deleteCart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $deleteCart->bind_param("i", $_SESSION['user_id']);
    $deleteCart->execute();
}

// Clean up pending session state
unset(
    $_SESSION['pending_order_id'],
    $_SESSION['pending_amount'],
    $_SESSION['order_form_submitted'],
    $_SESSION['esewa_transaction_uuid'],
    $_SESSION['checkout_item_ids']
);

header("Location: " . SITE_URL . "/success.php?order_id=" . $orderId);
exit();

