<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$pCount = $GLOBALS['conn']->query("SELECT COUNT(*) as c FROM orders WHERE status='pending'")->fetch_assoc()['c'];
?>
<div class="w-64 bg-dark text-white flex flex-col h-screen fixed top-0 left-0 z-50 shadow-lg border-r border-white/5">
    <div class="px-6 py-6 border-b border-white/10 text-center">
        <div class="font-playfair text-[20px] font-bold">
            <span style="color:#52B788;">Shilpa</span><span style="color:#D4A017;">Nepal</span>
        </div>
        <div class="text-[10px] text-white/40 font-bold uppercase tracking-wider mt-1">Admin Panel</div>
    </div>
    <nav class="flex-grow py-4 overflow-y-auto font-medium text-sm flex flex-col space-y-1">
        <div class="text-[10px] text-white/30 font-bold uppercase tracking-widest px-6 py-3">Main</div>
        <a href="<?= SITE_URL ?>/admin/dashboard.php" class="flex items-center gap-3 px-6 py-3 text-white/60 hover:text-white hover:bg-primary <?= $currentPage==='dashboard.php'?'bg-primary text-white':'' ?> transition duration-200">
            <i class="bi bi-speedometer2 text-base"></i> Dashboard
        </a>
        
        <div class="text-[10px] text-white/30 font-bold uppercase tracking-widest px-6 py-3">Catalog</div>
        <a href="<?= SITE_URL ?>/admin/products.php" class="flex items-center gap-3 px-6 py-3 text-white/60 hover:text-white hover:bg-primary <?= $currentPage==='products.php'?'bg-primary text-white':'' ?> transition duration-200">
            <i class="bi bi-box-seam text-base"></i> Products
        </a>
        <a href="<?= SITE_URL ?>/admin/categories.php" class="flex items-center gap-3 px-6 py-3 text-white/60 hover:text-white hover:bg-primary <?= $currentPage==='categories.php'?'bg-primary text-white':'' ?> transition duration-200">
            <i class="bi bi-grid text-base"></i> Categories
        </a>
        
        <div class="text-[10px] text-white/30 font-bold uppercase tracking-widest px-6 py-3">Sales</div>
        <a href="<?= SITE_URL ?>/admin/orders.php" class="flex items-center gap-3 px-6 py-3 text-white/60 hover:text-white hover:bg-primary <?= $currentPage==='orders.php'?'bg-primary text-white':'' ?> transition duration-200">
            <i class="bi bi-bag-check text-base"></i> Orders
            <?php if ($pCount > 0): ?>
            <span class="bg-red-600 text-white text-[9px] font-bold rounded-full w-5 h-5 flex items-center justify-center ml-auto"><?= $pCount ?></span>
            <?php endif; ?>
        </a>
        <a href="<?= SITE_URL ?>/admin/completed.php" class="flex items-center gap-3 px-6 py-3 text-white/60 hover:text-white hover:bg-primary <?= $currentPage==='completed.php'?'bg-primary text-white':'' ?> transition duration-200">
            <i class="bi bi-check-circle text-base"></i> Completed
        </a>
        <a href="<?= SITE_URL ?>/admin/payments.php" class="flex items-center gap-3 px-6 py-3 text-white/60 hover:text-white hover:bg-primary <?= $currentPage==='payments.php'?'bg-primary text-white':'' ?> transition duration-200">
            <i class="bi bi-credit-card text-base"></i> Payments
        </a>
        
        <div class="text-[10px] text-white/30 font-bold uppercase tracking-widest px-6 py-3">Users</div>
        <a href="<?= SITE_URL ?>/admin/users.php" class="flex items-center gap-3 px-6 py-3 text-white/60 hover:text-white hover:bg-primary <?= $currentPage==='users.php'?'bg-primary text-white':'' ?> transition duration-200">
            <i class="bi bi-people text-base"></i> Users
        </a>
        
        <div class="text-[10px] text-white/30 font-bold uppercase tracking-widest px-6 py-3">Site</div>
        <a href="<?= SITE_URL ?>/index.php" class="flex items-center gap-3 px-6 py-3 text-white/60 hover:text-white hover:bg-primary transition duration-200" target="_blank">
            <i class="bi bi-shop text-base"></i> View Store
        </a>
        <a href="<?= SITE_URL ?>/auth/logout.php" class="flex items-center gap-3 px-6 py-3 text-red-400 hover:text-white hover:bg-red-600 transition duration-200">
            <i class="bi bi-box-arrow-right text-base"></i> Logout
        </a>
    </nav>
</div>
