<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Home';
require_once 'includes/db.php';
require_once 'includes/auth_check.php';
$featured   = $conn->query("SELECT p.*,c.name as cat_name,COALESCE(AVG(r.rating),0) as avg_rating,COUNT(DISTINCT r.id) as review_count FROM products p JOIN categories c ON p.category_id=c.id LEFT JOIN reviews r ON r.product_id=p.id WHERE p.featured=1 AND p.stock>0 GROUP BY p.id LIMIT 8");
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
$catIcons   = ['thangka-paintings'=>'bi-image','pashmina-shawls'=>'bi-wind','singing-bowls'=>'bi-music-note-beamed','dhaka-fabric'=>'bi-grid-3x3-gap','lokta-paper'=>'bi-journal','wooden-carvings'=>'bi-tree','handmade-jewelry'=>'bi-gem','felt-products'=>'bi-heart'];
include 'includes/header.php';
?>
<main class="flex-grow">
<!-- Hero Section -->
<section class="bg-cream border-b border-border py-16 md:py-24 overflow-hidden relative">
    <div class="container">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <!-- Hero Content -->
            <div class="lg:col-span-5">
                <div class="bg-primary/5 border border-primary/20 text-primary text-[11px] font-bold tracking-[2px] uppercase px-4 py-1.5 rounded-full inline-block mb-5">
                    🇳🇵 Authentically Nepali
                </div>
                <h1 class="font-playfair text-[2.5rem] md:text-[3.5rem] font-extrabold leading-tight text-dark mb-4">
                    Handcrafted <span class="text-primary italic font-normal font-playfair">Treasures</span><br>from the Himalayas
                </h1>
                <p class="text-[1.1rem] text-dark/85 max-w-[520px] mb-8 leading-relaxed">
                    Every piece tells a story of generations of Nepali artisans keeping heritage alive.
                </p>
                <div class="flex flex-wrap gap-4 mb-8">
                    <a href="<?= SITE_URL ?>/products.php" class="inline-flex items-center gap-2 py-3.5 px-6 bg-accent hover:bg-accent-dark text-dark font-semibold rounded-lg transition duration-300 shadow"><i class="bi bi-bag"></i>Shop Now</a>
                    <a href="<?= SITE_URL ?>/pages/history.php" class="inline-flex items-center gap-2 py-3.5 px-6 border-2 border-dark text-dark hover:bg-dark hover:text-white font-semibold rounded-lg transition duration-300">Our Story</a>
                </div>
                <div class="flex gap-8 border-t border-border/60 pt-6">
                    <div>
                        <div class="font-bold text-xl text-primary">500+</div>
                        <small class="text-xs text-muted font-medium">Products</small>
                    </div>
                    <div>
                        <div class="font-bold text-xl text-primary">100%</div>
                        <small class="text-xs text-muted font-medium">Handmade</small>
                    </div>
                    <div>
                        <div class="font-bold text-xl text-primary">Fast</div>
                        <small class="text-xs text-muted font-medium">Delivery</small>
                    </div>
                </div>
            </div>
            <!-- Hero Image Bento Grid -->
            <div class="lg:col-span-7 hidden lg:block relative">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[110%] h-[110%] bg-accent/20 blur-[30px] rounded-full pointer-events-none z-0"></div>
                <div class="grid grid-cols-12 gap-4 max-w-[720px] mx-auto relative z-10">
                    
                    <!-- Top Left Image: Wide, col-span-7 -->
                    <div class="col-span-7 bg-white rounded-2xl p-1.5 border border-border/40 shadow-sm hover:shadow-md transition duration-300 group h-[245px]">
                        <div class="w-full h-full overflow-hidden rounded-xl border border-border/30 relative bg-cream/40">
                            <img src="<?= SITE_URL ?>/assets/images/bento-thangka.png" alt="Thangka Painting" class="w-full h-full object-cover group-hover:scale-[1.04] transition duration-500">
                        </div>
                    </div>
                    
                    <!-- Top Right Image: Square, col-span-5 -->
                    <div class="col-span-5 bg-white rounded-2xl p-1.5 border border-border/40 shadow-sm hover:shadow-md transition duration-300 group h-[245px]">
                        <div class="w-full h-full overflow-hidden rounded-xl border border-border/30 relative bg-cream/40">
                            <img src="<?= SITE_URL ?>/assets/images/bento-bowl.png" alt="Singing Bowl" class="w-full h-full object-cover group-hover:scale-[1.04] transition duration-500">
                        </div>
                    </div>
                    
                    <!-- Bottom Left Image: Square, col-span-5 -->
                    <div class="col-span-5 bg-white rounded-2xl p-1.5 border border-border/40 shadow-sm hover:shadow-md transition duration-300 group h-[245px]">
                        <div class="w-full h-full overflow-hidden rounded-xl border border-border/30 relative bg-cream/40">
                            <img src="<?= SITE_URL ?>/assets/images/bento-carving.png" alt="Wood Carving" class="w-full h-full object-cover group-hover:scale-[1.04] transition duration-500">
                        </div>
                    </div>
                    
                    <!-- Bottom Right Image: Wide, col-span-7 -->
                    <div class="col-span-7 bg-white rounded-2xl p-1.5 border border-border/40 shadow-sm hover:shadow-md transition duration-300 group h-[245px]">
                        <div class="w-full h-full overflow-hidden rounded-xl border border-border/30 relative bg-cream/40">
                            <img src="<?= SITE_URL ?>/assets/images/bento-shawl.png" alt="Pashmina Shawl" class="w-full h-full object-cover group-hover:scale-[1.04] transition duration-500">
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-16 bg-cream border-b border-border">
    <div class="container">
        <div class="text-center mb-10">
            <span class="text-[11px] font-bold tracking-[3px] text-primary uppercase">Browse by</span>
            <h2 class="font-playfair text-[2.1rem] font-bold text-dark mt-1">Product Categories</h2>
            <div class="w-[50px] h-[3px] bg-accent rounded-full mx-auto mt-3"></div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php while ($cat=$categories->fetch_assoc()): $icon=$catIcons[$cat['slug']]??'bi-box'; ?>
            <a href="<?= SITE_URL ?>/products.php?category=<?= urlencode($cat['slug']) ?>" class="group block bg-white border border-border rounded-2xl p-6 text-center hover:border-primary hover:shadow-md transition duration-300 h-[160px] flex flex-col justify-center items-center">
                <i class="bi <?= $icon ?> text-[2.2rem] text-primary mb-2 group-hover:-translate-y-1 transition duration-300"></i>
                <div class="font-playfair text-[1.15rem] font-bold text-dark leading-tight"><?= htmlspecialchars($cat['name']) ?></div>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-16 bg-white">
    <div class="container">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-10">
            <div>
                <span class="text-[11px] font-bold tracking-[3px] text-primary uppercase">Handpicked for you</span>
                <h2 class="font-playfair text-[2.1rem] font-bold text-dark mt-1">Featured Products</h2>
                <div class="w-[50px] h-[3px] bg-accent rounded-full mt-3"></div>
            </div>
            <a href="<?= SITE_URL ?>/products.php" class="inline-flex items-center gap-1.5 py-2 px-5 border-2 border-primary text-primary hover:bg-primary hover:text-white rounded-lg transition duration-300 font-semibold text-sm">View All <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php while ($p=$featured->fetch_assoc()): ?>
            <div class="bg-white border border-border rounded-2xl overflow-hidden shadow-sm hover:border-primary transition-all duration-300 flex flex-col h-full group">
                <a href="<?= SITE_URL ?>/product.php?slug=<?= urlencode($p['slug']) ?>" class="block flex-grow flex flex-col">
                    <div class="h-[240px] overflow-hidden bg-cream relative">
                        <?php if ($p['image']): ?>
                            <img src="<?= SITE_URL ?>/assets/images/uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="flex items-center justify-center h-full text-muted"><i class="bi bi-image text-[3rem]"></i></div>
                        <?php endif; ?>
                        <span class="absolute top-3 left-3 bg-primary text-white text-[9px] font-bold uppercase tracking-wider px-3 py-1 rounded-full shadow-md">Featured</span>
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
    </div>
