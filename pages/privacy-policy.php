<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Privacy Policy';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
include '../includes/header.php';
?>
<main class="flex-grow">
    <!-- Hero Section -->
    <div class="py-16 text-center relative overflow-hidden bg-gradient-to-r from-primary-dark to-primary text-white flex items-center justify-center min-h-[250px]">
        <div class="absolute w-full h-full opacity-15 top-0 left-0" style="background-image: radial-gradient(var(--accent) 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="container relative z-10 max-w-[800px]">
            <span class="text-xs uppercase tracking-[2px] font-semibold text-accent mb-2.5 inline-block">Store Policies</span>
            <h1 class="font-playfair text-[2.5rem] md:text-[3rem] font-bold mb-3 leading-tight">Privacy Policy</h1>
            <p class="text-sm md:text-base font-light text-white/90 leading-relaxed max-w-[640px] mx-auto">Your privacy is important to us. Learn how we handle and protect your personal data.</p>
        </div>
    </div>

    <!-- Content Section -->
    <div class="container py-12 max-w-[800px]">
        <div class="space-y-8 text-sm text-muted leading-relaxed">
            <p class="text-xs italic text-right">Last Updated: July 13, 2026</p>

            <section class="space-y-3">
                <h3 class="font-playfair text-xl font-bold text-dark pb-2 border-b border-border">1. Introduction</h3>
                <p>Welcome to <strong>ShilpaNepal</strong>. We respect your privacy and are committed to protecting the personal data you share with us. This Privacy Policy outlines how we collect, use, disclose, and safeguard your information when you visit our website, register an account, or make a purchase.</p>
            </section>

            <section class="space-y-3">
                <h3 class="font-playfair text-xl font-bold text-dark pb-2 border-b border-border">2. Information We Collect</h3>
                <p>We collect personal information that you voluntarily provide to us when placing an order or registering on our site. This includes:</p>
                <ul class="list-disc pl-5 space-y-1.5">
                    <li><strong>Contact Details:</strong> Your full name, email address, telephone number, and physical billing/shipping address.</li>
                    <li><strong>Account Credentials:</strong> Passwords and account preferences for your registered shop profile.</li>
                    <li><strong>Transaction Details:</strong> Payment and transaction records (excluding raw credit card details or payment credentials, which are processed directly and securely via eSewa or our authorized payment gateway).</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h3 class="font-playfair text-xl font-bold text-dark pb-2 border-b border-border">3. How We Use Your Information</h3>
                <p>We use the collected information for various business purposes, including to:</p>
                <ul class="list-disc pl-5 space-y-1.5">
                    <li>Process your orders, verify payments, and arrange safe delivery of your handcrafted items.</li>
                    <li>Provide customer support, resolve issues, and respond to your inquiries.</li>
                    <li>Send transaction notifications, order confirmations, and updates on our shipping status.</li>
                    <li>Improve our website, offerings, and user experience.</li>
                    <li>Send promotional updates or newsletter campaigns, from which you can opt-out at any time.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h3 class="font-playfair text-xl font-bold text-dark pb-2 border-b border-border">4. Data Sharing and Third-Party Services</h3>
                <p>We do not sell or rent your personal information to third parties. We only share necessary customer data with trusted third parties to facilitate our services:</p>
                <ul class="list-disc pl-5 space-y-1.5">
                    <li><strong>Payment Gateways:</strong> Transaction details are shared with <strong>eSewa</strong> to securely process payments.</li>
                    <li><strong>Logistics Partners:</strong> Your shipping details and contact number are shared with local and international courier services to ensure delivery of your products.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h3 class="font-playfair text-xl font-bold text-dark pb-2 border-b border-border">5. Data Security</h3>
                <p>We implement a variety of security measures, including HTTPS/SSL encryption and restricted access controls, to maintain the safety of your personal information. However, no transmission method over the internet is 100% secure, and we cannot guarantee absolute security.</p>
            </section>

            <section class="space-y-3">
                <h3 class="font-playfair text-xl font-bold text-dark pb-2 border-b border-border">6. Your Rights</h3>
                <p>You have the right to request access to, edit, or delete the personal data we hold about you. You can update your account profile directly through the site or request full deletion of your customer record by emailing us at <a href="mailto:<?= SITE_EMAIL ?>" class="text-primary hover:underline font-semibold"><?= SITE_EMAIL ?></a>.</p>
            </section>

            <div class="pt-6 border-t border-border flex justify-between items-center">
                <a href="<?= SITE_URL ?>/index.php" class="inline-flex items-center gap-1.5 py-2 px-5 border-2 border-primary text-primary hover:bg-primary hover:text-white rounded-lg transition font-semibold text-xs"><i class="bi bi-arrow-left"></i>Back to Home</a>
                <span class="text-xs text-muted">ShilpaNepal Handicrafts</span>
            </div>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?>
