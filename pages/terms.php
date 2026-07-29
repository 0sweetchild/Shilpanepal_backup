<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Terms & Conditions';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
include '../includes/header.php';
?>
<main class="flex-grow">
    <!-- Hero Section -->
    <div class="py-16 text-center relative overflow-hidden bg-gradient-to-r from-primary-dark to-primary text-white flex items-center justify-center min-h-[250px]">
        <div class="absolute w-full h-full opacity-15 top-0 left-0" style="background-image: radial-gradient(var(--accent) 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="container relative z-10 max-w-[800px]">
            <span class="text-xs uppercase tracking-[2px] font-semibold text-accent mb-2.5 inline-block">Terms of Service</span>
            <h1 class="font-playfair text-[2.5rem] md:text-[3rem] font-bold mb-3 leading-tight">Terms & Conditions</h1>
            <p class="text-sm md:text-base font-light text-white/90 leading-relaxed max-w-[640px] mx-auto">Please read these terms and conditions carefully before using our services.</p>
        </div>
    </div>

    <!-- Content Section -->
    <div class="container py-12 max-w-[800px]">
        <div class="space-y-8 text-sm text-muted leading-relaxed">
            <p class="text-xs italic text-right">Last Updated: July 13, 2026</p>

            <section class="space-y-3">
                <h3 class="font-playfair text-xl font-bold text-dark pb-2 border-b border-border">1. Agreement to Terms</h3>
                <p>By accessing or using the <strong>ShilpaNepal</strong> website, you agree to be bound by these Terms & Conditions and all applicable laws and regulations of Nepal. If you do not agree with any of these terms, you are prohibited from using or accessing this site.</p>
            </section>

            <section class="space-y-3">
                <h3 class="font-playfair text-xl font-bold text-dark pb-2 border-b border-border">2. Account Registration & Security</h3>
                <p>When you create an account with us, you must provide accurate, complete, and current information. You are responsible for safeguarding the password that you use to access the site and for any activities or actions under your password.</p>
            </section>

            <section class="space-y-3">
                <h3 class="font-playfair text-xl font-bold text-dark pb-2 border-b border-border">3. Handmade Product Nature</h3>
                <p>All items sold on ShilpaNepal (including Thangka paintings, singing bowls, Pashmina shawls, and wood carvings) are <strong>100% handcrafted</strong> by local artisans. Because of this:</p>
                <ul class="list-disc pl-5 space-y-1.5">
                    <li>Slight variations in color, texture, design patterns, and dimensions are natural and expected.</li>
                    <li>These variations do not represent product defects, but rather add to the unique, one-of-a-kind value of each piece.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h3 class="font-playfair text-xl font-bold text-dark pb-2 border-b border-border">4. Pricing and Payments</h3>
                <p>All prices listed on our site are in <strong>Nepalese Rupees (NPR)</strong> unless specified otherwise. We reserve the right to change prices at any time without notice. Payments are securely processed via <strong>eSewa</strong>. Order processing will begin only after confirmation of successful payment transaction from our gateway.</p>
            </section>

            <section class="space-y-3">
                <h3 class="font-playfair text-xl font-bold text-dark pb-2 border-b border-border">5. Intellectual Property</h3>
                <p>All content, images, brand logos, code, designs, and text on this website are the intellectual property of ShilpaNepal and are protected by Nepalese copyright, trademark, and intellectual property laws. You may not reproduce, copy, or redistribute any materials without explicit written permission.</p>
            </section>

            <section class="space-y-3">
                <h3 class="font-playfair text-xl font-bold text-dark pb-2 border-b border-border">6. Limitation of Liability</h3>
                <p>In no event shall ShilpaNepal, nor its founders, staff, or artisan partners, be liable for any indirect, incidental, special, consequential, or punitive damages arising out of your access to or use of our products and services.</p>
            </section>

            <div class="pt-6 border-t border-border flex justify-between items-center">
                <a href="<?= SITE_URL ?>/index.php" class="inline-flex items-center gap-1.5 py-2 px-5 border-2 border-primary text-primary hover:bg-primary hover:text-white rounded-lg transition font-semibold text-xs"><i class="bi bi-arrow-left"></i>Back to Home</a>
                <span class="text-xs text-muted">ShilpaNepal Handicrafts</span>
            </div>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?>
