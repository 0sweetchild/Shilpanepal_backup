<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
if (isLoggedIn()) { header("Location: " . SITE_URL . "/index.php"); exit(); }
$error = ''; $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $conn->prepare("SELECT id,full_name,email,password,role FROM users WHERE email=?");
        $stmt->bind_param("s", $email); $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email']     = $user['email'];
            $_SESSION['role']      = $user['role'];
            $redirect = $_SESSION['redirect_after_login'] ?? null;
            unset($_SESSION['redirect_after_login']);
            if ($user['role'] === 'admin') header("Location: " . SITE_URL . "/admin/dashboard.php");
            elseif ($redirect) header("Location: " . $redirect);
            else header("Location: " . SITE_URL . "/index.php");
            exit();
        } else { $error = 'Invalid email or password.'; }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login | ShilpaNepal</title>
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
        <h4 class="font-playfair text-xl font-bold text-dark mt-4">Welcome back</h4>
        <p class="text-xs text-muted mt-1">Sign in to your ShilpaNepal account</p>
    </div>
    
    <?php if ($error): ?>
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-3 text-xs font-semibold flex items-center gap-2 mb-4">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
    <?php endif; ?>
    
    <div class="bg-blue-50 border border-blue-100 text-blue-800 rounded-lg p-3 text-xs mb-5">
        <strong>Demo Admin:</strong> admin@shilpanepal.com / Admin@1234
    </div>
    
    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Email Address</label>
            <div class="flex relative rounded-lg overflow-hidden border border-border focus-within:border-primary">
                <span class="bg-cream/40 px-3 flex items-center border-r border-border text-muted"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="w-full px-4 py-2 text-sm bg-transparent focus:outline-none" value="<?= htmlspecialchars($email) ?>" required autofocus>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Password</label>
            <div class="flex relative rounded-lg overflow-hidden border border-border focus-within:border-primary">
                <span class="bg-cream/40 px-3 flex items-center border-r border-border text-muted"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" id="pwd" class="w-full px-4 py-2 text-sm bg-transparent focus:outline-none" required>
                <button class="px-3 hover:bg-cream/35 transition text-muted" type="button" onclick="var f=document.getElementById('pwd');f.type=f.type==='password'?'text':'password'"><i class="bi bi-eye"></i></button>
            </div>
        </div>
        <button type="submit" class="w-full py-2.5 bg-primary hover:bg-primary-dark text-white rounded-lg font-semibold text-sm transition duration-300 flex items-center justify-center gap-2 shadow mt-6"><i class="bi bi-box-arrow-in-right"></i>Sign In</button>
    </form>
    
    <hr class="border-border my-6">
    
    <p class="text-center text-xs text-muted">New here? <a href="<?= SITE_URL ?>/auth/register.php" class="font-bold text-primary hover:underline">Create account</a></p>
    <p class="text-center mt-3"><a href="<?= SITE_URL ?>/index.php" class="inline-flex items-center gap-1.5 text-xs text-muted hover:text-primary transition"><i class="bi bi-arrow-left"></i>Back to Shop</a></p>
</div>
</body>
</html>
