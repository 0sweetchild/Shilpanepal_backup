<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
requireLogin();

if (!isset($_SESSION['pending_order_id'])) {
    header("Location: " . SITE_URL . "/cart.php"); exit();
}

$orderId         = $_SESSION['pending_order_id'];
$amount          = $_SESSION['pending_amount'];
$formattedAmount = number_format((float)$amount, 2, '.', '');
$transactionUuid = "ORDER-" . $orderId . "-" . time();

// HMAC SHA256 Signature
$message   = "total_amount={$formattedAmount},transaction_uuid={$transactionUuid},product_code=" . ESEWA_MERCHANT_CODE;
$signature = base64_encode(hash_hmac('sha256', $message, ESEWA_SECRET, true));

// Save payment record — all variables, no literals in bind_param
$method = 'esewa';
$status = 'pending';
$ins = $conn->prepare("INSERT INTO payments (order_id, transaction_id, method, amount, status) VALUES (?, ?, ?, ?, ?)");
$ins->bind_param("issds", $orderId, $transactionUuid, $method, $amount, $status);
$ins->execute();

$_SESSION['esewa_transaction_uuid'] = $transactionUuid;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redirecting to eSewa...</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height:100vh;background:#f8f9fa;">
<div class="text-center">
    <div class="spinner-border text-success mb-3" style="width:3rem;height:3rem;"></div>
    <h5>Redirecting to eSewa...</h5>
    <p class="text-muted small">Please wait while we connect to eSewa payment gateway.</p>
</div>
<form id="esewaForm" action="<?= ESEWA_URL ?>" method="POST" style="display:none;">
    <input type="hidden" name="amount"                      value="<?= $formattedAmount ?>">
    <input type="hidden" name="tax_amount"                  value="0.00">
    <input type="hidden" name="total_amount"                value="<?= $formattedAmount ?>">
    <input type="hidden" name="transaction_uuid"            value="<?= $transactionUuid ?>">
    <input type="hidden" name="product_code"                value="<?= ESEWA_MERCHANT_CODE ?>">
    <input type="hidden" name="product_service_charge"      value="0.00">
    <input type="hidden" name="product_delivery_charge"     value="0.00">
    <input type="hidden" name="success_url"                 value="<?= SITE_URL ?>/payment/esewa_success.php">
    <input type="hidden" name="failure_url"                 value="<?= SITE_URL ?>/payment/esewa_failure.php">
    <input type="hidden" name="signed_field_names"          value="total_amount,transaction_uuid,product_code">
    <input type="hidden" name="signature"                   value="<?= $signature ?>">
</form>
<script>
    setTimeout(function() {
        document.getElementById('esewaForm').submit();
    }, 1000);
</script>
</body>
</html>