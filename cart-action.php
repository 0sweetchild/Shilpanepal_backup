<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/db.php';
require_once 'includes/auth_check.php';
ob_clean();
header('Content-Type: application/json');
if (!isLoggedIn()) { echo json_encode(['success'=>false,'redirect'=>SITE_URL.'/auth/login.php']); exit(); }
$action     = $_POST['action'] ?? '';
$product_id = intval($_POST['product_id'] ?? 0);
$user_id    = $_SESSION['user_id'];
function cartCount($conn,$uid){ $s=$conn->prepare("SELECT COALESCE(SUM(quantity),0) as t FROM cart WHERE user_id=?"); $s->bind_param("i",$uid); $s->execute(); return (int)$s->get_result()->fetch_assoc()['t']; }
function cartTotal($conn,$uid){ $s=$conn->prepare("SELECT COALESCE(SUM(c.quantity*p.price),0) as t FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=?"); $s->bind_param("i",$uid); $s->execute(); return (float)$s->get_result()->fetch_assoc()['t']; }
if ($action==='add') {
    $s=$conn->prepare("SELECT stock FROM products WHERE id=?"); $s->bind_param("i",$product_id); $s->execute();
    $p=$s->get_result()->fetch_assoc();
    if (!$p||$p['stock']<1){ echo json_encode(['success'=>false,'message'=>'Out of stock.']); exit(); }
    $s=$conn->prepare("INSERT INTO cart (user_id,product_id,quantity) VALUES (?,?,1) ON DUPLICATE KEY UPDATE quantity=quantity+1");
    $s->bind_param("ii",$user_id,$product_id); $s->execute();
    echo json_encode(['success'=>true,'cart_count'=>cartCount($conn,$user_id)]);
} elseif ($action==='update') {
    $qty=intval($_POST['quantity']??1);
    $s=$conn->prepare("UPDATE cart SET quantity=? WHERE user_id=? AND product_id=?"); $s->bind_param("iii",$qty,$user_id,$product_id); $s->execute();
    $s2=$conn->prepare("SELECT p.price FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=? AND c.product_id=?"); $s2->bind_param("ii",$user_id,$product_id); $s2->execute();
    $price=(float)$s2->get_result()->fetch_assoc()['price'];
    echo json_encode(['success'=>true,'item_total'=>$price*$qty,'cart_total'=>cartTotal($conn,$user_id),'cart_count'=>cartCount($conn,$user_id)]);
} elseif ($action==='remove') {
    $s=$conn->prepare("DELETE FROM cart WHERE user_id=? AND product_id=?"); $s->bind_param("ii",$user_id,$product_id); $s->execute();
    echo json_encode(['success'=>true,'cart_total'=>cartTotal($conn,$user_id),'cart_count'=>cartCount($conn,$user_id)]);
} else { echo json_encode(['success'=>false,'message'=>'Invalid action.']); }
