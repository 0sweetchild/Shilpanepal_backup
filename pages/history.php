<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Our History & Story';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
include '../includes/header.php';
?>
<main class="flex-grow">
    <!-- History Hero Banner -->
    <div class="py-16 text-center relative overflow-hidden bg-gradient-to-r from-primary-dark to-primary text-white flex items-center justify-center min-h-[300px]">
        <div class="absolute w-full h-full opacity-10 top-0 left-0" style="background-image: radial-gradient(var(--accent) 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div class="container relative z-10 max-w-[800px]">
            <span class="text-xs uppercase tracking-[2px] font-semibold text-accent mb-2.5 inline-block">Sacred Heritage & Craft</span>
            <h1 class="font-playfair text-[2.5rem] md:text-[3rem] font-bold mb-3 leading-tight">Our Story & Origins</h1>
            <p class="text-sm md:text-base font-light text-white/90 leading-relaxed max-w-[640px] mx-auto">Unearthing the deep-rooted historical origin and ancestral lineage of traditional Nepali masterpieces.</p>
        </div>
    </div>

    <div class="container py-12">
        <!-- Our Story Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center mb-16">
            <div class="lg:col-span-6 space-y-4">
                <div class="pe-lg-4">
                    <span class="text-[11px] font-bold tracking-[3px] text-primary uppercase">The ShilpaNepal Journey</span>
                    <h2 class="font-playfair text-[2rem] font-bold text-dark mt-1.5 mb-4">How We Began</h2>
                    <p class="text-sm text-muted leading-relaxed mb-4">
                        ShilpaNepal was born out of a profound love for the rich artistic heritage of the Kathmandu Valley and the wider Himalayas. For centuries, Nepali artisans have poured their souls into clay, wood, metal, and fabric, creating items that are not just beautiful, but are deeply spiritual and cultural.
                    </p>
                    <p class="text-sm text-muted leading-relaxed">
                        However, with modern globalization, these age-old, generational practices were at risk of dying out. Independent master craftsmen struggled to find sustainable markets, and younger generations began moving away from traditional trades. ShilpaNepal was founded to bridge this gap—providing a digital stage for these incredible artists to reach conscious collectors worldwide, ensuring fair compensation and keeping heritage alive.
                    </p>
                </div>
            </div>
            <div class="lg:col-span-6">
                <div class="p-6 md:p-8 border border-border rounded-2xl bg-cream shadow-sm flex flex-col justify-center h-full space-y-4">
                    <div style="color: var(--accent);">
                        <i class="bi bi-patch-check-fill text-[2.5rem]"></i>
                    </div>
                    <h4 class="font-playfair text-lg text-primary font-bold leading-relaxed">“Every chisel stroke on Sal wood, every weave of Dhaka, and every hammer mark on a singing bowl is a heartbeat of Nepal.”</h4>
                    <p class="text-muted text-xs font-semibold">— Traditional Artisan Association</p>
                </div>
            </div>
        </div>

        <hr class="border-border my-12">

        <!-- Product Origins Section -->
        <div class="text-center mb-12">
            <span class="text-[11px] font-bold tracking-[3px] text-primary uppercase">Ancestral Crafts</span>
            <h2 class="font-playfair text-[2.1rem] font-bold text-dark mt-1">Origin & History of Our Products</h2>
            <div class="w-[50px] h-[3px] bg-accent rounded-full mx-auto mt-3 mb-4"></div>
            <p class="text-sm text-muted max-w-[600px] mx-auto leading-relaxed">Nepali crafts carry a lineage that spans centuries. Explore where each of our product categories comes from and the history behind them.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            <!-- Thangka Paintings -->
            <div class="bg-white border border-border rounded-2xl overflow-hidden hover:shadow-lg hover:border-accent transition duration-300 flex flex-col group">
                <div class="h-[220px] overflow-hidden relative">
                    <span class="absolute top-4 left-4 bg-primary text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded">7th Century</span>
                    <img src="<?= SITE_URL ?>/assets/images/uploads/mandala-thangka.jpg" alt="Thangka painting origin" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-6 flex-grow">
                    <h5 class="font-playfair text-lg font-bold text-dark mb-3">Thangka Paintings</h5>
                    <p class="text-xs text-muted leading-relaxed">
                        Thangka paintings originated in the 7th century, evolving from Nepalese scroll paintings called <em>Paubhas</em>. Historically, they served as visual teaching aids for travelling Buddhist monks. Painted on cotton canvas with mineral-based pigments and 24-carat gold dust, each intricate mandala tells a story of Buddhist cosmology, enlightenment, and divine contemplation.
                    </p>
                </div>
            </div>

            <!-- Singing Bowls -->
            <div class="bg-white border border-border rounded-2xl overflow-hidden hover:shadow-lg hover:border-accent transition duration-300 flex flex-col group">
                <div class="h-[220px] overflow-hidden relative">
                    <span class="absolute top-4 left-4 bg-primary text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded">Ancient Times</span>
                    <img src="<?= SITE_URL ?>/assets/images/uploads/himalayan-singing-bowl-set.jpg" alt="Singing bowl history" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-6 flex-grow">
                    <h5 class="font-playfair text-lg font-bold text-dark mb-3">Himalayan Singing Bowls</h5>
                    <p class="text-xs text-muted leading-relaxed">
                        Dating back to the pre-Buddhist Bon shamanistic era, singing bowls have been hand-hammered in the Himalayas for over 2,000 years. Traditional bowls are created using a sacred alloy of seven metals—representing the seven celestial bodies: gold (Sun), silver (Moon), copper (Venus), iron (Mars), tin (Jupiter), mercury (Mercury), and lead (Saturn). They are praised for acoustic healing, meditation, and chakra alignment.
                    </p>
                </div>
            </div>

            <!-- Pashmina Shawls -->
            <div class="bg-white border border-border rounded-2xl overflow-hidden hover:shadow-lg hover:border-accent transition duration-300 flex flex-col group">
                <div class="h-[220px] overflow-hidden relative">
                    <span class="absolute top-4 left-4 bg-primary text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded">Royal Weave</span>
                    <img src="<?= SITE_URL ?>/assets/images/uploads/pure-pashmina-shawl.jpg" alt="Pashmina history" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-6 flex-grow">
                    <h5 class="font-playfair text-lg font-bold text-dark mb-3">Pure Pashmina</h5>
                    <p class="text-xs text-muted leading-relaxed">
                        Often called "Soft Gold", Pashmina weaving originated in the high-altitude regions of Nepal and Tibet, where local mountain goats (Chyangra) grow a super-fine, warm undercoat to survive freezing winters. For centuries, this fleece has been hand-combed, hand-spun, and woven on wooden handlooms in the Kathmandu Valley, producing lightweight, warm luxury wraps worn by royalty.
                    </p>
                </div>
            </div>

            <!-- Wooden Carvings -->
            <div class="bg-white border border-border rounded-2xl overflow-hidden hover:shadow-lg hover:border-accent transition duration-300 flex flex-col group">
                <div class="h-[220px] overflow-hidden relative">
                    <span class="absolute top-4 left-4 bg-primary text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded">Malla Dynasty</span>
                    <img src="<?= SITE_URL ?>/assets/images/uploads/carved-window-panel.jpg" alt="Wooden carving history" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-6 flex-grow">
                    <h5 class="font-playfair text-lg font-bold text-dark mb-3">Newari Woodcarving</h5>
                    <p class="text-xs text-muted leading-relaxed">
                        Woodcarving is one of Nepal's oldest architectural legacies, reaching its absolute peak during the Malla Dynasty (12th-18th centuries). The Newar communities of Bhaktapur and Patan carved seasoned Sal and Devdar wood to create the breathtaking peacock windows, shrines, and multi-tiered temples that Kathmandu is famous for. Each pattern symbolizes spiritual guardianship and sacred geometry.
                    </p>
                </div>
            </div>

            <!-- Dhaka Fabric -->
            <div class="bg-white border border-border rounded-2xl overflow-hidden hover:shadow-lg hover:border-accent transition duration-300 flex flex-col group">
                <div class="h-[220px] overflow-hidden relative">
                    <span class="absolute top-4 left-4 bg-primary text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded">Traditional Loom</span>
                    <img src="<?= SITE_URL ?>/assets/images/uploads/dhaka-topi-shawl-set.jpg" alt="Dhaka weave history" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-6 flex-grow">
                    <h5 class="font-playfair text-lg font-bold text-dark mb-3">Dhaka Fabric Weaving</h5>
                    <p class="text-xs text-muted leading-relaxed">
                        Dhaka represents the pride of indigenous Nepali weaving, traditionally hand-loomed by Limbu and Rai women in eastern hilly districts like Tehrathum and Palpa. The complex, colorful geometric patterns are created entirely by hand, thread by thread, without any computer aids or automated machinery. Historically, wearing Dhaka marked prestige, courage, and cultural identity.
                    </p>
                </div>
            </div>

            <!-- Lokta Paper -->
            <div class="bg-white border border-border rounded-2xl overflow-hidden hover:shadow-lg hover:border-accent transition duration-300 flex flex-col group">
                <div class="h-[220px] overflow-hidden relative">
                    <span class="absolute top-4 left-4 bg-primary text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded">1000+ Years</span>
                    <img src="<?= SITE_URL ?>/assets/images/uploads/lokta-paper-photo-album.jpg" alt="Lokta paper history" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-6 flex-grow">
                    <h5 class="font-playfair text-lg font-bold text-dark mb-3">Handmade Lokta Paper</h5>
                    <p class="text-xs text-muted leading-relaxed">
                        Lokta paper-making is an ancient Nepalese craft that dates back over a thousand years. The paper is crafted from the fibrous inner bark of the high-altitude Daphne (Lokta) bushes native to the Himalayas. It is naturally resistant to insects and moisture. Historically, royal decrees and sacred scriptures were written on Lokta paper due to its incredible longevity and durability.
                    </p>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center py-10 mt-12 bg-cream border border-border rounded-2xl max-w-[800px] mx-auto shadow-sm px-6">
            <h3 class="font-playfair text-[1.8rem] font-bold text-dark mb-2">Support the Legacy of Nepali Artisans</h3>
            <p class="text-muted text-sm max-w-[600px] mx-auto mb-6">By purchasing these handcrafted treasures, you become a custodian of this living history, directly funding the livelihoods of the artisan families behind these creations.</p>
            <div class="flex gap-4 justify-center">
                <a href="<?= SITE_URL ?>/products.php" class="py-2.5 px-6 bg-primary hover:bg-primary-dark text-white rounded-lg font-semibold text-sm transition duration-300 shadow flex items-center gap-1.5"><i class="bi bi-shop"></i>Explore the Shop</a>
                <a href="<?= SITE_URL ?>/pages/contact.php" class="py-2.5 px-6 border border-muted text-muted hover:bg-white rounded-lg font-semibold text-sm transition duration-300 flex items-center gap-1.5"><i class="bi bi-telephone"></i>Get in Touch</a>
            </div>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?>
