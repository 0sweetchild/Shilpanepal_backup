</main>
<footer class="bg-dark text-white/70 pt-16 mt-12 border-t-4 border-primary">
    <div class="container pb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 mb-12">
            <!-- Brand Column -->
            <div class="lg:col-span-4">
                <h5 class="font-playfair text-[22px] font-bold text-white leading-tight mb-3">
                    <span class="text-white">Shilpa</span><span class="text-accent">Nepal</span>
                </h5>
                <p class="text-sm text-white/50">Bringing authentic Nepali handicrafts to the world.</p>
                <div class="flex gap-4 mt-6">
                    <a href="#"
                        class="text-white/45 hover:text-accent hover:-translate-y-1 transition-all duration-300 text-xl"><i
                            class="bi bi-facebook"></i></a>
                    <a href="#"
                        class="text-white/45 hover:text-accent hover:-translate-y-1 transition-all duration-300 text-xl"><i
                            class="bi bi-instagram"></i></a>
                    <a href="#"
                        class="text-white/45 hover:text-accent hover:-translate-y-1 transition-all duration-300 text-xl"><i
                            class="bi bi-twitter-x"></i></a>
                </div>
            </div>

            <!-- Shop Links -->
            <div class="col-span-1 lg:col-span-2">
                <h6 class="text-white font-bold text-xs uppercase tracking-widest mb-6">Shop</h6>
                <ul class="space-y-3 text-sm">
                    <li><a href="<?= SITE_URL ?>/products.php"
                            class="text-white/55 hover:text-accent hover:pl-1 transition-all duration-300">All
                            Products</a></li>
                    <li><a href="<?= SITE_URL ?>/products.php?category=thangka-paintings"
                            class="text-white/55 hover:text-accent hover:pl-1 transition-all duration-300">Thangka</a>
                    </li>
                    <li><a href="<?= SITE_URL ?>/products.php?category=pashmina-shawls"
                            class="text-white/55 hover:text-accent hover:pl-1 transition-all duration-300">Pashmina</a>
                    </li>
                    <li><a href="<?= SITE_URL ?>/products.php?category=singing-bowls"
                            class="text-white/55 hover:text-accent hover:pl-1 transition-all duration-300">Singing
                            Bowls</a></li>
                </ul>
            </div>

            <!-- Info Links -->
            <div class="col-span-1 lg:col-span-2">
                <h6 class="text-white font-bold text-xs uppercase tracking-widest mb-6">Info</h6>
                <ul class="space-y-3 text-sm">
                    <li><a href="<?= SITE_URL ?>/pages/about.php"
                            class="text-white/55 hover:text-accent hover:pl-1 transition-all duration-300">About Us</a>
                    </li>
                    <li><a href="<?= SITE_URL ?>/pages/history.php"
                            class="text-white/55 hover:text-accent hover:pl-1 transition-all duration-300">Our
                            History</a></li>
                    <li><a href="<?= SITE_URL ?>/pages/contact.php"
                            class="text-white/55 hover:text-accent hover:pl-1 transition-all duration-300">Contact</a>
                    </li>
                    <li><a href="<?= SITE_URL ?>/pages/privacy-policy.php"
                            class="text-white/55 hover:text-accent hover:pl-1 transition-all duration-300">Privacy
                            Policy</a></li>
                    <li><a href="<?= SITE_URL ?>/pages/terms.php"
                            class="text-white/55 hover:text-accent hover:pl-1 transition-all duration-300">Terms &
                            Conditions</a></li>
                    <li><a href="<?= SITE_URL ?>/pages/refund-policy.php"
                            class="text-white/55 hover:text-accent hover:pl-1 transition-all duration-300">Refund
                            Policy</a></li>
                </ul>
            </div>

            <!-- Contact Details -->
            <div class="lg:col-span-4">
                <h6 class="text-white font-bold text-xs uppercase tracking-widest mb-6">Contact Us</h6>
                <ul class="space-y-4 text-sm text-white/55">
                    <li class="flex items-start gap-2.5"><i class="bi bi-geo-alt text-accent text-lg"></i>Kirtipur,
                        Kathmandu, Nepal</li>
                    <li class="flex items-start gap-2.5"><i
                            class="bi bi-telephone text-accent text-lg"></i>+977-9840339908</li>
                    <li class="flex items-start gap-2.5"><i
                            class="bi bi-envelope text-accent text-lg"></i>info@shilpanepal.com</li>
                    <li class="flex items-start gap-2.5 border-t border-white/10 pt-3"><i
                            class="bi bi-truck text-accent text-lg"></i>Free shipping on orders above NPR 2000</li>
                </ul>
                <div class="flex flex-wrap gap-2 mt-6">
                    <span
                        class="bg-white/5 border border-white/10 text-white/70 px-3.5 py-1.5 rounded-lg text-xs font-semibold">eSewa
                        Payments</span>
                    <span
                        class="bg-white/5 border border-white/10 text-white/70 px-3.5 py-1.5 rounded-lg text-xs font-semibold">Free
                        Delivery</span>
                </div>
            </div>
        </div>

        <hr class="border-white/10 my-8">

        <div class="text-center text-xs text-white/40">
            &copy; <?= date('Y') ?> ShilpaNepal. All rights reserved. Made in Nepal.
        </div>
    </div>
</footer>

<script src="<?= SITE_URL ?>/assets/js/cart.js?v=<?= time() ?>"></script>
</body>

</html>