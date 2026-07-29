<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Order Confirmed';
require_once 'includes/db.php';
require_once 'includes/auth_check.php';
requireLogin();

$orderId = intval($_GET['order_id'] ?? 0);
$stmt = $conn->prepare("SELECT o.*,p.transaction_id,p.ref_id FROM orders o LEFT JOIN payments p ON p.order_id=o.id WHERE o.id=? AND o.user_id=?");
$stmt->bind_param("ii",$orderId,$_SESSION['user_id']); $stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
if (!$order) { header("Location: index.php"); exit(); }

$items = $conn->prepare("SELECT oi.*,p.name,p.image FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=?");
$items->bind_param("i",$orderId); $items->execute();
$orderItems = $items->get_result()->fetch_all(MYSQLI_ASSOC);
include 'includes/header.php';
?>
<main class="flex-grow">
<div class="container py-12">
    <div class="text-center mb-10 max-w-[600px] mx-auto">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-green-200">
            <i class="bi bi-check-lg text-green-600 text-4xl"></i>
        </div>
        <h2 class="font-playfair text-[2rem] font-bold text-dark mb-2">Order Confirmed!</h2>
        <p class="text-sm text-muted">Thank you, <?= htmlspecialchars(explode(' ',$order['full_name'])[0]) ?>! Your order has been placed successfully.</p>
        <div class="inline-block border border-border rounded-xl px-6 py-2.5 mt-5 bg-cream shadow-sm text-sm">
            <strong class="text-dark">Order #<?= $orderId ?></strong> &nbsp;|&nbsp;
            <span class="text-green-700 font-semibold"><i class="bi bi-shield-check me-1"></i>Payment Verified via eSewa</span>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-[800px] mx-auto">
        <div class="lg:col-span-12 space-y-6">
            <!-- Items Card -->
            <div class="border border-border rounded-2xl p-6 bg-white shadow-sm space-y-4">
                <h6 class="font-playfair text-base font-bold text-dark border-b border-border pb-3">Items Ordered</h6>
                <div class="divide-y divide-border/60">
                    <?php foreach ($orderItems as $item): ?>
                    <div class="flex justify-between items-center py-3 text-sm">
                        <div class="flex items-center gap-3">
                            <span class="bg-gray-100 text-dark text-xs font-bold px-2 py-0.5 rounded"><?= $item['quantity'] ?>x</span>
                            <span class="text-dark font-medium"><?= htmlspecialchars($item['name']) ?></span>
                        </div>
                        <span class="font-bold text-dark">NPR <?= number_format($item['price']*$item['quantity'],2) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="flex justify-between items-center pt-3 border-t border-border">
                    <strong class="text-sm text-dark">Total Paid</strong>
                    <strong class="text-primary text-lg font-bold">NPR <?= number_format($order['total_amount'],2) ?></strong>
                </div>
            </div>
            
            <!-- Delivery Card -->
            <div class="border border-border rounded-2xl p-6 bg-cream shadow-sm">
                <h6 class="font-playfair text-base font-bold text-dark border-b border-border/80 pb-3 mb-4">Delivery Details</h6>
                <div class="grid grid-cols-3 gap-y-3 text-xs">
                    <div class="text-muted font-semibold">Name</div><div class="col-span-2 text-dark font-medium"><?= htmlspecialchars($order['full_name']) ?></div>
                    <div class="text-muted font-semibold">Phone</div><div class="col-span-2 text-dark font-medium"><?= htmlspecialchars($order['phone']) ?></div>
                    <div class="text-muted font-semibold">Address</div><div class="col-span-2 text-dark font-medium"><?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?></div>
                    <div class="text-muted font-semibold">Payment</div><div class="col-span-2"><span class="bg-green-100 border border-green-200 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded">eSewa ✓</span></div>
                    <?php if ($order['ref_id']): ?>
                    <div class="text-muted font-semibold">Ref ID</div><div class="col-span-2 text-muted font-mono text-[10px]"><?= htmlspecialchars($order['ref_id']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?= SITE_URL ?>/orders.php" class="inline-flex justify-center items-center gap-2 py-2.5 px-6 border-2 border-primary text-primary hover:bg-primary hover:text-white rounded-lg transition duration-300 font-semibold text-sm"><i class="bi bi-bag-check"></i>View My Orders</a>
                <a href="<?= SITE_URL ?>/products.php" class="inline-flex justify-center items-center gap-2 py-2.5 px-6 bg-primary hover:bg-primary-dark text-white rounded-lg transition duration-300 font-semibold text-sm shadow"><i class="bi bi-bag"></i>Continue Shopping</a>
            </div>
        </div>
    </div>
</div>
</main>
<?php include 'includes/footer.php'; ?>
