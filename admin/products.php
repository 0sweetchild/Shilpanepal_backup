<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Products';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
requireAdmin();

if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i",$deleteId); $stmt->execute();
    $_SESSION['success'] = "Product deleted.";
    header("Location: products.php"); exit();
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id          = intval($_POST['id'] ?? 0);
    $category_id = intval($_POST['category_id']);
    $name        = trim($_POST['name']);
    $description = trim($_POST['description'] ?? '');
    $price       = floatval($_POST['price']);
    $stock       = intval($_POST['stock']);
    $featured    = isset($_POST['featured']) ? 1 : 0;
    $threshold   = intval($_POST['low_stock_threshold'] ?? 5);
    $slug        = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/','-',$name),'-'));
    if ($id>0) $slug .= '-'.$id;
    $image = trim($_POST['existing_image'] ?? '');
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION));
        if (in_array($ext,['jpg','jpeg','png','webp'])) {
            $newName = uniqid().'.'.$ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'],'../assets/images/uploads/'.$newName)) $image = $newName;
        }
    }
    if ($id>0) {
        $stmt = $conn->prepare("UPDATE products SET category_id=?,name=?,slug=?,description=?,price=?,stock=?,featured=?,low_stock_threshold=?,image=? WHERE id=?");
        $stmt->bind_param("isssdiiisi",$category_id,$name,$slug,$description,$price,$stock,$featured,$threshold,$image,$id);
    } else {
        $stmt = $conn->prepare("INSERT INTO products (category_id,name,slug,description,price,stock,featured,low_stock_threshold,image) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("isssdiiis",$category_id,$name,$slug,$description,$price,$stock,$featured,$threshold,$image);
    }
    $stmt->execute();
    $_SESSION['success'] = $id>0 ? "Product updated!" : "Product added!";
    header("Location: products.php"); exit();
}
$editProduct = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
    $stmt->bind_param("i",$editId); $stmt->execute();
    $editProduct = $stmt->get_result()->fetch_assoc();
}
$products   = $conn->query("SELECT p.*,c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id ORDER BY p.created_at DESC");
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Products | ShilpaNepal Admin</title>
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
            <button type="button" class="text-green-850 hover:text-green-950" onclick="document.getElementById('successAlert').style.display='none'"><i class="bi bi-x-lg"></i></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- FORM COLUMN -->
        <div class="lg:col-span-4">
            <div class="bg-white border border-border rounded-2xl p-6 shadow-sm">
                <h5 class="font-playfair text-base font-bold text-dark mb-4 border-b border-border pb-2"><?= $editProduct ? 'Edit Product' : 'Add New Product' ?></h5>
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <?php if ($editProduct): ?>
                        <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
                        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editProduct['image'] ?? '') ?>">
                    <?php endif; ?>
                    
                    <div>
                        <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" class="w-full px-3 py-2 text-xs border border-border rounded-lg bg-white focus:outline-none focus:border-primary" required>
                            <option value="">Select...</option>
                            <?php $categories->data_seek(0); while ($cat=$categories->fetch_assoc()): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($editProduct['category_id']??'')==$cat['id']?'selected':'' ?>><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" class="w-full px-3 py-2 text-xs border border-border rounded-lg bg-white focus:outline-none focus:border-primary" required value="<?= htmlspecialchars($editProduct['name']??'') ?>">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Description</label>
                        <textarea name="description" class="w-full px-3 py-2 text-xs border border-border rounded-lg bg-white focus:outline-none focus:border-primary" rows="3"><?= htmlspecialchars($editProduct['description']??'') ?></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Price (NPR) <span class="text-red-500">*</span></label>
                            <input type="number" name="price" step="0.01" min="0" class="w-full px-3 py-2 text-xs border border-border rounded-lg bg-white focus:outline-none focus:border-primary" required value="<?= $editProduct['price']??'' ?>">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Stock <span class="text-red-500">*</span></label>
                            <input type="number" name="stock" min="0" class="w-full px-3 py-2 text-xs border border-border rounded-lg bg-white focus:outline-none focus:border-primary" required value="<?= $editProduct['stock']??'' ?>">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" min="1" class="w-full px-3 py-2 text-xs border border-border rounded-lg bg-white focus:outline-none focus:border-primary" value="<?= $editProduct['low_stock_threshold']??5 ?>">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Product Image</label>
                        <input type="file" name="image" class="w-full text-xs border border-border rounded-lg bg-white focus:outline-none focus:border-primary file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-cream file:text-dark hover:file:bg-border/60" accept="image/*">
                        <?php if (!empty($editProduct['image'])): ?>
                            <div class="mt-2 flex items-center gap-2">
                                <img src="<?= SITE_URL ?>/assets/images/uploads/<?= htmlspecialchars($editProduct['image']) ?>" class="h-12 w-12 object-cover rounded-lg border border-border">
                                <small class="text-xs text-muted">Current image</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-2 py-1">
                        <input type="checkbox" name="featured" id="featuredChk" class="w-4 h-4 text-primary focus:ring-primary border-border rounded" <?= ($editProduct['featured']??0)?'checked':'' ?>>
                        <label class="text-xs font-semibold text-dark select-none cursor-pointer" for="featuredChk">Feature on homepage</label>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-grow py-2 px-4 bg-primary hover:bg-primary-dark text-white rounded-lg font-semibold text-xs transition duration-300 flex items-center justify-center gap-1.5"><i class="bi bi-<?= $editProduct?'pencil':'plus' ?>"></i><?= $editProduct?'Update Product':'Add Product' ?></button>
                        <?php if ($editProduct): ?>
                            <a href="products.php" class="py-2 px-4 border border-border text-dark hover:bg-cream rounded-lg transition font-semibold text-xs">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- TABLE COLUMN -->
        <div class="lg:col-span-8">
            <div class="bg-white border border-border rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 mb-6">
                    <h6 class="font-bold text-dark text-sm tracking-wide uppercase">All Products (<?= $products->num_rows ?>)</h6>
                    <input type="text" id="productSearch" class="w-full sm:w-[200px] px-3 py-1.5 text-xs border border-border rounded-lg focus:outline-none focus:border-primary" placeholder="Search...">
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse align-middle" id="productTable">
                        <thead>
                            <tr class="border-b border-border text-xs font-bold text-muted uppercase tracking-wider">
                                <th class="pb-3">Product</th>
                                <th class="pb-3">Category</th>
                                <th class="pb-3">Price</th>
                                <th class="pb-3 text-center">Stock</th>
                                <th class="pb-3 text-center">Featured</th>
                                <th class="pb-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60 text-xs font-medium text-dark/90">
                        <?php while ($p=$products->fetch_assoc()): ?>
                        <tr class="hover:bg-cream/10 transition">
                            <td class="py-3.5 pr-2">
                                <div class="flex items-center gap-2">
                                    <?php if ($p['image']): ?>
                                        <img src="<?= SITE_URL ?>/assets/images/uploads/<?= htmlspecialchars($p['image']) ?>" class="w-[38px] h-[38px] object-cover rounded-lg border border-border">
                                    <?php else: ?>
                                        <div class="w-[38px] h-[38px] bg-cream rounded-lg border border-border flex items-center justify-center text-muted"><i class="bi bi-image"></i></div>
                                    <?php endif; ?>
                                    <span class="font-semibold text-dark truncate max-w-[150px]"><?= htmlspecialchars($p['name']) ?></span>
                                </div>
                            </td>
                            <td class="py-3.5"><span class="bg-gray-100 border border-gray-200 text-gray-800 text-[10px] px-2 py-0.5 rounded font-bold"><?= htmlspecialchars($p['cat_name']) ?></span></td>
                            <td class="py-3.5 font-semibold">NPR <?= number_format($p['price'],0) ?></td>
                            <td class="py-3.5 text-center">
                                <?php if ($p['stock']<=$p['low_stock_threshold']): ?>
                                    <span class="bg-amber-100 border border-amber-200 text-amber-800 text-[10px] font-bold px-2.5 py-0.5 rounded-full"><?= $p['stock'] ?></span>
                                <?php else: ?>
                                    <span class="bg-green-105 bg-green-100 border border-green-200 text-green-800 text-[10px] font-bold px-2.5 py-0.5 rounded-full"><?= $p['stock'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 text-center">
                                <?= $p['featured']?'<i class="bi bi-star-fill text-warning text-sm"></i>':'<i class="bi bi-star text-muted/65 text-sm"></i>' ?>
                            </td>
                            <td class="py-3.5">
                                <div class="flex gap-1">
                                    <a href="products.php?edit=<?= $p['id'] ?>" class="p-1 text-primary hover:bg-cream border border-border/80 rounded transition"><i class="bi bi-pencil"></i></a>
                                    <a href="<?= SITE_URL ?>/product.php?slug=<?= urlencode($p['slug']) ?>" target="_blank" class="p-1 text-muted hover:bg-cream border border-border/80 rounded transition"><i class="bi bi-eye"></i></a>
                                    <a href="products.php?delete=<?= $p['id'] ?>" class="p-1 text-red-500 hover:bg-red-50 border border-red-200 rounded transition" onclick="return confirm('Delete this product?')"><i class="bi bi-trash"></i></a>
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
<script>
document.getElementById('productSearch').addEventListener('input',function(){var q=this.value.toLowerCase();document.querySelectorAll('#productTable tbody tr').forEach(function(row){row.style.display=row.textContent.toLowerCase().includes(q)?'':'none';});});
</script>
</body>
</html>