</section>

<!-- Values Section -->
<section class="py-16 bg-cream border-t border-border">
    <div class="container">
        <div class="text-center mb-10">
            <span class="text-[11px] font-bold tracking-[3px] text-primary uppercase">Why choose us</span>
            <h2 class="font-playfair text-[2.1rem] font-bold text-dark mt-1">The ShilpaNepal Difference</h2>
            <div class="w-[50px] h-[3px] bg-accent rounded-full mx-auto mt-3"></div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 text-center">
            <div class="bg-white border border-border/60 rounded-2xl p-6 shadow-sm hover:scale-[1.02] transition-transform duration-300">
                <i class="bi bi-patch-check-fill text-4xl text-primary mb-4 d-block"></i>
                <h6 class="font-bold text-[1rem] text-dark mb-2">100% Authentic</h6>
                <p class="text-muted text-xs leading-relaxed">Sourced directly from verified Nepali artisans</p>
            </div>
            <div class="bg-white border border-border/60 rounded-2xl p-6 shadow-sm hover:scale-[1.02] transition-transform duration-300">
                <i class="bi bi-truck text-4xl text-primary mb-4 d-block"></i>
                <h6 class="font-bold text-[1rem] text-dark mb-2">Free Delivery</h6>
                <p class="text-muted text-xs leading-relaxed">Free shipping on orders above NPR 2,000</p>
            </div>
            <div class="bg-white border border-border/60 rounded-2xl p-6 shadow-sm hover:scale-[1.02] transition-transform duration-300">
                <i class="bi bi-arrow-counterclockwise text-4xl text-primary mb-4 d-block"></i>
                <h6 class="font-bold text-[1rem] text-dark mb-2">Easy Returns</h6>
                <p class="text-muted text-xs leading-relaxed">7-day hassle-free return policy</p>
            </div>
            <div class="bg-white border border-border/60 rounded-2xl p-6 shadow-sm hover:scale-[1.02] transition-transform duration-300">
                <i class="bi bi-shield-check text-4xl text-primary mb-4 d-block"></i>
                <h6 class="font-bold text-[1rem] text-dark mb-2">Secure Payment</h6>
                <p class="text-muted text-xs leading-relaxed">Pay safely via eSewa — Nepal's trusted wallet</p>
            </div>
        </div>
    </div>
</section>
</main>
<?php include 'includes/footer.php'; ?>
