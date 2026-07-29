<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Users';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['change_role'])) {
    $uid  = intval($_POST['user_id']);
    $role = $_POST['role']==='admin' ? 'admin' : 'customer';
    if ($uid !== (int)$_SESSION['user_id']) {
        $stmt = $conn->prepare("UPDATE users SET role=? WHERE id=?");
        $stmt->bind_param("si",$role,$uid); $stmt->execute();
        $_SESSION['success'] = "User role updated.";
    }
    header("Location: users.php"); exit();
}
$users = $conn->query("SELECT u.*,COUNT(o.id) as order_count,COALESCE(SUM(o.total_amount),0) as total_spent FROM users u LEFT JOIN orders o ON o.user_id=u.id AND o.payment_status='paid' GROUP BY u.id ORDER BY u.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Users | ShilpaNepal Admin</title>
    <link class="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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

    <!-- Users List Table -->
    <div class="bg-white border border-border rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 mb-6">
            <h6 class="font-bold text-dark text-sm tracking-wide uppercase">All Users (<?= $users->num_rows ?>)</h6>
            <input type="text" id="userSearch" class="w-full sm:w-[200px] px-3 py-1.5 text-xs border border-border rounded-lg focus:outline-none focus:border-primary" placeholder="Search...">
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse align-middle" id="userTable">
                <thead>
                    <tr class="border-b border-border text-xs font-bold text-muted uppercase tracking-wider">
                        <th class="pb-3">Name</th>
                        <th class="pb-3">Email</th>
                        <th class="pb-3">Phone</th>
                        <th class="pb-3">Role</th>
                        <th class="pb-3 text-center">Orders</th>
                        <th class="pb-3">Spent</th>
                        <th class="pb-3">Joined</th>
                        <th class="pb-3">Change Role</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/60 text-xs font-medium text-dark/95">
                <?php while ($u=$users->fetch_assoc()): ?>
                <tr class="hover:bg-cream/10 transition">
                    <td class="py-3.5 font-bold text-dark"><?= htmlspecialchars($u['full_name']) ?></td>
                    <td class="py-3.5 text-muted"><?= htmlspecialchars($u['email']) ?></td>
                    <td class="py-3.5 text-muted"><?= htmlspecialchars($u['phone']) ?></td>
                    <td class="py-3.5">
                        <?= $u['role']==='admin'?'<span class="bg-red-105 bg-red-100 border border-red-200 text-red-800 text-[10px] font-bold px-2.5 py-0.5 rounded-full">Admin</span>':'<span class="bg-gray-105 bg-gray-100 border border-gray-200 text-gray-800 text-[10px] font-bold px-2.5 py-0.5 rounded-full">Customer</span>' ?>
                    </td>
                    <td class="py-3.5 text-center font-bold text-dark/85"><?= $u['order_count'] ?></td>
                    <td class="py-3.5 font-semibold text-dark">NPR <?= number_format($u['total_spent'],0) ?></td>
                    <td class="py-3.5 text-muted"><?= date('M d, Y',strtotime($u['created_at'])) ?></td>
                    <td class="py-3.5">
                        <?php if ($u['id']!=$_SESSION['user_id']): ?>
                        <form method="POST" class="flex gap-2 items-center">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <select name="role" class="px-2 py-1 text-xs border border-border rounded-lg bg-white focus:outline-none focus:border-primary w-24">
                                <option value="customer" <?= $u['role']==='customer'?'selected':'' ?>>Customer</option>
                                <option value="admin"    <?= $u['role']==='admin'   ?'selected':'' ?>>Admin</option>
                            </select>
                            <button type="submit" name="change_role" class="p-1 px-1.5 bg-primary hover:bg-primary-dark text-white rounded transition text-xs font-bold"><i class="bi bi-check2"></i></button>
                        </form>
                        <?php else: ?><span class="text-muted/65 italic text-[11px] font-semibold pl-2">You</span><?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
<script>
document.getElementById('userSearch').addEventListener('input',function(){var q=this.value.toLowerCase();document.querySelectorAll('#userTable tbody tr').forEach(function(row){row.style.display=row.textContent.toLowerCase().includes(q)?'':'none';});});
</script>
</body>
</html>
