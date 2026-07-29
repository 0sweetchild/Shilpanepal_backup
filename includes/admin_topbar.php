<div class="bg-white border-b border-border py-4 px-8 flex justify-between items-center sticky top-0 z-40 shadow-sm">
    <h6 class="font-playfair text-lg font-bold text-dark mb-0"><?= $pageTitle ?? 'Admin' ?></h6>
    <div class="flex items-center gap-4">
        <span class="text-xs text-muted font-semibold flex items-center gap-1.5"><i class="bi bi-person-circle text-base"></i><?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?></span>
        <a href="<?= SITE_URL ?>/auth/logout.php" class="py-1 px-3 border border-red-500 text-red-500 hover:bg-red-500 hover:text-white rounded-lg transition text-xs font-semibold">Logout</a>
    </div>
</div>
