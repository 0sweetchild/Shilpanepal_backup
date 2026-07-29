<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'About';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
include '../includes/header.php';
?>
<main class="flex-grow">
    <!-- Hero Section -->
    <div class="py-16 text-center relative overflow-hidden bg-gradient-to-r from-primary-dark to-primary text-white flex items-center justify-center min-h-[250px]">
        <div class="absolute w-full h-full opacity-15 top-0 left-0" style="background-image: radial-gradient(var(--accent) 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="container relative z-10 max-w-[800px]">
            <span class="text-xs uppercase tracking-[2px] font-semibold text-accent mb-2.5 inline-block">Preserving Heritage</span>
            <h1 class="font-playfair text-[2.5rem] md:text-[3rem] font-bold mb-3 leading-tight">About ShilpaNepal</h1>
            <p class="text-sm md:text-base font-light text-white/90 leading-relaxed max-w-[640px] mx-auto">Bringing authentic, hand-carved, and hand-woven wonders from the heart of the Himalayas directly to your home.</p>
        </div>
    </div>

    <!-- Content Sections -->
    <div class="container py-12">
        <!-- Our Story -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center mb-16">
            <div class="lg:col-span-6 space-y-4">
                <h2 class="font-playfair text-[2rem] font-bold text-dark relative pb-2.5">
                    Our Story
                    <span class="absolute bottom-0 start-0 w-[60px] h-[3px] bg-accent rounded-full"></span>
                </h2>
                <p class="text-sm text-muted leading-relaxed">
                    Founded in the historic streets of Kathmandu, <strong>ShilpaNepal</strong> is dedicated to supporting local Nepali artisans and preserving the centuries-old crafting traditions of Nepal. From the intricate brushstrokes of Thangka painters in Bhaktapur to the rhythmic hand-weaving of Dhaka and Pashmina shawl weavers, every single piece in our collection tells a unique story of devotion, heritage, and timeless art.
                </p>
                <p class="text-sm text-muted leading-relaxed">
                    We select our products directly from independent, generational master-craftsmen and micro-cooperatives. By purchasing from us, you directly support these skilled families and contribute to sustainable livelihood opportunities in local community clusters.
                </p>
            </div>
            <div class="lg:col-span-6">
                <div class="p-6 md:p-8 border border-border rounded-2xl bg-cream shadow-sm flex flex-col justify-center h-full space-y-4">
                    <div style="color: var(--accent);">
                        <i class="bi bi-quote text-[2.5rem] transform scale-x-[-1] inline-block"></i>
                    </div>
                    <h4 class="font-playfair text-lg text-primary font-bold leading-relaxed">“Art is not just a profession in Nepal; it is a spiritual practice passed down through generations.”</h4>
                    <p class="text-muted text-xs font-semibold">— Master Artisan, Lalitpur Carving Guild</p>
                </div>
            </div>
        </div>

        <!-- Our Core Values Grid -->
        <div class="py-12 mb-16 rounded-2xl px-6 md:px-12 bg-primary-light">
            <div class="text-center mb-10">
                <h2 class="font-playfair text-[2rem] font-bold text-dark">Our Core Values</h2>
                <p class="text-muted text-xs mt-1">The guiding principles behind every product we showcase.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Value 1 -->
                <div class="flex flex-col items-center md:items-start text-center md:text-left space-y-3">
                    <div class="flex items-center justify-center w-[60px] h-[60px] bg-white border border-border/40 rounded-xl text-primary shadow-sm">
                        <i class="bi bi-shield-check text-2xl"></i>
                    </div>
                    <h5 class="font-bold text-[1rem] text-dark">100% Authenticity</h5>
                    <p class="text-muted text-xs leading-relaxed">Every singing bowl, thangka, and woodcarving is fully authentic, crafted in Nepal by certified artisans using traditional methods.</p>
                </div>
                <!-- Value 2 -->
                <div class="flex flex-col items-center md:items-start text-center md:text-left space-y-3">
                    <div class="flex items-center justify-center w-[60px] h-[60px] bg-white border border-border/40 rounded-xl text-primary shadow-sm">
                        <i class="bi bi-people text-2xl"></i>
                    </div>
                    <h5 class="font-bold text-[1rem] text-dark">Fair Trade Principles</h5>
                    <p class="text-muted text-xs leading-relaxed">We work directly with artisans, eliminating middle-markups and ensuring fair wages and safe working conditions for all craftsmen.</p>
                </div>
                <!-- Value 3 -->
                <div class="flex flex-col items-center md:items-start text-center md:text-left space-y-3">
                    <div class="flex items-center justify-center w-[60px] h-[60px] bg-white border border-border/40 rounded-xl text-primary shadow-sm">
                        <i class="bi bi-globe text-2xl"></i>
                    </div>
                    <h5 class="font-bold text-[1rem] text-dark">Environmental Respect</h5>
                    <p class="text-muted text-xs leading-relaxed">We promote eco-friendly production methods, including natural dyes for fabric products and sustainably sourced wood and lokta paper.</p>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center py-6 bg-cream border border-border rounded-2xl max-w-[800px] mx-auto shadow-sm">
            <h3 class="font-playfair text-[1.8rem] font-bold text-dark mb-2">Explore Our Authentic Collection</h3>
            <p class="text-muted text-sm mb-6">Discover the magic of Nepali craftsmanship today.</p>
            <div class="flex gap-4 justify-center">
                <a href="<?= SITE_URL ?>/products.php" class="py-2.5 px-6 bg-primary hover:bg-primary-dark text-white rounded-lg font-semibold text-sm transition duration-300 shadow"><i class="bi bi-shop me-2"></i>Start Shopping</a>
                <a href="<?= SITE_URL ?>/pages/contact.php" class="py-2.5 px-6 border border-muted text-muted hover:bg-white rounded-lg font-semibold text-sm transition duration-300">Contact Us</a>
            </div>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?>
