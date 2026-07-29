<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
requireLogin();

$data = $_GET['data'] ?? '';
if (empty($data)) {
    $_SESSION['error'] = "Payment verification failed.";
    header("Location: " . SITE_URL . "/cart.php");
    exit();
}

$decoded = json_decode(base64_decode($data), true);
$status = $decoded['status'] ?? '';
$transactionUuid = $decoded['transaction_uuid'] ?? '';
$refId = $decoded['transaction_id'] ?? '';

if ($status !== 'COMPLETE') {
    $_SESSION['error'] = "Payment was not completed.";
    header("Location: " . SITE_URL . "/cart.php");
    exit();
}

// Verify signature
$signedFields = $decoded['signed_field_names'] ?? '';
$receivedSig = $decoded['signature'] ?? '';
$fields = explode(',', $signedFields);
$msgParts = array();
foreach ($fields as $f) {
    $msgParts[] = $f . '=' . ($decoded[$f] ?? '');
}
$message = implode(',', $msgParts);
$expectedSig = base64_encode(hash_hmac('sha256', $message, ESEWA_SECRET, true));

if ($receivedSig !== $expectedSig) {
    $_SESSION['error'] = "Payment signature verification failed.";
    header("Location: " . SITE_URL . "/cart.php");
    exit();
}

// Find order
$stmt = $conn->prepare("SELECT order_id FROM payments WHERE transaction_id=?");
$stmt->bind_param("s", $transactionUuid);
$stmt->execute();
$pay = $stmt->get_result()->fetch_assoc();
if (!$pay) {
    $_SESSION['error'] = "Order not found.";
    header("Location: " . SITE_URL . "/cart.php");
    exit();
}

$orderId = $pay['order_id'];

// Update order & payment
$upd = $conn->prepare("UPDATE orders SET payment_status='paid',status='processing' WHERE id=?");
$upd->bind_param("i", $orderId);
$upd->execute();

$upd2 = $conn->prepare("UPDATE payments SET status='completed',ref_id=? WHERE transaction_id=?");
$upd2->bind_param("ss", $refId, $transactionUuid);
$upd2->execute();

// Reduce stock
$items = $conn->prepare("SELECT * FROM order_items WHERE order_id=?");
$items->bind_param("i", $orderId);
$items->execute();
$result = $items->get_result();
while ($item = $result->fetch_assoc()) {
    $upd3 = $conn->prepare("UPDATE products SET stock=stock-? WHERE id=?");
    $upd3->bind_param("ii", $item['quantity'], $item['product_id']);
    $upd3->execute();
}

// Clear cart for selected items only
$selectedIds = $_SESSION['checkout_item_ids'] ?? [];
if (!empty($selectedIds)) {
    $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
    $del = $conn->prepare("DELETE FROM cart WHERE user_id=? AND product_id IN ($placeholders)");
    $types = 'i' . str_repeat('i', count($selectedIds));
    $bindParams = array_merge([$_SESSION['user_id']], $selectedIds);
    $del->bind_param($types, ...$bindParams);
    $del->execute();
} else {
    $del = $conn->prepare("DELETE FROM cart WHERE user_id=?");
    $del->bind_param("i", $_SESSION['user_id']);
    $del->execute();
}

// Clear session
unset($_SESSION['pending_order_id'], $_SESSION['pending_amount'], $_SESSION['order_form_submitted'], $_SESSION['esewa_transaction_uuid'], $_SESSION['checkout_item_ids']);

header("Location: " . SITE_URL . "/success.php?order_id=" . $orderId);
exit();
