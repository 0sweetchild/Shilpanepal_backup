<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
if (isLoggedIn()) { header("Location: " . SITE_URL . "/index.php"); exit(); }
$errors = []; $input = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input['full_name'] = trim($_POST['full_name'] ?? '');
    $input['email']     = strtolower(trim($_POST['email'] ?? ''));
    $input['phone']     = trim($_POST['phone'] ?? '');
    $input['address']   = trim($_POST['address'] ?? '');
    $input['password']  = $_POST['password'] ?? '';
    $input['confirm']   = $_POST['confirm_password'] ?? '';
    if (strlen($input['full_name']) < 3) {
        $errors['full_name'] = 'Full name must be at least 3 characters.';
    } elseif (!preg_match('/^[a-zA-Z\s\.\'\-]+$/', $input['full_name'])) {
        $errors['full_name'] = 'Full name must contain letters only.';
    }
    if (empty($input['email'])) {
        $errors['email'] = 'Email address is required.';
    } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $input['email'])) {
        $errors['email'] = 'Please enter a valid email address (e.g. name@example.com).';
    }
    if (!preg_match('/^(98|97|96)\d{8}$/', $input['phone'])) $errors['phone'] = 'Enter a valid Nepal phone number.';
    if (empty($input['address'])) $errors['address'] = 'Address is required.';
    if (strlen($input['password']) < 8) $errors['password'] = 'Password must be at least 8 characters.';
    if ($input['password'] !== $input['confirm']) $errors['confirm'] = 'Passwords do not match.';
    if (empty($errors['email'])) {
        $s = $conn->prepare("SELECT id FROM users WHERE email=?");
        $s->bind_param("s", $input['email']); $s->execute();
        if ($s->get_result()->num_rows > 0) $errors['email'] = 'Email already registered.';
    }
    if (empty($errors)) {
        $hashed = password_hash($input['password'], PASSWORD_BCRYPT);
        $s = $conn->prepare("INSERT INTO users (full_name,email,phone,password,address,role) VALUES (?,?,?,?,?,'customer')");
        $s->bind_param("sssss", $input['full_name'], $input['email'], $input['phone'], $hashed, $input['address']);
        if ($s->execute()) {
            $_SESSION['user_id']   = $s->insert_id;
            $_SESSION['full_name'] = $input['full_name'];
            $_SESSION['email']     = $input['email'];
            $_SESSION['role']      = 'customer';
            $_SESSION['success']   = "Welcome to ShilpaNepal, " . htmlspecialchars($input['full_name']) . "!";
            header("Location: " . SITE_URL . "/index.php"); exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Register | ShilpaNepal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              primary: {
                DEFAULT: '#8E1C24',
                dark: '#580F14',
                light: '#FAF3F3',
              },
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
</head>
<body class="bg-cream text-dark font-sans min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
<div class="max-w-md w-full bg-white border border-border rounded-2xl shadow-md p-8 sm:p-12">
    <div class="text-center mb-8">
        <a href="<?= SITE_URL ?>/index.php" class="inline-block text-decoration-none">
            <div class="font-playfair text-[2rem] font-bold leading-tight">
                <span class="text-primary">Shilpa</span><span class="text-accent">Nepal</span>
            </div>
        </a>
        <h4 class="font-playfair text-xl font-bold text-dark mt-4">Create your account</h4>
        <p class="text-xs text-muted mt-1">Join ShilpaNepal today</p>
    </div>
    
    <form method="POST" class="space-y-4" novalidate>
        <div>
            <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Full Name <span class="text-red-500">*</span></label>
            <div class="flex relative rounded-lg overflow-hidden border <?= isset($errors['full_name'])?'border-red-500':'border-border' ?> focus-within:border-primary">
                <span class="bg-cream/40 px-3 flex items-center border-r border-border text-muted"><i class="bi bi-person"></i></span>
                <input type="text" name="full_name" class="w-full px-4 py-2 text-sm bg-transparent focus:outline-none" value="<?= htmlspecialchars($input['full_name'] ?? '') ?>">
            </div>
            <?php if(isset($errors['full_name'])): ?><div class="text-red-600 text-xs font-semibold mt-1"><?= $errors['full_name'] ?></div><?php endif; ?>
        </div>
        
        <div>
            <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Email <span class="text-red-500">*</span></label>
            <div class="flex relative rounded-lg overflow-hidden border <?= isset($errors['email'])?'border-red-500':'border-border' ?> focus-within:border-primary">
                <span class="bg-cream/40 px-3 flex items-center border-r border-border text-muted"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="w-full px-4 py-2 text-sm bg-transparent focus:outline-none" value="<?= htmlspecialchars($input['email'] ?? '') ?>">
            </div>
            <?php if(isset($errors['email'])): ?><div class="text-red-600 text-xs font-semibold mt-1"><?= $errors['email'] ?></div><?php endif; ?>
        </div>
        
        <div>
            <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Phone <span class="text-red-500">*</span></label>
            <div class="flex relative rounded-lg overflow-hidden border <?= isset($errors['phone'])?'border-red-500':'border-border' ?> focus-within:border-primary">
                <span class="bg-cream/40 px-3 flex items-center border-r border-border text-muted text-xs font-bold">+977</span>
                <input type="tel" name="phone" class="w-full px-4 py-2 text-sm bg-transparent focus:outline-none" placeholder="98XXXXXXXX" value="<?= htmlspecialchars($input['phone'] ?? '') ?>">
            </div>
            <?php if(isset($errors['phone'])): ?><div class="text-red-600 text-xs font-semibold mt-1"><?= $errors['phone'] ?></div><?php endif; ?>
        </div>
        
        <div>
            <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Delivery Address <span class="text-red-500">*</span></label>
            <div class="flex relative rounded-lg overflow-hidden border <?= isset($errors['address'])?'border-red-500':'border-border' ?> focus-within:border-primary">
                <span class="bg-cream/40 px-3 flex items-center border-r border-border text-muted"><i class="bi bi-geo-alt"></i></span>
                <input type="text" name="address" class="w-full px-4 py-2 text-sm bg-transparent focus:outline-none" placeholder="Kirtipur, Kathmandu" value="<?= htmlspecialchars($input['address'] ?? '') ?>">
            </div>
            <?php if(isset($errors['address'])): ?><div class="text-red-600 text-xs font-semibold mt-1"><?= $errors['address'] ?></div><?php endif; ?>
        </div>
        
        <div>
            <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Password <span class="text-red-500">*</span></label>
            <div class="flex relative rounded-lg overflow-hidden border <?= isset($errors['password'])?'border-red-500':'border-border' ?> focus-within:border-primary">
                <span class="bg-cream/40 px-3 flex items-center border-r border-border text-muted"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" id="pwd" class="w-full px-4 py-2 text-sm bg-transparent focus:outline-none" placeholder="Min 8 characters">
                <button class="px-3 hover:bg-cream/35 transition text-muted" type="button" onclick="var f=document.getElementById('pwd');f.type=f.type==='password'?'text':'password'"><i class="bi bi-eye"></i></button>
            </div>
            <?php if(isset($errors['password'])): ?><div class="text-red-600 text-xs font-semibold mt-1"><?= $errors['password'] ?></div><?php endif; ?>
        </div>
        
        <div>
            <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Confirm Password <span class="text-red-500">*</span></label>
            <div class="flex relative rounded-lg overflow-hidden border <?= isset($errors['confirm'])?'border-red-500':'border-border' ?> focus-within:border-primary">
                <span class="bg-cream/40 px-3 flex items-center border-r border-border text-muted"><i class="bi bi-lock-fill"></i></span>
                <input type="password" name="confirm_password" class="w-full px-4 py-2 text-sm bg-transparent focus:outline-none" placeholder="Repeat password">
            </div>
            <?php if(isset($errors['confirm'])): ?><div class="text-red-600 text-xs font-semibold mt-1"><?= $errors['confirm'] ?></div><?php endif; ?>
        </div>
        
        <button type="submit" class="w-full py-2.5 bg-primary hover:bg-primary-dark text-white rounded-lg font-semibold text-sm transition duration-300 flex items-center justify-center gap-2 shadow mt-6"><i class="bi bi-person-plus"></i>Create Account</button>
    </form>
    
    <hr class="border-border my-6">
    
    <p class="text-center text-xs text-muted">Already have an account? <a href="<?= SITE_URL ?>/auth/login.php" class="font-bold text-primary hover:underline">Sign in</a></p>
</div>
</body>
</html>
