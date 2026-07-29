<?php
function isLoggedIn() { return isset($_SESSION['user_id']); }
function isAdmin()    { return isset($_SESSION['role']) && $_SESSION['role'] === 'admin'; }
function isCustomer() { return isset($_SESSION['role']) && $_SESSION['role'] === 'customer'; }
function requireLogin() {
    if (!isLoggedIn()) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $_SESSION['redirect_after_login'] = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header("Location: " . SITE_URL . "/auth/login.php"); exit();
    }
}
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) { header("Location: " . SITE_URL . "/index.php"); exit(); }
}
function getCurrentUser($conn) {
    if (!isLoggedIn()) return null;
    $id = $_SESSION['user_id'];
    $s  = $conn->prepare("SELECT * FROM users WHERE id=?");
    $s->bind_param("i", $id); $s->execute();
    return $s->get_result()->fetch_assoc();
}
function getCartCount($conn) {
    if (!isLoggedIn()) return 0;
    $id = $_SESSION['user_id'];
    $s  = $conn->prepare("SELECT COALESCE(SUM(quantity),0) as total FROM cart WHERE user_id=?");
    $s->bind_param("i", $id); $s->execute();
    return (int)$s->get_result()->fetch_assoc()['total'];
}
