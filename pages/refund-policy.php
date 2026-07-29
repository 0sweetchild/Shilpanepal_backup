<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Refund Policy';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
include '../includes/header.php';
?>
<main class="flex-grow">
    <!-- Hero Section -->
    <div class="py-16 text-center relative overflow-hidden bg-gradient-to-r from-primary-dark to-primary text-white flex items-center justify-center min-h-[250px]">
        <div class="absolute w-full h-full opacity-15 top-0 left-0" style="background-image: radial-gradient(var(--accent) 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="container relative z-10 max-w-[800px]">
            <span class="text-xs uppercase tracking-[2px] font-semibold text-accent mb-2.5 inline-block">Customer Care</span>
            <h1 class="font-playfair text-[2.5rem] md:text-[3rem] font-bold mb-3 leading-tight">Refund Policy</h1>
            <p class="text-sm md:text-base font-light text-white/90 leading-relaxed max-w-[640px] mx-auto">Thank you for shopping at ShilpaNepal. Read our guidelines on returns and refunds.</p>
        </div>
    </div>

    <!-- Content Section -->
    <div class="container py-12 max-w-[800px]">
        <div class="space-y-8 text-sm text-muted leading-relaxed">
            <p class="text-xs italic text-right">Last Updated: July 13, 2026</p>

            <section class="space-y-3">
                <h3 class="font-playfair text-xl font-bold text-dark pb-2 border-b border-border">1. Return Eligibility</h3>
                <p>We take immense pride in the craftsmanship of our items. If you are not entirely satisfied with your purchase, we are here to help. You have <strong>7 calendar days</strong> from the date you received an item to return it.</p>
                <p>To be eligible for a return, the item must be:</p>
                <ul class="list-disc pl-5 space-y-1.5">
                    <li>Unused and in the same pristine condition that you received it.</li>
                    <li>In its original packaging, including all protective bubble wraps, labels, and certificates of authenticity.</li>
                    <li>Accompanied by the original receipt or proof of purchase.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h3 class="font-playfair text-xl font-bold text-dark pb-2 border-b border-border">2. Non-Returnable Items</h3>
                <p>Certain items are exempt from standard returns:</p>
                <ul class="list-disc pl-5 space-y-1.5">
                    <li><strong>Custom Orders:</strong> Hand-commissioned or custom-painted Thangka paintings made to your specifications.</li>
                    <li><strong>Clearance Items:</strong> Products bought during special clearance sales or marked as "Final Sale".</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h3 class="font-playfair text-xl font-bold text-dark pb-2 border-b border-border">3. Return Process</h3>
                <p>To initiate a return, please contact us at <a href="mailto:<?= SITE_EMAIL ?>" class="text-primary hover:underline font-semibold"><?= SITE_EMAIL ?></a> with your order number and photos of the item. Once your return is accepted:</p>
                <ul class="list-disc pl-5 space-y-1.5">
                    <li>You can return the item in person or send it to our store outlet in Kirtipur, Kathmandu, Nepal.</li>
                    <li>Unless the product arrived damaged or defective, you will be responsible for paying your own shipping costs for returning your item. Shipping costs are non-refundable.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h3 class="font-playfair text-xl font-bold text-dark pb-2 border-b border-border">4. Refund Approval & Timing</h3>
                <p>Once we receive your item, we will inspect it and notify you that we have received your returned item. We will immediately notify you on the status of your refund after inspecting the item.</p>
                <p>If your return is approved, we will initiate a refund to your original payment method (such as an eSewa refund or direct bank transfer). You should expect to receive the credit within <strong>5 to 7 business days</strong>, depending on processing times.</p>
            </section>

            <section class="space-y-3">
                <h3 class="font-playfair text-xl font-bold text-dark pb-2 border-b border-border">5. Damaged or Defective Items</h3>
                <p>Since our products are handmade, slight organic imperfections are normal. However, if your item arrives damaged in transit, please email us within <strong>24 hours of delivery</strong> with photos of the package and item. We will arrange a replacement or issue a full refund immediately at no extra cost to you.</p>
            </section>

            <div class="pt-6 border-t border-border flex justify-between items-center">
                <a href="<?= SITE_URL ?>/index.php" class="inline-flex items-center gap-1.5 py-2 px-5 border-2 border-primary text-primary hover:bg-primary hover:text-white rounded-lg transition font-semibold text-xs"><i class="bi bi-arrow-left"></i>Back to Home</a>
                <span class="text-xs text-muted">ShilpaNepal Handicrafts</span>
            </div>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?>
