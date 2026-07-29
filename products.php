<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Shop';
require_once 'includes/db.php';
require_once 'includes/auth_check.php';

$search   = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$minPrice = intval($_GET['min_price'] ?? 0);
$maxPrice = intval($_GET['max_price'] ?? 99999);
$sort     = $_GET['sort'] ?? 'newest';

$where  = ["p.stock > 0"];
$params = [];
$types  = "";
if ($search !== '') { $where[] = "(p.name LIKE ? OR p.description LIKE ?)"; $like = "%$search%"; $params[] = $like; $params[] = $like; $types .= "ss"; }
if ($category !== '') { $where[] = "c.slug = ?"; $params[] = $category; $types .= "s"; }
if ($minPrice > 0) { $where[] = "p.price >= ?"; $params[] = $minPrice; $types .= "i"; }
if ($maxPrice < 99999) { $where[] = "p.price <= ?"; $params[] = $maxPrice; $types .= "i"; }

$orderBy = 'p.created_at DESC';
if ($sort === 'price_asc')  $orderBy = 'p.price ASC';
if ($sort === 'price_desc') $orderBy = 'p.price DESC';
if ($sort === 'rating')     $orderBy = 'avg_rating DESC';
if ($sort === 'name')       $orderBy = 'p.name ASC';

