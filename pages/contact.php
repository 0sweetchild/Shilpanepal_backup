<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Contact';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';

$successMsg = '';
$errorMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($message)) {
        $errorMsg = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = 'Please enter a valid email address.';
    } else {
        $successMsg = 'Thank you for reaching out! Your message has been received, and our support team will get back to you within 24 hours.';
    }
}
include '../includes/header.php';
?>
<main class="flex-grow">
    <!-- Hero Section -->
    <div class="py-16 text-center relative overflow-hidden bg-gradient-to-r from-primary-dark to-primary text-white flex items-center justify-center min-h-[250px]">
        <div class="absolute w-full h-full opacity-15 top-0 left-0" style="background-image: radial-gradient(var(--accent) 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="container relative z-10 max-w-[800px]">
            <span class="text-xs uppercase tracking-[2px] font-semibold text-accent mb-2.5 inline-block">Get In Touch</span>
            <h1 class="font-playfair text-[2.5rem] md:text-[3rem] font-bold mb-3 leading-tight">Contact Us</h1>
            <p class="text-sm md:text-base font-light text-white/90 leading-relaxed max-w-[640px] mx-auto">Have questions about our handicrafts, custom orders, or shipping? We are here to help.</p>
        </div>
    </div>

    <div class="container py-12">
        <?php if (!empty($successMsg)): ?>
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-2xl p-6 mb-8 flex justify-between items-start shadow-sm" id="contactSuccess">
                <div class="flex gap-4">
                    <i class="bi bi-check-circle-fill text-2xl text-green-600"></i>
                    <div>
                        <h5 class="font-playfair font-bold text-base text-green-900">Message Sent Successfully!</h5>
                        <p class="text-xs text-green-800/90 mt-1"><?= htmlspecialchars($successMsg) ?></p>
                    </div>
                </div>
                <button type="button" class="text-green-800 hover:text-green-950 text-lg" onclick="document.getElementById('contactSuccess').style.display='none'"><i class="bi bi-x-lg"></i></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-2xl p-6 mb-8 flex justify-between items-start shadow-sm" id="contactError">
                <div class="flex gap-4">
                    <i class="bi bi-exclamation-triangle-fill text-2xl text-red-600"></i>
                    <div>
                        <h5 class="font-playfair font-bold text-base text-red-900">Validation Error</h5>
                        <p class="text-xs text-red-800/90 mt-1"><?= htmlspecialchars($errorMsg) ?></p>
                    </div>
                </div>
                <button type="button" class="text-red-800 hover:text-red-950 text-lg" onclick="document.getElementById('contactError').style.display='none'"><i class="bi bi-x-lg"></i></button>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <!-- Contact Details & Map -->
            <div class="lg:col-span-5 space-y-8">
                <div class="space-y-4">
                    <h3 class="font-playfair text-[1.8rem] font-bold text-dark relative pb-2.5">
                        Contact Details
                        <span class="absolute bottom-0 start-0 w-[50px] h-[3px] bg-accent rounded-full"></span>
                    </h3>
                    
                    <div class="space-y-4 pt-4">
                        <div class="flex items-start gap-4">
                            <div class="flex items-center justify-center border border-border bg-cream rounded-xl text-primary w-11 h-11 flex-shrink-0 shadow-sm">
                                <i class="bi bi-geo-alt text-lg"></i>
                            </div>
                            <div>
                                <h6 class="font-playfair font-bold text-sm text-dark leading-tight">Address</h6>
                                <span class="text-muted text-xs">Kirtipur, Kathmandu, Nepal</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex items-center justify-center border border-border bg-cream rounded-xl text-primary w-11 h-11 flex-shrink-0 shadow-sm">
                                <i class="bi bi-telephone text-lg"></i>
                            </div>
                            <div>
                                <h6 class="font-playfair font-bold text-sm text-dark leading-tight">Phone</h6>
                                <span class="text-muted text-xs">+977-9840339908</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex items-center justify-center border border-border bg-cream rounded-xl text-primary w-11 h-11 flex-shrink-0 shadow-sm">
                                <i class="bi bi-envelope text-lg"></i>
                            </div>
                            <div>
                                <h6 class="font-playfair font-bold text-sm text-dark leading-tight">Email</h6>
                                <span class="text-muted text-xs">info@shilpanepal.com</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex items-center justify-center border border-border bg-cream rounded-xl text-primary w-11 h-11 flex-shrink-0 shadow-sm">
                                <i class="bi bi-clock text-lg"></i>
                            </div>
                            <div>
                                <h6 class="font-playfair font-bold text-sm text-dark leading-tight">Business Hours</h6>
                                <span class="text-muted text-xs leading-relaxed">Sun - Fri: 9:00 AM - 6:00 PM (NPT)<br>Saturday: Closed</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google Map Placeholder Frame -->
                <div class="rounded-2xl border border-border bg-cream overflow-hidden shadow-sm h-[220px] relative">
                    <div class="absolute w-full h-full flex flex-col items-center justify-center p-6 text-center">
                        <i class="bi bi-map-fill text-muted text-[2.2rem] mb-2"></i>
                        <h6 class="font-playfair font-bold text-sm text-dark">Kathmandu, Nepal</h6>
                        <small class="text-muted text-xs mb-4">Kirtipur Area</small>
                        <a href="https://maps.google.com" target="_blank" class="py-1.5 px-4 border-2 border-primary text-primary hover:bg-primary hover:text-white rounded-lg transition duration-300 font-semibold text-xs text-center uppercase">Open in Google Maps</a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="lg:col-span-7 space-y-4">
                <h3 class="font-playfair text-[1.8rem] font-bold text-dark relative pb-2.5">
                    Send Us a Message
                    <span class="absolute bottom-0 start-0 w-[50px] h-[3px] bg-accent rounded-full"></span>
                </h3>
                
                <form method="POST" class="space-y-4 pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Your Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="w-full px-4 py-2 border border-border rounded-lg bg-white focus:outline-none focus:border-primary text-sm" placeholder="John Doe" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Your Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" class="w-full px-4 py-2 border border-border rounded-lg bg-white focus:outline-none focus:border-primary text-sm" placeholder="john@example.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Subject</label>
                        <input type="text" name="subject" class="w-full px-4 py-2 border border-border rounded-lg bg-white focus:outline-none focus:border-primary text-sm" placeholder="Inquiry about custom Thangka sizes..." value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Message <span class="text-red-500">*</span></label>
                        <textarea name="message" class="w-full px-4 py-3 border border-border rounded-lg bg-white focus:outline-none focus:border-primary text-sm" rows="5" placeholder="Write your message here..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <button type="submit" name="send_message" class="py-2.5 px-6 bg-primary hover:bg-primary-dark text-white rounded-lg font-semibold text-sm transition duration-300 shadow flex items-center gap-2"><i class="bi bi-send-fill text-xs"></i>Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?>
