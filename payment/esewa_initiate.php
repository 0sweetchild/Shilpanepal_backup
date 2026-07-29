<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/db.php';
require_once '../includes/auth_check.php';

requireLogin();

if (!isset($_SESSION['pending_order_id'])) {
    header("Location: " . SITE_URL . "/cart.php");
    exit();
}

$orderId         = (int)$_SESSION['pending_order_id'];
$totalAmount     = (float)$_SESSION['pending_amount'];
$formattedAmount = number_format($totalAmount, 2, '.', '');
$transactionUuid = "ORDER-" . $orderId . "-" . time();

// Generate eSewa HMAC SHA256 signature
$signaturePayload = "total_amount={$formattedAmount},transaction_uuid={$transactionUuid},product_code=" . ESEWA_MERCHANT_CODE;
$signature        = base64_encode(hash_hmac('sha256', $signaturePayload, ESEWA_SECRET, true));

// Create initial payment tracking record
$paymentMethod = 'esewa';
$initialStatus = 'pending';
$savePayment   = $conn->prepare("INSERT INTO payments (order_id, transaction_id, method, amount, status) VALUES (?, ?, ?, ?, ?)");
$savePayment->bind_param("issds", $orderId, $transactionUuid, $paymentMethod, $totalAmount, $initialStatus);
$savePayment->execute();

$_SESSION['esewa_transaction_uuid'] = $transactionUuid;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connecting to eSewa...</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background-color: #f8f9fa;">

<div class="text-center p-4">
    <div class="spinner-border text-success mb-3" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <h5 class="fw-semibold text-dark mb-1">Redirecting to eSewa</h5>
    <p class="text-muted small mb-0">Please wait a moment while we transfer you securely to complete your payment.</p>
</div>

<form id="esewaForm" action="<?= ESEWA_URL ?>" method="POST" class="d-none">
    <input type="hidden" name="amount"                  value="<?= $formattedAmount ?>">
    <input type="hidden" name="tax_amount"              value="0.00">
    <input type="hidden" name="total_amount"            value="<?= $formattedAmount ?>">
    <input type="hidden" name="transaction_uuid"        value="<?= $transactionUuid ?>">
    <input type="hidden" name="product_code"            value="<?= ESEWA_MERCHANT_CODE ?>">
    <input type="hidden" name="product_service_charge"  value="0.00">
    <input type="hidden" name="product_delivery_charge" value="0.00">
    <input type="hidden" name="success_url"             value="<?= SITE_URL ?>/payment/esewa_success.php">
    <input type="hidden" name="failure_url"             value="<?= SITE_URL ?>/payment/esewa_failure.php">
    <input type="hidden" name="signed_field_names"      value="total_amount,transaction_uuid,product_code">
    <input type="hidden" name="signature"               value="<?= $signature ?>">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            document.getElementById('esewaForm').submit();
        }, 800);
    });
</script>
</body>
</html>