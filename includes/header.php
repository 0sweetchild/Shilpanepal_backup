<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth_check.php';
$cartCount = getCartCount($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?><?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css?v=<?= time() ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                container: {
                    center: true,
                    padding: '1.5rem',
                },
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#8E1C24',
                            dark: '#580F14',
                            light: '#FAF3F3',
                        },
                        secondary: '#B23A44',
                        accent: {
                            DEFAULT: '#C5A880',
                            dark: '#A48962',
                        },
                        cream: '#FAF7F2',
                        dark: '#1F1616',
                        border: '#EBE5DC',
                        muted: '#847777',
                    },
                    fontFamily: {
                        playfair: ['Playfair Display', 'serif'],
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <script>const SITE_URL = "<?= SITE_URL ?>";</script>
</head>

<body class="bg-white text-dark font-sans antialiased min-h-screen flex flex-col">

    <nav class="sticky top-0 z-50 bg-white border-b border-border py-3">
        <div class="container flex flex-wrap justify-between items-center">
            <!-- Brand -->
            <a class="flex flex-col text-decoration-none" href="<?= SITE_URL ?>/index.php">
                <div class="font-playfair text-[24px] font-bold leading-tight">
                    <span class="text-primary">Shilpa</span><span class="text-accent">Nepal</span>
                </div>
                <span class="text-[9.5px] font-bold text-muted uppercase tracking-[1.5px] -mt-0.5">Crafted by Nature
                </span>
            </a>

            <!-- Mobile Toggler -->
            <button class="lg:hidden p-2 text-dark border border-border rounded-lg hover:bg-cream"
                onclick="document.getElementById('navMenu').classList.toggle('hidden')">
                <i class="bi bi-list text-xl"></i>
            </button>

            <!-- Navbar Menu -->
            <div class="hidden lg:flex items-center w-full lg:w-auto mt-4 lg:mt-0 flex-grow lg:flex-grow-0"
                id="navMenu">
                <!-- Search -->
                <form class="flex w-full lg:w-[380px] lg:mx-8 mb-4 lg:mb-0" action="<?= SITE_URL ?>/products.php"
                    method="GET">
                    <div class="flex w-full">
                        <input type="text" name="search"
                            class="w-full px-4 py-2 border border-border rounded-l-full bg-white/80 focus:outline-none focus:border-primary text-sm transition duration-300"
                            placeholder="Search Nepali handicrafts..."
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <button
                            class="px-5 py-2 border border-primary bg-primary hover:bg-primary-dark text-white rounded-r-full transition duration-300"
                            type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <ul
                    class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2 lg:gap-4 w-full lg:w-auto font-semibold text-sm">
                    <li><a class="block py-2 px-3 text-dark hover:text-primary transition duration-300"
                            href="<?= SITE_URL ?>/products.php">Shop</a></li>
                    <li><a class="block py-2 px-3 text-dark hover:text-primary transition duration-300"
                            href="<?= SITE_URL ?>/pages/history.php">Our History</a></li>
                    <li><a class="block py-2 px-3 text-dark hover:text-primary transition duration-300"
                            href="<?= SITE_URL ?>/pages/about.php">About</a></li>
                    <li><a class="block py-2 px-3 text-dark hover:text-primary transition duration-300"
                            href="<?= SITE_URL ?>/pages/contact.php">Contact</a></li>

                    <?php if (isLoggedIn()): ?>
                        <li class="relative">
                            <a class="cart-icon flex items-center gap-1 py-2 px-3 text-dark hover:text-primary transition duration-300"
                                href="<?= SITE_URL ?>/cart.php">
                                <i class="bi bi-cart3 text-lg"></i>
                                <?php if ($cartCount > 0): ?>
                                    <span
                                        class="cart-badge bg-primary text-white text-[9px] font-bold rounded-full w-5 h-5 flex items-center justify-center border-2 border-white -mt-2 -ml-1"><?= $cartCount ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="relative">
                            <button onclick="document.getElementById('userDropdown').classList.toggle('hidden')"
                                class="flex items-center gap-1.5 py-2 px-3 text-dark hover:text-primary transition duration-300 font-semibold focus:outline-none">
                                <i
                                    class="bi bi-person-circle text-lg"></i><?= htmlspecialchars(explode(' ', $_SESSION['full_name'])[0]) ?>
                                <i class="bi bi-chevron-down text-[10px]"></i>
                            </button>
                            <ul id="userDropdown"
                                class="hidden absolute right-0 mt-2 w-48 bg-white border border-border rounded-lg shadow-lg py-1 z-50">
                                <?php if (isAdmin()): ?>
                                    <li><a class="flex items-center gap-2 px-4 py-2 text-sm text-dark hover:bg-cream hover:text-primary transition"
                                            href="<?= SITE_URL ?>/admin/dashboard.php"><i class="bi bi-speedometer2"></i>Admin
                                            Panel</a></li>
                                    <li class="border-t border-border my-1"></li>
                                <?php else: ?>
                                    <li><a class="flex items-center gap-2 px-4 py-2 text-sm text-dark hover:bg-cream hover:text-primary transition"
                                            href="<?= SITE_URL ?>/orders.php"><i class="bi bi-bag-check"></i>My Orders</a></li>
                                    <li class="border-t border-border my-1"></li>
                                <?php endif; ?>
                                <li><a class="flex items-center gap-2 px-4 py-2 text-sm text-primary hover:bg-cream transition"
                                        href="<?= SITE_URL ?>/auth/logout.php"><i
                                            class="bi bi-box-arrow-right"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li>
                            <a class="cart-icon flex items-center gap-1 py-2 px-3 text-dark hover:text-primary transition duration-300"
                                href="<?= SITE_URL ?>/cart.php">
                                <i class="bi bi-cart3 text-lg"></i>
                            </a>
                        </li>
                        <li><a class="inline-block py-1.5 px-4 text-center border-2 border-primary text-primary hover:bg-primary hover:text-white rounded-lg transition duration-300 font-semibold text-xs"
                                href="<?= SITE_URL ?>/auth/login.php">Login</a></li>
                        <li><a class="inline-block py-1.5 px-4 text-center bg-primary hover:bg-primary-dark text-white rounded-lg transition duration-300 font-semibold text-xs"
                                href="<?= SITE_URL ?>/auth/register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-50 border-b border-green-200 text-green-800 py-3 px-4 transition-all duration-300"
            id="successAlert">
            <div class="container flex justify-between items-center text-sm font-medium">
                <div><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['success']) ?></div>
                <button type="button" class="text-green-800 hover:text-green-950"
                    onclick="document.getElementById('successAlert').style.display='none'"><i
                        class="bi bi-x-lg"></i></button>
            </div>
        </div>
        <?php unset($_SESSION['success']); endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-50 border-b border-red-200 text-red-800 py-3 px-4 transition-all duration-300" id="errorAlert">
            <div class="container flex justify-between items-center text-sm font-medium">
                <div><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($_SESSION['error']) ?></div>
                <button type="button" class="text-red-800 hover:text-red-950"
                    onclick="document.getElementById('errorAlert').style.display='none'"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>
        <?php unset($_SESSION['error']); endif; ?>