<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Completed Orders';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = intval($_POST['order_id']);
    $status  = $_POST['status'];
    $allowed = ['pending','processing','shipped','delivered','cancelled'];
    if (in_array($status, $allowed)) {
        $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $orderId);
        $stmt->execute();
        
        if ($status === 'delivered') {
            $os = $conn->prepare("SELECT email FROM orders WHERE id=?");
            $os->bind_param("i", $orderId); $os->execute();
            $orderEmail = $os->get_result()->fetch_assoc()['email'];
            $el = $conn->prepare("INSERT INTO email_logs (order_id,type,sent_to) VALUES (?,'order_delivered',?)");
            $el->bind_param("is", $orderId, $orderEmail); $el->execute();
        }
    }
    $_SESSION['success'] = "Order status updated!";
    header("Location: completed.php"); exit();
}

// Fetch Completed Orders (status = 'delivered')
$orders = $conn->query("
    SELECT o.*, COUNT(oi.id) as item_count,
    GROUP_CONCAT(CONCAT(p.name, ' (x', oi.quantity, ')') SEPARATOR '||') as items_list
    FROM orders o
    LEFT JOIN order_items oi ON oi.order_id=o.id
    LEFT JOIN products p ON oi.product_id=p.id
    WHERE o.status='delivered'
    GROUP BY o.id
    ORDER BY o.updated_at DESC
");

// Total completed orders and revenue
$totals = $conn->query("SELECT COUNT(*) as cnt, COALESCE(SUM(total_amount), 0) as rev FROM orders WHERE status='delivered'")->fetch_assoc();
$totalCompletedCount = $totals['cnt'];
$totalCompletedRevenue = $totals['rev'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Completed Orders | ShilpaNepal Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              primary: {
                DEFAULT: '#8E1C24',
                dark: '#580F14',
                light: '#FAF3F3',
              },
              accent: {
                DEFAULT: '#C5A880',
                dark: '#A48962',
              },
              cream: '#FAF7F2',
              dark: '#1F1616',
              border: '#EBE5DC',
              muted: '#847777',
            },
            fontFamily: {
              playfair: ['Playfair Display', 'serif'],
              sans: ['Plus Jakarta Sans', 'sans-serif'],
            }
          }
        }
      }
    </script>
</head>
<body class="bg-[#FAF8F5] text-dark font-sans antialiased">
<?php include '../includes/admin_sidebar.php'; ?>
<div class="ml-64 min-h-screen flex flex-col">
<?php include '../includes/admin_topbar.php'; ?>
<div class="p-8 space-y-6 flex-grow">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-3 text-xs font-semibold flex justify-between items-center mb-4" id="successAlert">
            <span><i class="bi bi-check-circle me-2"></i><?= $_SESSION['success'] ?></span>
            <button type="button" class="text-green-855 hover:text-green-955" onclick="document.getElementById('successAlert').style.display='none'"><i class="bi bi-x-lg"></i></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="flex justify-between items-center mb-2">
        <div>
            <h4 class="font-playfair text-[2rem] font-bold text-dark leading-tight">Completed Orders</h4>
            <small class="text-sm text-muted">Archive of all successfully delivered customer orders</small>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Revenue Card -->
        <div class="bg-white border border-border rounded-2xl p-6 shadow-sm flex items-start justify-between">
            <div>
                <div class="text-[11px] font-bold text-muted uppercase tracking-wider">Delivered Revenue</div>
                <div class="font-playfair text-[2rem] font-bold text-green-600 mt-2.5">NPR <?= number_format($totalCompletedRevenue,2) ?></div>
                <small class="text-xs text-muted">Total value of completed orders</small>
            </div>
            <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl"><i class="bi bi-cash-coin"></i></div>
        </div>
        
        <!-- Deliveries Card -->
        <div class="bg-white border border-border rounded-2xl p-6 shadow-sm flex items-start justify-between">
            <div>
                <div class="text-[11px] font-bold text-muted uppercase tracking-wider">Completed Deliveries</div>
                <div class="font-playfair text-[2rem] font-bold text-primary mt-2.5"><?= $totalCompletedCount ?></div>
                <small class="text-xs text-muted">Orders successfully fulfilled</small>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl"><i class="bi bi-check2-all"></i></div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white border border-border rounded-2xl p-6 shadow-sm">
        <h6 class="font-bold text-dark text-sm tracking-wide uppercase mb-4">Delivered Orders Log</h6>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse align-middle">
                <thead>
                    <tr class="border-b border-border text-xs font-bold text-muted uppercase tracking-wider">
                        <th class="pb-3">#</th>
                        <th class="pb-3">Customer Details</th>
                        <th class="pb-3">Items Ordered</th>
                        <th class="pb-3">Total Paid</th>
                        <th class="pb-3">Payment</th>
                        <th class="pb-3">Ordered Date</th>
                        <th class="pb-3">Completed Date</th>
                        <th class="pb-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/60 text-xs font-medium text-dark/95">
                <?php if ($orders->num_rows === 0): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-8">No completed orders found.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($o = $orders->fetch_assoc()): ?>
                        <tr class="hover:bg-cream/10 transition">
                            <td class="py-4 font-bold">#<?= $o['id'] ?></td>
                            <td class="py-4 pr-2">
                                <div class="font-bold text-dark mb-1"><?= htmlspecialchars($o['full_name']) ?></div>
                                <div class="text-[10px] text-muted space-y-0.5">
                                    <div class="flex items-center gap-1"><i class="bi bi-envelope"></i><?= htmlspecialchars($o['email']) ?></div>
                                    <div class="flex items-center gap-1"><i class="bi bi-telephone"></i><?= htmlspecialchars($o['phone']) ?></div>
                                    <div class="flex items-center gap-1"><i class="bi bi-geo-alt"></i><?= htmlspecialchars($o['address']) ?>, <?= htmlspecialchars($o['city']) ?></div>
                                </div>
                            </td>
                            <td class="py-4 pr-2">
                                <?php if ($o['items_list']): ?>
                                    <ul class="space-y-1 text-[11px] text-dark/80">
                                        <?php 
                                        $items = explode('||', $o['items_list']);
                                        foreach ($items as $item):
                                        ?>
                                            <li class="flex items-center gap-1"><i class="bi bi-caret-right-fill text-accent"></i><?= htmlspecialchars($item) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <span class="text-muted text-[11px]">No items</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 font-semibold text-dark">NPR <?= number_format($o['total_amount'],2) ?></td>
                            <td class="py-4">
                                <?= $o['payment_status'] === 'paid' ? '<span class="bg-green-105 bg-green-100 border border-green-200 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded">Paid</span>' : '<span class="bg-yellow-100 border border-yellow-200 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded">Unpaid</span>' ?>
                            </td>
                            <td class="py-4 text-muted" style="font-size:11px;"><?= date('M d, Y H:i', strtotime($o['created_at'])) ?></td>
                            <td class="py-4 text-green-700 font-bold" style="font-size:11px;"><?= date('M d, Y H:i', strtotime($o['updated_at'])) ?></td>
                            <td class="py-4">
                                <form method="POST" class="flex gap-2 items-center">
                                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                    <select name="status" class="px-2 py-1 text-[11px] border border-border rounded-lg bg-white focus:outline-none focus:border-primary w-28">
                                        <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                                            <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="update_status" class="p-1 px-1.5 bg-primary hover:bg-primary-dark text-white rounded transition text-[11px] font-bold"><i class="bi bi-check2"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</body>
</html>
