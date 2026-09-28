<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Dashboard';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
requireAdmin();

$totalOrders       = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$totalRevenue      = $conn->query("SELECT COALESCE(SUM(total_amount),0) as r FROM orders WHERE payment_status='paid'")->fetch_assoc()['r'];
$totalProducts     = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];
$totalUsers        = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='customer'")->fetch_assoc()['c'];
$pendingOrders     = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='pending'")->fetch_assoc()['c'];
$completedOrders   = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='delivered'")->fetch_assoc()['c'];
$recentOrders      = $conn->query("SELECT o.* FROM orders o ORDER BY o.created_at DESC LIMIT 7");
$availableProducts = $conn->query("SELECT p.*,c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id ORDER BY p.created_at DESC LIMIT 7");

/* Commented out as requested:
$lowStock      = $conn->query("SELECT p.*,c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id WHERE p.stock<=p.low_stock_threshold ORDER BY p.stock ASC LIMIT 8");
$monthlyRev    = $conn->query("SELECT DATE_FORMAT(created_at,'%b') as month,DATE_FORMAT(created_at,'%Y-%m') as month_key,COALESCE(SUM(total_amount),0) as revenue FROM orders WHERE payment_status='paid' AND created_at>=DATE_SUB(NOW(),INTERVAL 6 MONTH) GROUP BY month_key,month ORDER BY month_key ASC")->fetch_all(MYSQLI_ASSOC);
$catSales      = $conn->query("SELECT c.name,COALESCE(SUM(oi.quantity*oi.price),0) as revenue FROM categories c LEFT JOIN products p ON p.category_id=c.id LEFT JOIN order_items oi ON oi.product_id=p.id LEFT JOIN orders o ON o.id=oi.order_id AND o.payment_status='paid' GROUP BY c.id ORDER BY revenue DESC LIMIT 6")->fetch_all(MYSQLI_ASSOC);
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard | ShilpaNepal Admin</title>
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
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl p-3.5 text-xs font-semibold flex justify-between items-center" id="successAlert">
            <span><i class="bi bi-check-circle-fill text-green-600 me-2"></i><?= $_SESSION['success'] ?></span>
            <button type="button" class="text-green-800 hover:text-green-950" onclick="document.getElementById('successAlert').remove()"><i class="bi bi-x-lg"></i></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-3.5 text-xs font-semibold flex justify-between items-center" id="errorAlert">
            <span><i class="bi bi-exclamation-triangle-fill text-red-600 me-2"></i><?= $_SESSION['error'] ?></span>
            <button type="button" class="text-red-800 hover:text-red-950" onclick="document.getElementById('errorAlert').remove()"><i class="bi bi-x-lg"></i></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Top Row -->
    <div class="flex justify-between items-center mb-2">
        <div>
            <h4 class="font-playfair text-[2rem] font-bold text-dark leading-tight">Dashboard</h4>
            <small class="text-sm text-muted">Welcome back, <?= htmlspecialchars(explode(' ',$_SESSION['full_name'])[0]) ?>!</small>
        </div>
        <span class="text-xs text-muted font-semibold flex items-center gap-1.5 bg-white border border-border px-3.5 py-2 rounded-xl"><i class="bi bi-calendar3"></i><?= date('D, M d Y') ?></span>
    </div>
    
    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <!-- Revenue Card -->
        <div class="bg-white border border-border rounded-2xl p-5 shadow-sm flex items-start justify-between">
            <div>
                <div class="text-[11px] font-bold text-muted uppercase tracking-wider">Total Revenue</div>
                <div class="font-playfair text-[1.75rem] font-bold text-dark mt-2">NPR <?= number_format($totalRevenue/1000,1) ?>K</div>
                <small class="text-xs text-green-600 font-semibold">From paid orders</small>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg"><i class="bi bi-cash-stack"></i></div>
        </div>
        
        <!-- Total Orders Card -->
        <div class="bg-white border border-border rounded-2xl p-5 shadow-sm flex items-start justify-between">
            <div>
                <div class="text-[11px] font-bold text-muted uppercase tracking-wider">Total Orders</div>
                <div class="font-playfair text-[1.75rem] font-bold text-dark mt-2"><?= $totalOrders ?></div>
                <small class="text-xs text-amber-600 font-semibold"><?= $pendingOrders ?> pending</small>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg"><i class="bi bi-bag-check"></i></div>
        </div>

        <!-- Completed Orders Card -->
        <div class="bg-white border border-border rounded-2xl p-5 shadow-sm flex items-start justify-between">
            <div>
                <div class="text-[11px] font-bold text-muted uppercase tracking-wider">Completed</div>
                <div class="font-playfair text-[1.75rem] font-bold text-dark mt-2"><?= $completedOrders ?></div>
                <small class="text-xs text-teal-600 font-semibold">Delivered orders</small>
            </div>
            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg"><i class="bi bi-check-circle"></i></div>
        </div>
        
        <!-- Products Card -->
        <div class="bg-white border border-border rounded-2xl p-5 shadow-sm flex items-start justify-between">
            <div>
                <div class="text-[11px] font-bold text-muted uppercase tracking-wider">Products</div>
                <div class="font-playfair text-[1.75rem] font-bold text-dark mt-2"><?= $totalProducts ?></div>
                <small class="text-xs text-green-600 font-semibold">Active catalog</small>
                <?php /* Commented out Low Stock count: <small class="text-xs text-red-600 font-semibold"><?= $lowStock->num_rows ?> low stock</small> */ ?>
            </div>
            <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-lg"><i class="bi bi-box-seam"></i></div>
        </div>
        
        <!-- Users Card -->
        <div class="bg-white border border-border rounded-2xl p-5 shadow-sm flex items-start justify-between">
            <div>
                <div class="text-[11px] font-bold text-muted uppercase tracking-wider">Customers</div>
                <div class="font-playfair text-[1.75rem] font-bold text-dark mt-2"><?= $totalUsers ?></div>
                <small class="text-xs text-muted font-semibold">Registered users</small>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg"><i class="bi bi-people"></i></div>
        </div>
    </div>
    
    <?php /* Commented out as requested: Monthly Revenue & Sales by Category Charts
    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Monthly Revenue Chart -->
        <div class="lg:col-span-8 bg-white border border-border rounded-2xl p-6 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h6 class="font-bold text-dark text-sm tracking-wide uppercase">Monthly Revenue</h6>
                <span class="text-xs text-muted font-medium">Last 6 months</span>
            </div>
            <canvas id="revenueChart" height="100"></canvas>
        </div>
        <!-- Category Sales Doughnut -->
        <div class="lg:col-span-4 bg-white border border-border rounded-2xl p-6 shadow-sm">
            <h6 class="font-bold text-dark text-sm tracking-wide uppercase mb-4">Sales by Category</h6>
            <canvas id="categoryChart" height="200"></canvas>
        </div>
    </div>
    */ ?>
    
    <!-- Bottom Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Available Products Card -->
        <div class="lg:col-span-5 bg-white border border-border rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex justify-between items-center border-b border-border/60 pb-3">
                <h6 class="font-bold text-dark text-sm tracking-wide uppercase flex items-center gap-2">
                    <i class="bi bi-box-seam text-primary"></i>Available Products
                </h6>
                <a href="products.php" class="text-xs text-primary font-bold hover:underline">View all</a>
            </div>
            <div class="divide-y divide-border/60 max-h-[380px] overflow-y-auto pr-1">
                <?php if ($availableProducts->num_rows === 0): ?>
                    <p class="text-xs text-muted py-4 text-center">No products available.</p>
                <?php else: while ($p = $availableProducts->fetch_assoc()): ?>
                <div class="flex items-center justify-between py-2.5 hover:bg-cream/20 px-2 rounded-lg transition">
                    <div class="flex items-center gap-3">
                        <img src="<?= !empty($p['image']) ? SITE_URL.'/assets/images/uploads/'.htmlspecialchars($p['image']) : SITE_URL.'/assets/images/placeholder.jpg' ?>" 
                             alt="<?= htmlspecialchars($p['name']) ?>" 
                             class="w-10 h-10 object-cover rounded-lg border border-border">
                        <div>
                            <div class="text-xs font-semibold text-dark truncate max-w-[160px]"><?= htmlspecialchars($p['name']) ?></div>
                            <div class="text-[10px] text-muted"><?= htmlspecialchars($p['cat_name']) ?></div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-bold text-dark">NPR <?= number_format($p['price'], 0) ?></div>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded <?= $p['stock'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                            <?= $p['stock'] > 0 ? $p['stock'].' in stock' : 'Out of stock' ?>
                        </span>
                    </div>
                </div>
                <?php endwhile; endif; ?>
            </div>
        </div>

        <?php /* Commented out as requested: Low Stock Alerts
        <!-- Low Stock Alerts -->
        <div class="lg:col-span-4 bg-white border border-border rounded-2xl p-6 shadow-sm space-y-4">
            <h6 class="font-bold text-red-600 text-sm tracking-wide uppercase flex items-center gap-1.5"><i class="bi bi-exclamation-triangle"></i>Low Stock Alerts</h6>
            <div class="divide-y divide-border/60 max-h-[300px] overflow-y-auto pr-1">
                <?php $lowStock->data_seek(0); if ($lowStock->num_rows===0): ?>
                    <p class="text-xs text-muted py-2">All products well stocked!</p>
                <?php else: while ($ls=$lowStock->fetch_assoc()): ?>
                <div class="flex justify-between items-center py-2.5">
                    <div>
                        <div class="text-xs font-semibold text-dark truncate max-w-[150px]"><?= htmlspecialchars($ls['name']) ?></div>
                        <div class="text-[10px] text-muted"><?= htmlspecialchars($ls['cat_name']) ?></div>
                    </div>
                    <span class="bg-amber-100 border border-amber-200 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded"><?= $ls['stock'] ?> left</span>
                </div>
                <?php endwhile; endif; ?>
            </div>
        </div>
        */ ?>
        
        <!-- Recent Orders -->
        <div class="lg:col-span-7 bg-white border border-border rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex justify-between items-center">
                <h6 class="font-bold text-dark text-sm tracking-wide uppercase">Recent Orders</h6>
                <a href="orders.php" class="text-xs text-primary font-bold hover:underline">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse align-middle">
                    <thead>
                        <tr class="border-b border-border text-xs font-bold text-muted uppercase tracking-wider">
                            <th class="pb-3">Order #</th>
                            <th class="pb-3">Customer</th>
                            <th class="pb-3">Amount</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3">Payment</th>
                            <th class="pb-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60 text-xs font-medium">
                        <?php while ($o=$recentOrders->fetch_assoc()): ?>
                        <tr class="hover:bg-cream/10 transition">
                            <td class="py-3 font-bold">#<?= $o['id'] ?></td>
                            <td class="py-3 text-dark"><?= htmlspecialchars($o['full_name']) ?></td>
                            <td class="py-3 font-semibold">NPR <?= number_format($o['total_amount'],0) ?></td>
                            <td class="py-3">
                                <?php
                                $statusColors = ['pending'=>'bg-yellow-100 text-yellow-800 border-yellow-200', 'processing'=>'bg-blue-100 text-blue-800 border-blue-200', 'shipped'=>'bg-green-100 text-green-800 border-green-200', 'delivered'=>'bg-green-100 text-green-800 border-green-200', 'cancelled'=>'bg-red-100 text-red-800 border-red-200'];
                                $col = $statusColors[$o['status']] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border <?= $col ?>"><?= ucfirst($o['status']) ?></span>
                            </td>
                            <td class="py-3">
                                <?= $o['payment_status']==='paid'?'<span class="bg-green-100 border border-green-200 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded">Paid</span>':'<span class="bg-yellow-100 border border-yellow-200 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded">Unpaid</span>' ?>
                            </td>
                            <td class="py-3 text-muted"><?= date('M d',strtotime($o['created_at'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php /* Commented out Chart JS scripts:
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var months   = <?= json_encode(array_column($monthlyRev,'month')) ?>;
var revenues = <?= json_encode(array_map(function($r){ return (float)$r['revenue']; }, $monthlyRev)) ?>;
new Chart(document.getElementById('revenueChart'),{type:'bar',data:{labels:months,datasets:[{label:'Revenue (NPR)',data:revenues,backgroundColor:'rgba(142,28,36,0.15)',borderColor:'#8E1C24',borderWidth:2,borderRadius:6}]},options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true},x:{grid:{display:false}}}}});
var catLabels = <?= json_encode(array_column($catSales,'name')) ?>;
var catData   = <?= json_encode(array_map(function($c){ return (float)$c['revenue']; }, $catSales)) ?>;
new Chart(document.getElementById('categoryChart'),{type:'doughnut',data:{labels:catLabels,datasets:[{data:catData,backgroundColor:['#8E1C24','#C5A880','#52B788','#1B4332','#8E44AD','#2980B9'],borderWidth:2,borderColor:'#fff'}]},options:{plugins:{legend:{position:'bottom',labels:{font:{size:11},padding:8}}},cutout:'65%'}});
</script>
*/ ?>
</body>
</html>


