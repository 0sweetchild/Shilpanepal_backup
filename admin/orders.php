<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Orders';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_status'])) {
    $orderId = intval($_POST['order_id']);
    $status  = $_POST['status'];
    $allowed = ['pending','processing','shipped','delivered','cancelled'];
    if (in_array($status,$allowed)) {
        $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->bind_param("si",$status,$orderId); $stmt->execute();
        if ($status==='shipped') {
            $os = $conn->prepare("SELECT email FROM orders WHERE id=?");
            $os->bind_param("i",$orderId); $os->execute();
            $orderEmail = $os->get_result()->fetch_assoc()['email'];
            $el = $conn->prepare("INSERT INTO email_logs (order_id,type,sent_to) VALUES (?,'order_shipped',?)");
            $el->bind_param("is",$orderId,$orderEmail); $el->execute();
        }
    }
    $_SESSION['success'] = "Order status updated!";
    header("Location: orders.php"); exit();
}

$statusFilter = $_GET['status'] ?? '';
$where = $statusFilter ? "WHERE o.status='" . $conn->real_escape_string($statusFilter) . "'" : '';
$orders = $conn->query("SELECT o.*, COUNT(oi.id) as item_count FROM orders o LEFT JOIN order_items oi ON oi.order_id=o.id $where GROUP BY o.id ORDER BY o.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Orders | ShilpaNepal Admin</title>
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

    <!-- Status Tabs -->
    <div class="flex flex-wrap gap-2 mb-4">
        <?php foreach ([''=>'All','pending'=>'Pending','processing'=>'Processing','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled'] as $val=>$label): ?>
        <a href="orders.php<?= $val?'?status='.$val:'' ?>" class="py-1.5 px-4 rounded-lg font-semibold text-xs transition duration-300 <?= $statusFilter===$val?'bg-primary text-white shadow-sm':'border border-border bg-white text-dark hover:bg-cream' ?>"><?= $label ?></a>
        <?php endforeach; ?>
    </div>

    <!-- Orders Table -->
    <div class="bg-white border border-border rounded-2xl p-6 shadow-sm">
        <h6 class="font-bold text-dark text-sm tracking-wide uppercase mb-4">Orders (<?= $orders->num_rows ?>)</h6>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse align-middle">
                <thead>
                    <tr class="border-b border-border text-xs font-bold text-muted uppercase tracking-wider">
                        <th class="pb-3">#</th>
                        <th class="pb-3">Customer</th>
                        <th class="pb-3">Items</th>
                        <th class="pb-3">Total</th>
                        <th class="pb-3">Payment</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Date</th>
                        <th class="pb-3">Update</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/60 text-xs font-medium text-dark/95">
                <?php if ($orders->num_rows===0): ?>
                    <tr><td colspan="8" class="text-center text-muted py-8">No orders found.</td></tr>
                <?php else: while ($o=$orders->fetch_assoc()): ?>
                    <tr class="hover:bg-cream/10 transition">
                        <td class="py-4 font-bold">#<?= $o['id'] ?></td>
                        <td class="py-4 pr-2">
                            <div class="font-semibold text-dark"><?= htmlspecialchars($o['full_name']) ?></div>
                            <div class="text-[10px] text-muted"><?= htmlspecialchars($o['phone']) ?></div>
                        </td>
                        <td class="py-4"><span class="bg-gray-100 border border-gray-200 text-gray-800 text-[10px] font-bold px-2 py-0.5 rounded"><?= $o['item_count'] ?> item<?= $o['item_count']!=1?'s':'' ?></span></td>
                        <td class="py-4 font-semibold text-dark">NPR <?= number_format($o['total_amount'],0) ?></td>
                        <td class="py-4">
                            <?= $o['payment_status']==='paid'?'<span class="bg-green-100 border border-green-200 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded">Paid</span>':'<span class="bg-yellow-100 border border-yellow-200 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded">Unpaid</span>' ?>
                        </td>
                        <td class="py-4">
                            <?php
                            $statusColors = ['pending'=>'bg-yellow-100 text-yellow-800 border-yellow-200', 'processing'=>'bg-blue-100 text-blue-800 border-blue-200', 'shipped'=>'bg-green-100 text-green-850 border-green-200', 'delivered'=>'bg-green-100 text-green-800 border-green-200', 'cancelled'=>'bg-red-100 text-red-800 border-red-200'];
                            $col = $statusColors[$o['status']] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            ?>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border <?= $col ?>"><?= ucfirst($o['status']) ?></span>
                        </td>
                        <td class="py-4 text-muted"><?= date('M d, Y',strtotime($o['created_at'])) ?></td>
                        <td class="py-4">
                            <form method="POST" class="flex gap-2 items-center">
                                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                <select name="status" class="px-2 py-1 text-xs border border-border rounded-lg bg-white focus:outline-none focus:border-primary w-28">
                                    <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $o['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="update_status" class="p-1 px-1.5 bg-primary hover:bg-primary-dark text-white rounded transition text-xs font-bold"><i class="bi bi-check2"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</body>
</html>
