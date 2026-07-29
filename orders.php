<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'My Orders';
require_once 'includes/db.php';
require_once 'includes/auth_check.php';
requireLogin();

$stmt = $conn->prepare("SELECT o.*,COUNT(oi.id) as item_count FROM orders o LEFT JOIN order_items oi ON oi.order_id=o.id WHERE o.user_id=? GROUP BY o.id ORDER BY o.created_at DESC");
$stmt->bind_param("i",$_SESSION['user_id']); $stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$activeOrders = [];
$completedOrders = [];
foreach ($orders as $order) {
    if (in_array($order['status'], ['delivered', 'cancelled'])) {
        $completedOrders[] = $order;
    } else {
        $activeOrders[] = $order;
    }
}

include 'includes/header.php';
?>
<main class="flex-grow">
<div class="container py-12">
    <div class="flex items-center gap-3 mb-8">
        <h2 class="font-playfair text-[2rem] font-bold text-dark flex items-center gap-2">
            <i class="bi bi-bag-check"></i>My Orders
        </h2>
    </div>

    <?php if (empty($orders)): ?>
    <div class="text-center py-16 bg-cream/40 border border-border border-dashed rounded-2xl max-w-[600px] mx-auto">
        <i class="bi bi-bag-x text-[4rem] text-muted mb-4 d-block"></i>
        <h5 class="font-playfair text-lg font-bold text-dark mb-2">No orders yet</h5>
        <a href="<?= SITE_URL ?>/products.php" class="inline-block mt-2 py-2.5 px-6 bg-primary hover:bg-primary-dark text-white rounded-lg font-semibold text-sm transition duration-300">Start Shopping</a>
    </div>
    <?php else: ?>
    
    <!-- Tab Controls -->
    <div class="flex border-b border-border mb-8 gap-4 font-semibold text-sm">
        <button class="tab-btn pb-3 px-1 border-b-2 border-primary text-primary transition focus:outline-none" data-target="active-orders">
            Active Orders <span class="bg-primary/10 text-primary px-2 py-0.5 rounded text-xs font-bold ml-1"><?= count($activeOrders) ?></span>
        </button>
        <button class="tab-btn pb-3 px-1 border-b-2 border-transparent text-muted hover:text-dark transition focus:outline-none" data-target="completed-orders">
            Completed Orders <span class="bg-gray-100 text-dark/70 px-2 py-0.5 rounded text-xs font-bold ml-1"><?= count($completedOrders) ?></span>
        </button>
        <button class="tab-btn pb-3 px-1 border-b-2 border-transparent text-muted hover:text-dark transition focus:outline-none" data-target="all-orders">
            All Orders <span class="bg-gray-100 text-dark/70 px-2 py-0.5 rounded text-xs font-bold ml-1"><?= count($orders) ?></span>
        </button>
    </div>

    <!-- Tab Contents -->
    <div class="space-y-6">
        <!-- Active Orders Tab -->
        <div class="tab-panel" id="active-orders">
            <?php if (empty($activeOrders)): ?>
                <div class="text-center py-12 border border-border rounded-2xl bg-cream/10">
                    <i class="bi bi-bag text-[3rem] text-muted/65 mb-2 d-block"></i>
                    <p class="text-sm text-muted">No active orders found.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto bg-white border border-border rounded-2xl shadow-sm p-4">
                    <table class="w-full text-left border-collapse align-middle">
                        <thead>
                            <tr class="border-b border-border text-xs font-bold text-muted uppercase tracking-wider">
                                <th class="py-4 px-3">Order #</th>
                                <th class="py-4 px-3">Date</th>
                                <th class="py-4 px-3">Items</th>
                                <th class="py-4 px-3">Total</th>
                                <th class="py-4 px-3">Status</th>
                                <th class="py-4 px-3">Payment</th>
                                <th class="py-4 px-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60 text-sm">
                            <?php foreach ($activeOrders as $order): ?>
                            <tr class="hover:bg-cream/10 transition">
                                <td class="py-4 px-3 font-bold">#<?= $order['id'] ?></td>
                                <td class="py-4 px-3 text-muted"><?= date('M d, Y',strtotime($order['created_at'])) ?></td>
                                <td class="py-4 px-3 text-dark font-medium"><?= $order['item_count'] ?> item<?= $order['item_count']!=1?'s':'' ?></td>
                                <td class="py-4 px-3 font-semibold">NPR <?= number_format($order['total_amount'],2) ?></td>
                                <td class="py-4 px-3">
                                    <?php
                                    $statusColors = ['pending'=>'bg-yellow-100 text-yellow-800 border-yellow-200', 'processing'=>'bg-blue-100 text-blue-800 border-blue-200', 'shipped'=>'bg-green-100 text-green-800 border-green-200'];
                                    $col = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                    ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $col ?>"><?= ucfirst($order['status']) ?></span>
                                </td>
                                <td class="py-4 px-3">
                                    <?= $order['payment_status']==='paid' ? '<span class="bg-green-100 border border-green-200 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded">Paid</span>' : '<span class="bg-yellow-100 border border-yellow-200 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded">Unpaid</span>' ?>
                                </td>
                                <td class="py-4 px-3">
                                    <a href="<?= SITE_URL ?>/success.php?order_id=<?= $order['id'] ?>" class="inline-block py-1 px-4 border border-primary text-primary hover:bg-primary hover:text-white rounded-lg transition duration-300 font-semibold text-xs text-center">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Completed Orders Tab -->
        <div class="tab-panel hidden" id="completed-orders">
            <?php if (empty($completedOrders)): ?>
                <div class="text-center py-12 border border-border rounded-2xl bg-cream/10">
                    <i class="bi bi-check-circle text-[3rem] text-muted/65 mb-2 d-block"></i>
                    <p class="text-sm text-muted">No completed or cancelled orders found.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto bg-white border border-border rounded-2xl shadow-sm p-4">
                    <table class="w-full text-left border-collapse align-middle">
                        <thead>
                            <tr class="border-b border-border text-xs font-bold text-muted uppercase tracking-wider">
                                <th class="py-4 px-3">Order #</th>
                                <th class="py-4 px-3">Date</th>
                                <th class="py-4 px-3">Items</th>
                                <th class="py-4 px-3">Total</th>
                                <th class="py-4 px-3">Status</th>
                                <th class="py-4 px-3">Payment</th>
                                <th class="py-4 px-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60 text-sm">
                            <?php foreach ($completedOrders as $order): ?>
                            <tr class="hover:bg-cream/10 transition">
                                <td class="py-4 px-3 font-bold">#<?= $order['id'] ?></td>
                                <td class="py-4 px-3 text-muted"><?= date('M d, Y',strtotime($order['created_at'])) ?></td>
                                <td class="py-4 px-3 text-dark font-medium"><?= $order['item_count'] ?> item<?= $order['item_count']!=1?'s':'' ?></td>
                                <td class="py-4 px-3 font-semibold">NPR <?= number_format($order['total_amount'],2) ?></td>
                                <td class="py-4 px-3">
                                    <?php
                                    $statusColors = ['delivered'=>'bg-green-100 text-green-800 border-green-200', 'cancelled'=>'bg-red-100 text-red-800 border-red-200'];
                                    $col = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                    ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $col ?>"><?= ucfirst($order['status']) ?></span>
                                </td>
                                <td class="py-4 px-3">
                                    <?= $order['payment_status']==='paid' ? '<span class="bg-green-100 border border-green-200 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded">Paid</span>' : '<span class="bg-yellow-100 border border-yellow-200 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded">Unpaid</span>' ?>
                                </td>
                                <td class="py-4 px-3">
                                    <a href="<?= SITE_URL ?>/success.php?order_id=<?= $order['id'] ?>" class="inline-block py-1 px-4 border border-primary text-primary hover:bg-primary hover:text-white rounded-lg transition duration-300 font-semibold text-xs text-center">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- All Orders Tab -->
        <div class="tab-panel hidden" id="all-orders">
            <div class="overflow-x-auto bg-white border border-border rounded-2xl shadow-sm p-4">
                <table class="w-full text-left border-collapse align-middle">
                    <thead>
                        <tr class="border-b border-border text-xs font-bold text-muted uppercase tracking-wider">
                            <th class="py-4 px-3">Order #</th>
                            <th class="py-4 px-3">Date</th>
                            <th class="py-4 px-3">Items</th>
                            <th class="py-4 px-3">Total</th>
                            <th class="py-4 px-3">Status</th>
                            <th class="py-4 px-3">Payment</th>
                            <th class="py-4 px-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60 text-sm">
                        <?php foreach ($orders as $order): ?>
                        <tr class="hover:bg-cream/10 transition">
                            <td class="py-4 px-3 font-bold">#<?= $order['id'] ?></td>
                            <td class="py-4 px-3 text-muted"><?= date('M d, Y',strtotime($order['created_at'])) ?></td>
                            <td class="py-4 px-3 text-dark font-medium"><?= $order['item_count'] ?> item<?= $order['item_count']!=1?'s':'' ?></td>
                            <td class="py-4 px-3 font-semibold">NPR <?= number_format($order['total_amount'],2) ?></td>
                            <td class="py-4 px-3">
                                <?php
                                $statusColors = ['pending'=>'bg-yellow-100 text-yellow-800 border-yellow-200', 'processing'=>'bg-blue-100 text-blue-800 border-blue-200', 'shipped'=>'bg-green-100 text-green-800 border-green-200', 'delivered'=>'bg-green-100 text-green-800 border-green-200', 'cancelled'=>'bg-red-100 text-red-800 border-red-200'];
                                $col = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                ?>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $col ?>"><?= ucfirst($order['status']) ?></span>
                            </td>
                            <td class="py-4 px-3">
                                <?= $order['payment_status']==='paid' ? '<span class="bg-green-100 border border-green-200 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded">Paid</span>' : '<span class="bg-yellow-100 border border-yellow-200 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded">Unpaid</span>' ?>
                            </td>
                            <td class="py-4 px-3">
                                <a href="<?= SITE_URL ?>/success.php?order_id=<?= $order['id'] ?>" class="inline-block py-1 px-4 border border-primary text-primary hover:bg-primary hover:text-white rounded-lg transition duration-300 font-semibold text-xs text-center">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php endif; ?>
</div>
</main>

<script>
document.querySelectorAll('.tab-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        // Toggle tab highlights
        document.querySelectorAll('.tab-btn').forEach(function(b) {
            b.classList.remove('border-primary', 'text-primary');
            b.classList.add('border-transparent', 'text-muted', 'hover:text-dark');
        });
        this.classList.remove('border-transparent', 'text-muted', 'hover:text-dark');
        this.classList.add('border-primary', 'text-primary');

        // Toggle panel displays
        const target = this.getAttribute('data-target');
        document.querySelectorAll('.tab-panel').forEach(function(panel) {
            if (panel.id === target) {
                panel.classList.remove('hidden');
            } else {
                panel.classList.add('hidden');
            }
        });
    });
});
</script>
<?php include 'includes/footer.php'; ?>
