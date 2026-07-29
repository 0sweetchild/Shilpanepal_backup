<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Categories';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
requireAdmin();

if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE id=?");
    $stmt->bind_param("i",$deleteId); $stmt->execute();
    $_SESSION['success'] = "Category deleted.";
    header("Location: categories.php"); exit();
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id   = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/','-',$name),'-'));
    if ($id>0) {
        $stmt = $conn->prepare("UPDATE categories SET name=?,slug=? WHERE id=?");
        $stmt->bind_param("ssi",$name,$slug,$id);
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (name,slug) VALUES (?,?)");
        $stmt->bind_param("ss",$name,$slug);
    }
    $stmt->execute();
    $_SESSION['success'] = $id>0 ? "Category updated!" : "Category added!";
    header("Location: categories.php"); exit();
}
$editCat = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id=?");
    $stmt->bind_param("i",$editId); $stmt->execute();
    $editCat = $stmt->get_result()->fetch_assoc();
}
$cats = $conn->query("SELECT c.*,COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON p.category_id=c.id GROUP BY c.id ORDER BY c.name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Categories | ShilpaNepal Admin</title>
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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- FORM COLUMN -->
        <div class="lg:col-span-4">
            <div class="bg-white border border-border rounded-2xl p-6 shadow-sm">
                <h5 class="font-playfair text-base font-bold text-dark mb-4 border-b border-border pb-2"><?= $editCat ? 'Edit Category' : 'Add Category' ?></h5>
                <form method="POST" class="space-y-4">
                    <?php if ($editCat): ?><input type="hidden" name="id" value="<?= $editCat['id'] ?>"><?php endif; ?>
                    <div>
                        <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Category Name</label>
                        <input type="text" name="name" class="w-full px-3 py-2 text-xs border border-border rounded-lg bg-white focus:outline-none focus:border-primary" required value="<?= htmlspecialchars($editCat['name']??'') ?>">
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-grow py-2 px-4 bg-primary hover:bg-primary-dark text-white rounded-lg font-semibold text-xs transition duration-300"><?= $editCat?'Update':'Add Category' ?></button>
                        <?php if ($editCat): ?>
                            <a href="categories.php" class="py-2 px-4 border border-border text-dark hover:bg-cream rounded-lg transition font-semibold text-xs">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- TABLE COLUMN -->
        <div class="lg:col-span-8">
            <div class="bg-white border border-border rounded-2xl p-6 shadow-sm">
                <h6 class="font-bold text-dark text-sm tracking-wide uppercase mb-4">All Categories</h6>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse align-middle">
                        <thead>
                            <tr class="border-b border-border text-xs font-bold text-muted uppercase tracking-wider">
                                <th class="pb-3">Name</th>
                                <th class="pb-3">Slug</th>
                                <th class="pb-3 text-center">Products Count</th>
                                <th class="pb-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60 text-xs font-medium text-dark/95">
                        <?php while ($cat=$cats->fetch_assoc()): ?>
                        <tr class="hover:bg-cream/10 transition">
                            <td class="py-3.5 font-semibold text-dark"><?= htmlspecialchars($cat['name']) ?></td>
                            <td class="py-3.5"><code class="text-xs text-primary font-mono bg-cream px-2 py-0.5 rounded"><?= $cat['slug'] ?></code></td>
                            <td class="py-3.5 text-center"><span class="bg-gray-150 bg-gray-100 border border-gray-200 text-gray-800 text-[10px] font-bold px-2 py-0.5 rounded"><?= $cat['product_count'] ?></span></td>
                            <td class="py-3.5">
                                <div class="flex gap-1">
                                    <a href="categories.php?edit=<?= $cat['id'] ?>" class="p-1 text-primary hover:bg-cream border border-border/80 rounded transition"><i class="bi bi-pencil"></i></a>
                                    <a href="categories.php?delete=<?= $cat['id'] ?>" class="p-1 text-red-500 hover:bg-red-50 border border-red-200 rounded transition" onclick="return confirm('Delete this category?')"><i class="bi bi-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</body>
</html>
