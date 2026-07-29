<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Payments';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
requireAdmin();

$payments    = $conn->query("SELECT p.*,o.full_name,o.email FROM payments p JOIN orders o ON p.order_id=o.id ORDER BY p.created_at DESC");
$totalPaid   = $conn->query("SELECT COALESCE(SUM(amount),0) as t FROM payments WHERE status='completed'")->fetch_assoc()['t'];
$totalFailed = $conn->query("SELECT COUNT(*) as c FROM payments WHERE status='failed'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Payments | ShilpaNepal Admin</title>
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
    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
        <!-- Collected -->
        <div class="bg-white border border-border rounded-2xl p-6 shadow-sm">
            <div class="text-[11px] font-bold text-muted uppercase tracking-wider">Total Collected</div>
            <div class="font-playfair text-[2rem] font-bold text-green-600 mt-2.5">NPR <?= number_format($totalPaid,2) ?></div>
        </div>
        <!-- Transactions -->
        <div class="bg-white border border-border rounded-2xl p-6 shadow-sm">
            <div class="text-[11px] font-bold text-muted uppercase tracking-wider">Total Transactions</div>
            <div class="font-playfair text-[2rem] font-bold text-dark mt-2.5"><?= $payments->num_rows ?></div>
        </div>
        <!-- Failed -->
        <div class="bg-white border border-border rounded-2xl p-6 shadow-sm">
            <div class="text-[11px] font-bold text-muted uppercase tracking-wider">Failed Payments</div>
            <div class="font-playfair text-[2rem] font-bold text-red-600 mt-2.5"><?= $totalFailed ?></div>
        </div>
    </div>

    <!-- Payments List Table -->
    <div class="bg-white border border-border rounded-2xl p-6 shadow-sm">
        <h6 class="font-bold text-dark text-sm tracking-wide uppercase mb-4">All Payment Records</h6>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse align-middle">
                <thead>
                    <tr class="border-b border-border text-xs font-bold text-muted uppercase tracking-wider">
                        <th class="pb-3">Order #</th>
                        <th class="pb-3">Customer</th>
                        <th class="pb-3">Method</th>
                        <th class="pb-3">Amount</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Transaction ID</th>
                        <th class="pb-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/60 text-xs font-medium text-dark/95">
                <?php $payments->data_seek(0); while ($pay=$payments->fetch_assoc()): ?>
                <tr class="hover:bg-cream/10 transition">
                    <td class="py-3.5 font-bold">#<?= $pay['order_id'] ?></td>
                    <td class="py-3.5 pr-2">
                        <div class="font-bold text-dark mb-0.5"><?= htmlspecialchars($pay['full_name']) ?></div>
                        <div class="text-[10px] text-muted"><?= htmlspecialchars($pay['email']) ?></div>
                    </td>
                    <td class="py-3.5"><span class="bg-esewa text-white px-2.5 py-0.5 rounded font-bold text-[10px] shadow-sm">eSewa</span></td>
                    <td class="py-3.5 font-semibold text-dark">NPR <?= number_format($pay['amount'],2) ?></td>
                    <td class="py-3.5">
                        <?php if ($pay['status']==='completed'): ?>
                            <span class="bg-green-100 border border-green-200 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded">Completed</span>
                        <?php elseif ($pay['status']==='failed'): ?>
                            <span class="bg-red-100 border border-red-200 text-red-800 text-[10px] font-bold px-2 py-0.5 rounded">Failed</span>
                        <?php else: ?>
                            <span class="bg-yellow-100 border border-yellow-200 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3.5 font-mono text-[10px] text-muted/90"><?= $pay['transaction_id'] ? htmlspecialchars(substr($pay['transaction_id'],0,25)).'...' : '—' ?></td>
                    <td class="py-3.5 text-muted"><?= date('M d, Y H:i',strtotime($pay['created_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</body>
</html>
