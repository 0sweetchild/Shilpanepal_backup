<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header("Location: products.php"); exit(); }
require_once 'includes/db.php';
require_once 'includes/auth_check.php';

$stmt = $conn->prepare("SELECT p.*,c.name as cat_name,c.slug as cat_slug,COALESCE(AVG(r.rating),0) as avg_rating,COUNT(DISTINCT r.id) as review_count FROM products p JOIN categories c ON p.category_id=c.id LEFT JOIN reviews r ON r.product_id=p.id WHERE p.slug=? GROUP BY p.id");
$stmt->bind_param("s",$slug); $stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) { header("Location: products.php"); exit(); }
$pageTitle = $product['name'];

$revStmt = $conn->prepare("SELECT r.*,u.full_name FROM reviews r JOIN users u ON r.user_id=u.id WHERE r.product_id=? ORDER BY r.created_at DESC");
$revStmt->bind_param("i",$product['id']); $revStmt->execute();
$reviewList = $revStmt->get_result();

$userReviewed = false;
if (isLoggedIn()) {
    $chk = $conn->prepare("SELECT id FROM reviews WHERE user_id=? AND product_id=?");
    $chk->bind_param("ii",$_SESSION['user_id'],$product['id']); $chk->execute();
    $userReviewed = $chk->get_result()->num_rows > 0;
}

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['submit_review'])) {
    requireLogin();
    $rating  = intval($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');
    if ($rating>=1 && $rating<=5 && !$userReviewed) {
        $ins = $conn->prepare("INSERT INTO reviews (user_id,product_id,rating,comment) VALUES (?,?,?,?)");
        $ins->bind_param("iiis",$_SESSION['user_id'],$product['id'],$rating,$comment);
        if ($ins->execute()) { $_SESSION['success']="Review submitted!"; header("Location: product.php?slug=$slug"); exit(); }
    }
}

$related = $conn->prepare("SELECT p.*,c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id WHERE p.category_id=? AND p.id!=? AND p.stock>0 LIMIT 4");
$related->bind_param("ii",$product['category_id'],$product['id']); $related->execute();
$relatedProducts = $related->get_result();

$ratingBreakdown = [];
for ($i=5;$i>=1;$i--) {
    $rb = $conn->prepare("SELECT COUNT(*) as cnt FROM reviews WHERE product_id=? AND rating=?");
    $rb->bind_param("ii",$product['id'],$i); $rb->execute();
    $ratingBreakdown[$i] = (int)$rb->get_result()->fetch_assoc()['cnt'];
}
include 'includes/header.php';
?>
<main class="flex-grow">
<div class="container py-12">
    <!-- Breadcrumb -->
    <nav class="flex text-sm text-muted font-medium gap-2 mb-8">
        <a href="<?= SITE_URL ?>" class="hover:text-primary transition">Home</a>
        <span>/</span>
        <a href="<?= SITE_URL ?>/products.php" class="hover:text-primary transition">Shop</a>
        <span>/</span>
        <a href="<?= SITE_URL ?>/products.php?category=<?= $product['cat_slug'] ?>" class="hover:text-primary transition"><?= htmlspecialchars($product['cat_name']) ?></a>
        <span>/</span>
        <span class="text-dark font-semibold truncate max-w-[200px]"><?= htmlspecialchars($product['name']) ?></span>
    </nav>

    <!-- Main Detail -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 mb-16">
        <!-- Image Column -->
        <div class="lg:col-span-5">
            <div class="rounded-2xl border border-border overflow-hidden bg-cream h-[420px] flex items-center justify-center p-6 shadow-sm">
                <?php if ($product['image']): ?>
                    <img src="<?= SITE_URL ?>/assets/images/uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <i class="bi bi-image text-muted text-[5rem]"></i>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Info Column -->
        <div class="lg:col-span-7 flex flex-col">
            <span class="text-[11px] font-bold tracking-[3px] text-primary uppercase"><?= htmlspecialchars($product['cat_name']) ?></span>
            <h1 class="font-playfair text-[2rem] font-bold text-dark mt-1.5 mb-2 leading-tight"><?= htmlspecialchars($product['name']) ?></h1>
            <div class="flex items-center gap-2 mb-4">
                <div class="text-accent text-lg"><?php $r=round($product['avg_rating']); for($i=1;$i<=5;$i++) echo $i<=$r?'★':'☆'; ?></div>
                <span class="text-xs text-muted font-medium"><?= number_format($product['avg_rating'],1) ?> (<?= $product['review_count'] ?> reviews)</span>
            </div>
            <div class="font-playfair text-[2rem] font-bold text-primary mb-4">NPR <?= number_format($product['price'],2) ?></div>
            <div class="mb-5">
                <?php if ($product['stock']<=0): ?>
                    <span class="bg-gray-100 border border-gray-200 text-gray-800 text-xs font-semibold px-3 py-1 rounded">Out of Stock</span>
                <?php elseif ($product['stock']<=LOW_STOCK_LIMIT): ?>
                    <span class="bg-yellow-100 border border-yellow-200 text-yellow-800 text-xs font-semibold px-3 py-1 rounded inline-flex items-center gap-1.5"><i class="bi bi-exclamation-triangle"></i>Only <?= $product['stock'] ?> left!</span>
                <?php else: ?>
                    <span class="bg-green-100 border border-green-200 text-green-800 text-xs font-semibold px-3 py-1 rounded inline-flex items-center gap-1.5"><i class="bi bi-check-circle"></i>In Stock (<?= $product['stock'] ?> available)</span>
                <?php endif; ?>
            </div>
            <p class="text-sm text-dark/80 mb-6 leading-relaxed whitespace-pre-line"><?= htmlspecialchars($product['description']) ?></p>
            
            <?php if ($product['stock']>0): ?>
            <div class="flex flex-col sm:flex-row gap-4 items-stretch sm:items-center mb-5">
                <div class="flex border border-border rounded-lg bg-white overflow-hidden w-full sm:w-[130px] h-[46px]">
                    <button class="px-3 hover:bg-cream text-dark transition" type="button" id="qtyMinus">−</button>
                    <input type="number" id="qtyInput" class="w-full text-center border-none bg-transparent text-dark font-semibold focus:outline-none" value="1" min="1" max="<?= $product['stock'] ?>">
                    <button class="px-3 hover:bg-cream text-dark transition" type="button" id="qtyPlus">+</button>
                </div>
                <button class="flex-grow py-3 px-6 bg-primary hover:bg-primary-dark text-white rounded-lg transition duration-300 font-semibold text-sm add-to-cart flex items-center justify-center gap-2" data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>">
                    <i class="bi bi-cart-plus text-lg"></i>Add to Cart
                </button>
            </div>
            <a href="<?= SITE_URL ?>/cart.php" class="inline-flex justify-center items-center gap-2 py-3 border-2 border-primary text-primary hover:bg-primary hover:text-white rounded-lg transition duration-300 font-semibold text-sm w-full mb-4"><i class="bi bi-bag-check"></i>Go to Cart & Checkout</a>
            <?php else: ?>
            <button class="py-3 px-6 bg-gray-300 text-gray-500 rounded-lg font-semibold text-sm w-full mb-4 cursor-not-allowed" disabled>Out of Stock</button>
            <?php endif; ?>
            
            <div class="border-t border-border pt-4 mt-auto">
                <small class="text-[11px] text-muted flex flex-wrap gap-x-4 gap-y-1"><span class="flex items-center gap-1"><i class="bi bi-shield-check text-green-600"></i>100% Authentic</span><span class="flex items-center gap-1"><i class="bi bi-truck text-primary"></i>Free shipping above NPR 2000</span><span class="flex items-center gap-1"><i class="bi bi-arrow-counterclockwise text-yellow-600"></i>7-day returns</span></small>
            </div>
        </div>
    </div>

    <!-- REVIEWS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 mb-16">
        <div class="lg:col-span-12"><h3 class="font-playfair text-[2rem] font-bold text-dark mb-4 border-b border-border pb-3">Customer Reviews</h3></div>
        
        <div class="lg:col-span-4 space-y-6">
            <!-- Summary Card -->
            <div class="bg-cream border border-border rounded-2xl p-6 shadow-sm">
                <div class="text-center mb-4">
                    <div class="font-playfair text-[3.5rem] font-bold text-dark leading-none mb-1"><?= number_format($product['avg_rating'],1) ?></div>
                    <div class="text-accent text-xl"><?php for($i=1;$i<=5;$i++) echo $i<=round($product['avg_rating'])?'★':'☆'; ?></div>
                    <div class="text-xs text-muted font-semibold mt-1"><?= $product['review_count'] ?> reviews</div>
                </div>
                <div class="space-y-2">
                    <?php for($i=5;$i>=1;$i--): $cnt=$ratingBreakdown[$i]; $pct=$product['review_count']>0?($cnt/$product['review_count']*100):0; ?>
                    <div class="flex items-center gap-3 text-xs">
                        <span class="text-muted w-3 font-semibold"><?= $i ?></span>
                        <i class="bi bi-star-fill text-accent text-[11px]"></i>
                        <div class="flex-grow bg-white/60 h-2 rounded-full overflow-hidden border border-border/30">
                            <div class="bg-accent h-full" style="width:<?= $pct ?>%;"></div>
                        </div>
                        <span class="text-muted w-5 font-semibold text-right"><?= $cnt ?></span>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            
            <!-- Submit Review Box -->
            <div class="border border-border rounded-2xl p-6 shadow-sm bg-white">
                <h6 class="font-bold text-dark mb-4 text-sm tracking-wide uppercase">Write a Review</h6>
                <?php if (!isLoggedIn()): ?>
                    <p class="text-sm text-muted">Please <a href="<?= SITE_URL ?>/auth/login.php" class="text-primary font-semibold hover:underline">login</a> to write a review.</p>
                <?php elseif ($userReviewed): ?>
                    <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-3 text-xs font-semibold">You already reviewed this product.</div>
                <?php else: ?>
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Your Rating</label>
                        <div id="starSelect" class="flex gap-1.5 text-2xl text-gray-300">
                            <?php for($i=1;$i<=5;$i++): ?>
                            <i class="bi bi-star cursor-pointer transition hover:scale-110" data-val="<?= $i ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="0">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Your Review</label>
                        <textarea name="comment" class="w-full px-4 py-3 text-sm border border-border rounded-lg bg-white focus:outline-none focus:border-primary" rows="3" placeholder="Share your experience..."></textarea>
                    </div>
                    <button type="submit" name="submit_review" class="w-full py-2.5 bg-primary hover:bg-primary-dark text-white rounded-lg font-semibold text-sm transition duration-300">Submit Review</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Reviews List -->
        <div class="lg:col-span-8 space-y-6">
            <?php if ($reviewList->num_rows===0): ?>
            <div class="text-center py-12 text-muted border border-border border-dashed rounded-2xl bg-cream/10">
                <i class="bi bi-chat-square-text text-[3rem] text-muted/65 mb-3 d-block"></i>
                <span class="text-sm">No reviews yet. Be the first to review!</span>
            </div>
            <?php else: while ($rev=$reviewList->fetch_assoc()): ?>
            <div class="border-b border-border pb-6 last:border-b-0">
                <div class="flex justify-between items-start">
                    <div>
                        <strong class="text-dark font-bold text-sm block"><?= htmlspecialchars(explode(' ',$rev['full_name'])[0]) ?></strong>
                        <div class="text-accent text-xs mt-0.5"><?php for($i=1;$i<=5;$i++) echo $i<=$rev['rating']?'★':'☆'; ?></div>
                    </div>
                    <small class="text-xs text-muted font-medium"><?= date('M d, Y',strtotime($rev['created_at'])) ?></small>
                </div>
                <?php if ($rev['comment']): ?>
                    <p class="mt-3 text-sm text-dark/75 leading-relaxed bg-cream/20 border border-border/20 p-4 rounded-xl"><?= htmlspecialchars($rev['comment']) ?></p>
                <?php endif; ?>
            </div>
            <?php endwhile; endif; ?>
        </div>
    </div>

    <!-- RELATED PRODUCTS -->
    <?php if ($relatedProducts->num_rows>0): ?>
    <div class="border-t border-border pt-12">
        <h3 class="font-playfair text-[1.8rem] font-bold text-dark mb-6">You May Also Like</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <?php while ($rp=$relatedProducts->fetch_assoc()): ?>
            <div class="bg-white border border-border rounded-2xl overflow-hidden shadow-sm hover:border-primary transition-all duration-300 flex flex-col h-full group">
                <a href="<?= SITE_URL ?>/product.php?slug=<?= urlencode($rp['slug']) ?>" class="block flex-grow flex flex-col">
                    <div class="h-[180px] overflow-hidden bg-cream relative">
                        <?php if ($rp['image']): ?>
                            <img src="<?= SITE_URL ?>/assets/images/uploads/<?= htmlspecialchars($rp['image']) ?>" alt="" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="flex items-center justify-center h-full text-muted"><i class="bi bi-image text-[2rem]"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4 flex-grow flex flex-col">
                        <span class="text-[9.5px] font-bold uppercase tracking-widest text-accent-dark"><?= htmlspecialchars($rp['cat_name']) ?></span>
                        <h4 class="font-playfair text-sm font-bold text-dark mt-1 mb-1 leading-snug group-hover:text-primary transition-colors duration-200"><?= htmlspecialchars($rp['name']) ?></h4>
                        <div class="text-primary font-bold text-sm mt-auto pt-1">NPR <?= number_format($rp['price'],2) ?></div>
                    </div>
                </a>
                <div class="px-4 pb-4">
                    <button class="w-full py-2 bg-primary hover:bg-primary-dark text-white rounded-lg transition duration-300 font-semibold text-xs add-to-cart flex items-center justify-center gap-1" data-id="<?= $rp['id'] ?>" data-name="<?= htmlspecialchars($rp['name']) ?>"><i class="bi bi-cart-plus"></i>Add to Cart</button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
</main>

<script>
document.getElementById('qtyMinus')&&document.getElementById('qtyMinus').addEventListener('click',function(){var i=document.getElementById('qtyInput');if(parseInt(i.value)>1)i.value=parseInt(i.value)-1;});
document.getElementById('qtyPlus')&&document.getElementById('qtyPlus').addEventListener('click',function(){var i=document.getElementById('qtyInput');if(parseInt(i.value)<parseInt(i.max))i.value=parseInt(i.value)+1;});
var stars=document.querySelectorAll('#starSelect i');
stars.forEach(function(star,idx){
    star.addEventListener('mouseover',function(){stars.forEach(function(s,i){s.style.color=i<=idx?'#C5A880':'#ccc';});});
    star.addEventListener('mouseout',function(){highlightStars(parseInt(document.getElementById('ratingInput').value));});
    star.addEventListener('click',function(){document.getElementById('ratingInput').value=idx+1;highlightStars(idx+1);});
});
function highlightStars(val){stars.forEach(function(s,i){s.style.color=i<val?'#C5A880':'#ccc';});}
</script>
<?php include 'includes/footer.php'; ?>
