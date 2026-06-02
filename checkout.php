<?php
$pageTitle = 'Checkout';
require_once 'includes/db_connect.php';
if (!isLoggedIn()) { redirect('login.php'); }

$userId = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$userId")->fetch_assoc();
$cartItems = $conn->query("SELECT c.*, f.name, f.price, f.image FROM cart c JOIN foods f ON c.food_id=f.id WHERE c.user_id=$userId");
$items = [];
$subtotal = 0;
while ($item = $cartItems->fetch_assoc()) { $items[] = $item; $subtotal += $item['price'] * $item['quantity']; }

if (empty($items)) { redirect('cart.php'); }
$delivery = 99;
$total = $subtotal + $delivery;
$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = sanitize($conn, $_POST['address']);
    $phone = sanitize($conn, $_POST['phone']);
    $notes = sanitize($conn, $_POST['notes'] ?? '');
    if (!$address || !$phone) { $error = 'Please fill in all required fields.'; }
    else {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, delivery_fee, address, phone, notes) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param('iddsss', $userId, $total, $delivery, $address, $phone, $notes);
        $stmt->execute();
        $orderId = $stmt->insert_id;
        foreach ($items as $item) {
            $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, food_id, quantity, unit_price) VALUES (?,?,?,?)");
            $stmt2->bind_param('iiid', $orderId, $item['food_id'], $item['quantity'], $item['price']);
            $stmt2->execute();
        }
        $conn->query("DELETE FROM cart WHERE user_id=$userId");
        redirect('dashboard.php?order_success=1');
    }
}

require_once 'includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item"><a href="cart.php">Cart</a></li><li class="breadcrumb-item active">Checkout</li></ol></nav>
    <h1>Complete Your <span class="title-accent">Order</span></h1>
  </div>
</div>

<section class="pb-5">
  <div class="container">
    <?php if ($error): ?><div class="alert alert-danger mb-4"><?= $error ?></div><?php endif; ?>
    <div class="row g-4">
      <div class="col-lg-7">
        <div class="summary-box">
          <h5 style="font-weight:700;margin-bottom:1.5rem;">Delivery Information</h5>
          <form method="POST">
            <div class="mb-3">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" readonly style="opacity:0.7;">
            </div>
            <div class="mb-3">
              <label class="form-label">Phone Number <span style="color:var(--primary);">*</span></label>
              <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+92 300 0000000" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Delivery Address <span style="color:var(--primary);">*</span></label>
              <textarea name="address" class="form-control" rows="3" placeholder="House/Flat no., Street, Area, City" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
            </div>
            <div class="mb-4">
              <label class="form-label">Special Instructions (optional)</label>
              <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Extra spicy, no onions..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100" style="padding:0.85rem;font-size:1rem;"><i class="fas fa-check-circle me-2"></i>Place Order — <?= CURRENCY ?> <?= number_format($total) ?></button>
          </form>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="summary-box">
          <h5 style="font-weight:700;margin-bottom:1.5rem;">Order Review</h5>
          <?php foreach ($items as $item): ?>
          <div class="d-flex align-items-center gap-3 mb-3" style="border-bottom:1px solid var(--border);padding-bottom:0.8rem;">
            <img src="<?= htmlspecialchars($item['image']) ?>" style="width:50px;height:50px;border-radius:8px;object-fit:cover;" alt="">
            <div class="flex-grow-1">
              <div style="font-weight:600;font-size:0.88rem;"><?= htmlspecialchars($item['name']) ?></div>
              <div style="font-size:0.78rem;color:var(--text-muted);">x<?= $item['quantity'] ?></div>
            </div>
            <div style="font-weight:600;color:var(--accent);font-size:0.9rem;"><?= CURRENCY ?> <?= number_format($item['price'] * $item['quantity']) ?></div>
          </div>
          <?php endforeach; ?>
          <div class="summary-row mt-2"><span style="color:var(--text-muted);">Subtotal</span><span><?= CURRENCY ?> <?= number_format($subtotal) ?></span></div>
          <div class="summary-row"><span style="color:var(--text-muted);">Delivery</span><span><?= CURRENCY ?> <?= number_format($delivery) ?></span></div>
          <div class="summary-row total"><span>Total</span><span><?= CURRENCY ?> <?= number_format($total) ?></span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
