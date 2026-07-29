<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'My Cart';
require_once 'includes/db.php';
require_once 'includes/auth_check.php';
requireLogin();

// Clear pending order session variables and delete abandoned order from DB when visiting cart
if (isset($_SESSION['pending_order_id'])) {
    $orderId = $_SESSION['pending_order_id'];
    $stmtDel = $conn->prepare("DELETE FROM orders WHERE id=? AND status='pending' AND payment_status='unpaid'");
    $stmtDel->bind_param("i", $orderId);
    $stmtDel->execute();
}
unset($_SESSION['pending_order_id'], $_SESSION['pending_amount'], $_SESSION['order_form_submitted']);

// Direct GET fallback for delete action (if JS fails or is blocked)
if (isset($_GET['action']) && $_GET['action'] === 'remove') {
    $remove_prod_id = intval($_GET['product_id'] ?? 0);
    $stmtRemove = $conn->prepare("DELETE FROM cart WHERE user_id=? AND product_id=?");
    $stmtRemove->bind_param("ii", $_SESSION['user_id'], $remove_prod_id);
    $stmtRemove->execute();
    header("Location: " . SITE_URL . "/cart.php");
    exit();
}

$stmt = $conn->prepare("SELECT c.*,p.name,p.price,p.image,p.slug,p.stock,(c.quantity*p.price) as subtotal FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=? ORDER BY c.added_at DESC");
$stmt->bind_param("i",$_SESSION['user_id']); $stmt->execute();
$items      = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$cartTotal  = array_sum(array_column($items,'subtotal'));
$itemCount  = array_sum(array_column($items,'quantity'));
$shipping   = $cartTotal >= 2000 ? 0 : 150;
$grandTotal = $cartTotal + $shipping;
include 'includes/header.php';
?>
<?php if (isset($_GET['mock_confirm'])): ?>
<script>window.confirm = function() { return true; };</script>
<?php endif; ?>
<main class="flex-grow">
<div class="container py-12">
    <div class="flex items-center gap-3 mb-8">
        <h2 class="font-playfair text-[2rem] font-bold text-dark flex items-center gap-2">
            <i class="bi bi-cart3"></i>My Cart
        </h2>
        <?php if ($itemCount>0): ?>
            <span id="cartHeaderBadge" class="bg-primary text-white text-xs font-semibold px-3 py-1 rounded-full"><?= $itemCount ?> item<?= $itemCount!=1?'s':'' ?></span>
        <?php endif; ?>
    </div>
    
    <?php if (empty($items)): ?>
    <div class="text-center py-16 bg-cream/40 border border-border border-dashed rounded-2xl">
        <i class="bi bi-cart-x text-[5rem] text-muted mb-4 d-block"></i>
        <h4 class="font-playfair text-lg font-bold text-dark mb-2">Your cart is empty</h4>
        <a href="<?= SITE_URL ?>/products.php" class="inline-flex items-center gap-2 py-2.5 px-6 bg-primary hover:bg-primary-dark text-white rounded-lg font-semibold text-sm transition duration-300"><i class="bi bi-bag"></i>Start Shopping</a>
    </div>
    <?php else: ?>
    <form method="POST" action="<?= SITE_URL ?>/checkout.php" id="cartForm">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8">
            <div class="overflow-x-auto bg-white border border-border rounded-2xl shadow-sm p-4">
                <table class="w-full text-left border-collapse align-middle">
                    <thead>
                        <tr class="border-b border-border text-xs font-bold text-muted uppercase tracking-wider">
                            <th class="py-4 px-3" width="40"><input type="checkbox" id="selectAll" checked class="w-4 h-4 text-primary focus:ring-primary border-border rounded"></th>
                            <th class="py-4 px-3">Product</th>
                            <th class="py-4 px-3 text-center">Price</th>
                            <th class="py-4 px-3 text-center">Quantity</th>
                            <th class="py-4 px-3 text-center">Total</th>
                            <th class="py-4 px-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        <?php foreach ($items as $item): ?>
                        <tr class="hover:bg-cream/10 transition">
                            <td class="py-4 px-3">
                                <input type="checkbox" name="selected_items[]" value="<?= $item['product_id'] ?>" class="item-checkbox w-4 h-4 text-primary focus:ring-primary border-border rounded" checked data-price="<?= $item['price'] ?>" data-qty="<?= $item['quantity'] ?>">
                            </td>
                            <td class="py-4 px-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-[70px] h-[70px] border border-border rounded-lg overflow-hidden bg-cream flex-shrink-0 flex items-center justify-center">
                                        <?php if ($item['image']): ?><img src="<?= SITE_URL ?>/assets/images/uploads/<?= htmlspecialchars($item['image']) ?>" class="w-full h-full object-cover" alt="">
                                        <?php else: ?><i class="bi bi-image text-muted"></i><?php endif; ?>
                                    </div>
                                    <div class="min-w-0">
                                        <a href="<?= SITE_URL ?>/product.php?slug=<?= urlencode($item['slug']) ?>" class="text-sm text-dark font-semibold hover:text-primary transition block truncate max-w-[200px]"><?= htmlspecialchars($item['name']) ?></a>
                                        <?php if ($item['stock']<=LOW_STOCK_LIMIT): ?><div><small class="text-yellow-800 text-[11px] font-semibold bg-yellow-100 border border-yellow-200 px-1.5 py-0.5 rounded inline-flex items-center gap-1 mt-1"><i class="bi bi-exclamation-triangle"></i>Only <?= $item['stock'] ?> left</small></div><?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-3 text-center text-sm font-semibold">NPR <?= number_format($item['price'],2) ?></td>
                            <td class="py-4 px-3 text-center">
                                <div class="inline-flex items-center border border-border rounded-lg bg-white overflow-hidden h-9">
                                    <button type="button" class="px-2.5 hover:bg-cream text-dark transition qty-btn" data-id="<?= $item['product_id'] ?>" data-action="decrease">−</button>
                                    <span class="qty-display px-2 text-sm font-semibold"><?= $item['quantity'] ?></span>
                                    <button type="button" class="px-2.5 hover:bg-cream text-dark transition qty-btn" data-id="<?= $item['product_id'] ?>" data-action="increase">+</button>
                                </div>
                            </td>
                            <td class="py-4 px-3 text-center text-sm font-bold text-primary item-total">NPR <?= number_format($item['subtotal'],2) ?></td>
                            <td class="py-4 px-3 text-center">
                                <a href="<?= SITE_URL ?>/cart.php?action=remove&product_id=<?= $item['product_id'] ?>" class="text-red-500 hover:text-red-700 hover:scale-105 transition remove-item p-1.5 border border-red-200 hover:bg-red-50 rounded-lg inline-block" data-id="<?= $item['product_id'] ?>"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-between mt-6">
                <a href="<?= SITE_URL ?>/products.php" class="inline-flex items-center gap-1.5 py-2.5 px-6 border border-border text-dark hover:bg-cream rounded-lg transition font-semibold text-sm"><i class="bi bi-arrow-left"></i>Continue Shopping</a>
            </div>
        </div>
        <div class="lg:col-span-4">
            <div class="bg-white border border-border rounded-2xl p-6 shadow-sm space-y-4">
                <h5 class="font-playfair text-[1.4rem] font-bold text-dark border-b border-border pb-3">Order Summary</h5>
                <div class="flex justify-between text-sm">
                    <span class="text-muted" id="summarySubtotalLabel">Subtotal (<?= $itemCount ?> items)</span>
                    <span id="summarySubtotal" class="font-semibold text-dark">NPR <?= number_format($cartTotal,2) ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-muted">Shipping</span>
                    <span id="summaryShipping"><?= $shipping===0 ? '<span class="text-green-600 font-bold">FREE</span>' : 'NPR '.number_format($shipping,2) ?></span>
                </div>
                <div id="shippingAlertContainer">
                    <?php if ($shipping>0): ?>
                    <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-3 text-xs font-semibold flex items-center gap-1.5 shipping-info-alert"><i class="bi bi-truck"></i>Add NPR <span id="shippingThresholdValue"><?= number_format(2000-$cartTotal,2) ?></span> more for free shipping!</div>
                    <?php endif; ?>
                </div>
                <hr class="border-border">
                <div class="flex justify-between items-center py-2">
                    <span class="font-bold text-dark">Grand Total</span>
                    <strong id="cartTotal" class="text-primary text-[1.3rem] font-bold">NPR <?= number_format($grandTotal,2) ?></strong>
                </div>
                <button type="submit" id="checkoutBtn" class="w-full py-3 bg-primary hover:bg-primary-dark text-white rounded-lg font-semibold text-sm transition duration-300 flex items-center justify-center gap-2"><i class="bi bi-bag-check"></i>Proceed to Checkout</button>
                <div class="text-center pt-2">
                    <small class="text-muted flex items-center justify-center gap-1 text-[11px]"><i class="bi bi-shield-lock"></i>Secure checkout via eSewa</small>
                </div>
            </div>
        </div>
    </div>
    </form>
    <?php endif; ?>
</div>
</main>
<?php include 'includes/footer.php'; ?>
