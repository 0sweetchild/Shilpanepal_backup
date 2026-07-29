<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';

$orderId = $_SESSION['pending_order_id'] ?? null;
if ($orderId) {
    $s = $conn->prepare("UPDATE orders SET status='cancelled' WHERE id=?");
    $s->bind_param("i",$orderId); $s->execute();
    $s2 = $conn->prepare("UPDATE payments SET status='failed' WHERE order_id=?");
    $s2->bind_param("i",$orderId); $s2->execute();
}
unset($_SESSION['pending_order_id'],$_SESSION['pending_amount'],$_SESSION['order_form_submitted'],$_SESSION['esewa_transaction_uuid']);
$_SESSION['error'] = "Payment failed or was cancelled. Please try again.";
header("Location: " . SITE_URL . "/cart.php"); exit();
