<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Checkout';
require_once 'includes/db.php';
require_once 'includes/auth_check.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_items'])) {
    $_SESSION['checkout_item_ids'] = array_map('intval', $_POST['selected_items']);
    unset($_SESSION['pending_order_id'], $_SESSION['pending_amount'], $_SESSION['order_form_submitted']);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    unset($_SESSION['pending_order_id'], $_SESSION['pending_amount'], $_SESSION['order_form_submitted']);
}

$selectedIds = $_SESSION['checkout_item_ids'] ?? [];
if (empty($selectedIds)) {
    $stmt = $conn->prepare("SELECT product_id FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $allCartIds = array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'product_id');
    if (empty($allCartIds)) {
        header("Location: cart.php"); exit();
    }
    $_SESSION['checkout_item_ids'] = $allCartIds;
    $selectedIds = $allCartIds;
}

$placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
$stmt = $conn->prepare("SELECT c.*,p.name,p.price,p.image,p.stock,(c.quantity*p.price) as subtotal FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=? AND c.product_id IN ($placeholders)");
$types = 'i' . str_repeat('i', count($selectedIds));
$bindParams = array_merge([$_SESSION['user_id']], $selectedIds);
$stmt->bind_param($types, ...$bindParams);
$stmt->execute();
$cartItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (empty($cartItems)) { header("Location: cart.php"); exit(); }

$cartTotal  = array_sum(array_column($cartItems,'subtotal'));
$shipping   = $cartTotal >= 2000 ? 0 : 150;
$grandTotal = $cartTotal + $shipping;
$user       = getCurrentUser($conn);
$errors     = [];

$isFormSubmission = ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['full_name']));
if ($isFormSubmission) {
    if (isset($_SESSION['pending_order_id']) && isset($_SESSION['order_form_submitted'])) {
        header("Location: " . SITE_URL . "/payment/esewa_initiate.php"); exit();
    }
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = strtolower(trim($_POST['email'] ?? ''));
    $phone     = trim($_POST['phone'] ?? '');
    $address   = trim($_POST['address'] ?? '');
    $city      = trim($_POST['city'] ?? '');
    $notes     = trim($_POST['notes'] ?? '');
    if (strlen($full_name) < 3) {
        $errors['full_name'] = 'Full name must be at least 3 characters.';
    } elseif (!preg_match('/^[a-zA-Z\s\.\'\-]+$/', $full_name)) {
        $errors['full_name'] = 'Full name must contain letters only.';
    }
    if (empty($email)) {
        $errors['email'] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $errors['email'] = 'Valid email address is required (e.g. name@example.com).';
    }
    if (!preg_match('/^(98|97|96)\d{8}$/',$phone))    $errors['phone']     = 'Valid 10-digit Nepal phone required.';
    if (empty($address))                               $errors['address']   = 'Delivery address is required.';
    if (empty($city))                                  $errors['city']      = 'City is required.';
    if (empty($errors)) {
        $ins = $conn->prepare("INSERT INTO orders (user_id,full_name,email,phone,address,city,total_amount,notes) VALUES (?,?,?,?,?,?,?,?)");
        $ins->bind_param("isssssds",$_SESSION['user_id'],$full_name,$email,$phone,$address,$city,$grandTotal,$notes);
        $ins->execute();
        $orderId = $ins->insert_id;
        foreach ($cartItems as $item) {
            $ins2 = $conn->prepare("INSERT INTO order_items (order_id,product_id,quantity,price) VALUES (?,?,?,?)");
            $ins2->bind_param("iiid",$orderId,$item['product_id'],$item['quantity'],$item['price']);
            $ins2->execute();
        }
        $_SESSION['pending_order_id']     = $orderId;
        $_SESSION['pending_amount']       = $grandTotal;
        $_SESSION['order_form_submitted'] = true;
        header("Location: " . SITE_URL . "/payment/esewa_initiate.php"); exit();
    }
}
include 'includes/header.php';
?>
<main class="flex-grow">
<div class="container py-12">
    <div class="flex items-center gap-3 mb-8">
        <h2 class="font-playfair text-[2rem] font-bold text-dark flex items-center gap-2">
            <i class="bi bi-bag-check"></i>Checkout
        </h2>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- FORM COLUMN -->
        <div class="lg:col-span-7">
            <div class="bg-white border border-border rounded-2xl p-6 shadow-sm">
                <h5 class="font-playfair text-[1.4rem] font-bold text-dark mb-6 border-b border-border pb-3">Delivery Information</h5>
                <form method="POST" id="checkoutForm" onsubmit="var btn=document.getElementById('submitBtn'); btn.disabled=true; btn.innerHTML='Placing Order...';">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" class="w-full px-4 py-2 border rounded-lg bg-white focus:outline-none focus:border-primary <?= isset($errors['full_name'])?'border-red-500 focus:border-red-500':'border-border' ?>" value="<?= htmlspecialchars($_POST['full_name'] ?? $user['full_name']) ?>">
                            <?php if(isset($errors['full_name'])): ?><div class="text-red-600 text-xs font-semibold mt-1"><?= $errors['full_name'] ?></div><?php endif; ?>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Email Address <span class="text-red-500">*</span></label>
                                <input type="email" name="email" class="w-full px-4 py-2 border rounded-lg bg-white focus:outline-none focus:border-primary <?= isset($errors['email'])?'border-red-500 focus:border-red-500':'border-border' ?>" value="<?= htmlspecialchars($_POST['email'] ?? $user['email']) ?>">
                                <?php if(isset($errors['email'])): ?><div class="text-red-600 text-xs font-semibold mt-1"><?= $errors['email'] ?></div><?php endif; ?>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Phone Number <span class="text-red-500">*</span></label>
                                <input type="tel" name="phone" class="w-full px-4 py-2 border rounded-lg bg-white focus:outline-none focus:border-primary <?= isset($errors['phone'])?'border-red-500 focus:border-red-500':'border-border' ?>" placeholder="98XXXXXXXX" value="<?= htmlspecialchars($_POST['phone'] ?? $user['phone']) ?>">
                                <?php if(isset($errors['phone'])): ?><div class="text-red-600 text-xs font-semibold mt-1"><?= $errors['phone'] ?></div><?php endif; ?>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Delivery Address <span class="text-red-500">*</span></label>
                            <input type="text" name="address" class="w-full px-4 py-2 border rounded-lg bg-white focus:outline-none focus:border-primary <?= isset($errors['address'])?'border-red-500 focus:border-red-500':'border-border' ?>" placeholder="Street, Area" value="<?= htmlspecialchars($_POST['address'] ?? $user['address']) ?>">
                            <?php if(isset($errors['address'])): ?><div class="text-red-600 text-xs font-semibold mt-1"><?= $errors['address'] ?></div><?php endif; ?>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">City <span class="text-red-500">*</span></label>
                            <select name="city" class="w-full px-4 py-2 border rounded-lg bg-white focus:outline-none focus:border-primary <?= isset($errors['city'])?'border-red-500 focus:border-red-500':'border-border' ?>">
                                <option value="">Select city...</option>
                                <?php foreach (['Kathmandu','Lalitpur','Bhaktapur','Pokhara','Biratnagar','Birgunj','Butwal','Dharan','Hetauda','Itahari','Janakpur','Nepalgunj'] as $c): ?>
                                <option value="<?= $c ?>" <?= (($_POST['city'] ?? '') === $c) ? 'selected' : '' ?>><?= $c ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if(isset($errors['city'])): ?><div class="text-red-600 text-xs font-semibold mt-1"><?= $errors['city'] ?></div><?php endif; ?>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Order Notes (Optional)</label>
                            <textarea name="notes" class="w-full px-4 py-3 text-sm border border-border rounded-lg bg-white focus:outline-none focus:border-primary" rows="2" placeholder="Special instructions..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-6 space-y-3">
                        <h6 class="block text-xs font-bold text-dark uppercase tracking-wider mb-2">Payment Method</h6>
                        
                        <!-- eSewa -->
                        <div class="border border-border/85 rounded-xl p-4 flex items-center gap-3 bg-cream shadow-sm">
                            <input type="radio" name="payment" value="esewa" checked id="payEsewa" class="w-4 h-4 text-primary focus:ring-primary border-border">
                            <label for="payEsewa" class="flex items-center gap-3 cursor-pointer select-none">
                                <div class="bg-esewa text-white px-3.5 py-1 rounded-md font-bold text-[13px] tracking-wide shadow-sm">eSewa</div>
                                <span class="text-xs text-muted font-medium">Nepal's most popular digital wallet</span>
                            </label>
                        </div>

                        <!-- Khalti -->
                        <div class="border border-border/45 rounded-xl p-4 flex items-center gap-3 bg-white/60 opacity-70 cursor-not-allowed">
                            <input type="radio" name="payment" value="khalti" id="payKhalti" disabled class="w-4 h-4 text-primary focus:ring-primary border-border cursor-not-allowed">
                            <label for="payKhalti" class="flex items-center gap-3 cursor-not-allowed select-none">
                                <div class="bg-[#5C2D91] text-white px-3.5 py-1 rounded-md font-bold text-[13px] tracking-wide shadow-sm">Khalti</div>
                                <span class="text-xs text-muted font-medium">Digital Wallet (Coming Soon)</span>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" id="submitBtn" class="w-full py-3 bg-primary hover:bg-primary-dark text-white rounded-lg font-semibold text-sm transition duration-300 flex items-center justify-center gap-2 mt-6 shadow"><i class="bi bi-lock"></i>Place Order & Pay with eSewa</button>
                </form>
            </div>
        </div>
        
        <!-- SUMMARY COLUMN -->
        <div class="lg:col-span-5">
            <div class="bg-white border border-border rounded-2xl p-6 shadow-sm space-y-4">
                <h5 class="font-playfair text-[1.4rem] font-bold text-dark border-b border-border pb-3">Order Summary</h5>
                <div class="divide-y divide-border/60 max-h-[220px] overflow-y-auto pr-2 space-y-2">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="flex justify-between items-center text-sm py-2">
                        <div class="flex items-center gap-2">
                            <span class="bg-gray-100 text-dark text-xs font-bold px-2 py-0.5 rounded"><?= $item['quantity'] ?>x</span>
                            <span class="text-dark/90 font-medium truncate max-w-[200px]"><?= htmlspecialchars($item['name']) ?></span>
                        </div>
                        <span class="font-semibold text-dark">NPR <?= number_format($item['subtotal'],2) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <hr class="border-border">
                
                <div class="flex justify-between text-sm py-1">
                    <span class="text-muted">Subtotal</span><span class="font-semibold text-dark">NPR <?= number_format($cartTotal,2) ?></span>
                </div>
                <div class="flex justify-between text-sm py-1">
                    <span class="text-muted">Shipping</span>
                    <?php if ($shipping===0): ?><span class="text-green-600 font-bold">FREE</span>
                    <?php else: ?><span class="font-semibold text-dark">NPR <?= number_format($shipping,2) ?></span><?php endif; ?>
                </div>
                
                <hr class="border-border">
                
                <div class="flex justify-between items-center py-2">
                    <span class="font-bold text-dark">Grand Total</span>
                    <strong class="text-primary text-[1.3rem] font-bold">NPR <?= number_format($grandTotal,2) ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>
</main>
<?php include 'includes/footer.php'; ?>