$whereSQL = implode(' AND ', $where);
$sql  = "SELECT p.*,c.name as cat_name,c.slug as cat_slug,COALESCE(AVG(r.rating),0) as avg_rating,COUNT(DISTINCT r.id) as review_count FROM products p JOIN categories c ON p.category_id=c.id LEFT JOIN reviews r ON r.product_id=p.id WHERE $whereSQL GROUP BY p.id ORDER BY $orderBy";
$stmt = $conn->prepare($sql);
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$products      = $stmt->get_result();
$totalProducts = $products->num_rows;
$cats          = $conn->query("SELECT * FROM categories ORDER BY name");
include 'includes/header.php';
?>
<main class="flex-grow">
<div class="container py-12">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- SIDEBAR FILTER -->
        <div class="lg:col-span-3">
            <div class="lg:sticky lg:top-24 bg-white border border-border rounded-2xl p-6 shadow-sm">
                <form method="GET" id="filterForm" class="space-y-6">
                    <div>
                        <h6 class="font-bold text-dark mb-3 text-sm tracking-wide uppercase">Search</h6>
                        <div class="flex">
                            <input type="text" name="search" class="w-full px-4 py-2 text-sm border border-border rounded-l-lg bg-white/80 focus:outline-none focus:border-primary transition" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
                            <button class="px-4 border border-primary bg-primary hover:bg-primary-dark text-white rounded-r-lg transition" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                    <div>
                        <h6 class="font-bold text-dark mb-3 text-sm tracking-wide uppercase">Category</h6>
                        <div class="flex flex-col gap-2.5 text-sm text-dark/95">
                            <label class="flex items-center gap-2 cursor-pointer font-medium hover:text-primary transition">
                                <input type="radio" name="category" class="w-4 h-4 text-primary focus:ring-primary border-border" value="" <?= $category===''?'checked':'' ?> onchange="this.form.submit()"> All Categories
                            </label>
                            <?php while ($cat=$cats->fetch_assoc()): ?>
                            <label class="flex items-center gap-2 cursor-pointer font-medium hover:text-primary transition">
                                <input type="radio" name="category" class="w-4 h-4 text-primary focus:ring-primary border-border" value="<?= $cat['slug'] ?>" <?= $category===$cat['slug']?'checked':'' ?> onchange="this.form.submit()">
                                <?= htmlspecialchars($cat['name']) ?>
                            </label>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <div>
                        <h6 class="font-bold text-dark mb-3 text-sm tracking-wide uppercase">Price Range</h6>
                        <div class="flex gap-2 mb-3">
                            <input type="number" name="min_price" class="w-full px-3 py-1.5 text-sm border border-border rounded-lg bg-white focus:outline-none focus:border-primary" placeholder="Min" value="<?= $minPrice ?: '' ?>">
                            <input type="number" name="max_price" class="w-full px-3 py-1.5 text-sm border border-border rounded-lg bg-white focus:outline-none focus:border-primary" placeholder="Max" value="<?= $maxPrice < 99999 ? $maxPrice : '' ?>">
                        </div>
                        <button type="submit" class="w-full py-2 border border-primary text-primary hover:bg-primary hover:text-white rounded-lg transition duration-300 font-semibold text-xs uppercase">Apply Price</button>
                    </div>
                    <div>
                        <h6 class="font-bold text-dark mb-3 text-sm tracking-wide uppercase">Sort By</h6>
                        <select name="sort" class="w-full px-3 py-2 text-sm border border-border rounded-lg bg-white focus:outline-none focus:border-primary" onchange="this.form.submit()">
                            <option value="newest"    <?= $sort==='newest'    ?'selected':'' ?>>Newest First</option>
                            <option value="price_asc" <?= $sort==='price_asc' ?'selected':'' ?>>Price: Low to High</option>
                            <option value="price_desc"<?= $sort==='price_desc'?'selected':'' ?>>Price: High to Low</option>
                            <option value="rating"    <?= $sort==='rating'    ?'selected':'' ?>>Top Rated</option>
                            <option value="name"      <?= $sort==='name'      ?'selected':'' ?>>Name A-Z</option>
                        </select>
                    </div>
                    <?php if ($search||$category||$minPrice||$maxPrice<99999): ?>
                    <a href="products.php" class="flex items-center justify-center gap-1.5 w-full py-2 border border-muted text-muted hover:bg-cream rounded-lg transition text-xs font-semibold uppercase"><i class="bi bi-x-circle"></i>Clear Filters</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <!-- PRODUCTS LIST -->
        <div class="lg:col-span-9">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h4 class="font-playfair text-[2rem] font-bold text-dark mb-1"><?= $category ? htmlspecialchars(ucwords(str_replace('-',' ',$category))) : 'All Products' ?></h4>
                    <small class="text-sm text-muted font-medium"><?= $totalProducts ?> product<?= $totalProducts!==1?'s':'' ?> found<?= $search?' for "<strong>'.htmlspecialchars($search).'</strong>"':'' ?></small>
                </div>
            </div>
            <?php if ($totalProducts===0): ?>
            <div class="text-center py-16 bg-cream/40 border border-border rounded-2xl">
                <i class="bi bi-search text-[4rem] text-muted mb-4 d-block"></i>
                <h5 class="font-playfair text-lg font-bold text-dark">No products found</h5>
                <a href="products.php" class="inline-block mt-4 py-2 px-6 bg-primary hover:bg-primary-dark text-white rounded-lg font-semibold text-sm transition duration-300">Browse All Products</a>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <?php while ($p=$products->fetch_assoc()): ?>
                <div class="bg-white border border-border rounded-2xl overflow-hidden shadow-sm hover:border-primary transition-all duration-300 flex flex-col h-full group">
                    <a href="<?= SITE_URL ?>/product.php?slug=<?= urlencode($p['slug']) ?>" class="block flex-grow flex flex-col">
                        <div class="h-[240px] overflow-hidden bg-cream relative">
                            <?php if ($p['image']): ?>
                                <img src="<?= SITE_URL ?>/assets/images/uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="flex items-center justify-center h-full text-muted bg-cream"><i class="bi bi-image text-[3rem]"></i></div>
                            <?php endif; ?>
                            <?php if ($p['featured']): ?><span class="absolute top-3 left-3 bg-primary text-white text-[9px] font-bold uppercase tracking-wider px-3 py-1 rounded-full shadow-md">Featured</span><?php endif; ?>
                            <?php if ($p['stock']<=LOW_STOCK_LIMIT): ?><span class="absolute bottom-3 right-3 bg-yellow-100 border border-yellow-200 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded">Only <?= $p['stock'] ?> left</span><?php endif; ?>
                        </div>
                        <div class="p-5 flex-grow flex flex-col">
                            <span class="text-[9.5px] font-bold uppercase tracking-widest text-accent-dark"><?= htmlspecialchars($p['cat_name']) ?></span>
                            <h4 class="font-playfair text-[1.05rem] font-bold text-dark mt-1.5 mb-1.5 leading-snug group-hover:text-primary transition-colors duration-200"><?= htmlspecialchars($p['name']) ?></h4>
                            <div class="text-accent text-[13px] tracking-wide mb-1">
                                <?php $r=round($p['avg_rating']); for($i=1;$i<=5;$i++) echo $i<=$r?'★':'☆'; ?>
                                <small class="text-muted ml-1">(<?= $p['review_count'] ?>)</small>
                            </div>
                            <div class="text-primary font-bold text-[1.15rem] mt-auto pt-2.5">NPR <?= number_format($p['price'],2) ?></div>
                        </div>
                    </a>
                    <div class="px-5 pb-5">
                        <button class="w-full py-2.5 bg-primary hover:bg-primary-dark text-white rounded-lg transition duration-300 font-semibold text-sm add-to-cart flex items-center justify-center gap-1.5" data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>"><i class="bi bi-cart-plus"></i>Add to Cart</button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</main>
<?php include 'includes/footer.php'; ?>
