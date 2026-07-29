<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/db.php';
require_once '../includes/auth_check.php';

$orderId = $_SESSION['pending_order_id'] ?? null;

if ($orderId) {
    $cancelOrder = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
    $cancelOrder->bind_param("i", $orderId);
    $cancelOrder->execute();

    $cancelPayment = $conn->prepare("UPDATE payments SET status = 'failed' WHERE order_id = ?");
    $cancelPayment->bind_param("i", $orderId);
    $cancelPayment->execute();
}

unset(
    $_SESSION['pending_order_id'],
    $_SESSION['pending_amount'],
    $_SESSION['order_form_submitted'],
    $_SESSION['esewa_transaction_uuid']
);

$_SESSION['error'] = "Your payment was cancelled or could not be processed. Please try placing your order again.";
header("Location: " . SITE_URL . "/cart.php");
exit();

