<?php
$pageTitle = 'My Dashboard';
require_once 'includes/header.php';
if (!isLoggedIn()) redirect('login.php');

$userId = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$userId")->fetch_assoc();
$orders = $conn->query("SELECT * FROM orders WHERE user_id=$userId ORDER BY created_at DESC");
$totalOrders = $conn->query("SELECT COUNT(*) as c FROM orders WHERE user_id=$userId")->fetch_assoc()['c'];
$totalSpent = $conn->query("SELECT SUM(total_amount) as t FROM orders WHERE user_id=$userId")->fetch_assoc()['t'] ?? 0;
$success = isset($_GET['order_success']);
?>

<div class="page-header">
  <div class="container">
    <h1>Welcome, <span class="title-accent"><?= htmlspecialchars($user['name']) ?></span></h1>
    <p>Manage your orders and account details</p>
  </div>
</div>
<br>
<section class="pb-5">
  <div class="container">
    <?php if ($success): ?><div class="alert alert-success mb-4"><i class="fas fa-check-circle me-2"></i>Your order has been placed successfully! We're preparing it now.</div><?php endif; ?>
    <div class="row g-3 mb-4">
      <div class="col-md-3 col-6"><div class="stat-card"><div class="icon icon-red"><i class="fas fa-shopping-bag"></i></div><div style="font-size:1.5rem;font-weight:700;font-family:'Playfair Display',serif;"><?= $totalOrders ?></div><div style="color:var(--text-muted);font-size:0.82rem;">Total Orders</div></div></div>
      <div class="col-md-3 col-6"><div class="stat-card"><div class="icon icon-gold"><i class="fas fa-rupee-sign"></i></div><div style="font-size:1.5rem;font-weight:700;font-family:'Playfair Display',serif;"><?= number_format($totalSpent) ?></div><div style="color:var(--text-muted);font-size:0.82rem;">Total Spent (PKR)</div></div></div>
      <div class="col-md-3 col-6"><div class="stat-card"><div class="icon icon-green"><i class="fas fa-star"></i></div><div style="font-size:1.5rem;font-weight:700;font-family:'Playfair Display',serif;">Gold</div><div style="color:var(--text-muted);font-size:0.82rem;">Member Status</div></div></div>
      <div class="col-md-3 col-6"><div class="stat-card"><div class="icon icon-blue"><a href="menu.php" class="btn btn-primary btn-sm mt-1">Order Again</a></div></div></div>
    </div>

    <h4 style="font-weight:700;margin-bottom:1.2rem;">Order History</h4>
    <?php if ($orders->num_rows === 0): ?>
      <div class="text-center py-4" style="color:var(--text-muted);">
        <p>You haven't placed any orders yet.</p>
        <a href="menu.php" class="btn btn-primary">Start Ordering</a>
      </div>
    <?php else: ?>
      <?php while ($order = $orders->fetch_assoc()): ?>
      <?php
        $statusClass = match($order['status']) {
          'On Delivery' => 'status-delivery',
          'Delivered' => 'status-delivered',
          'Cancelled' => 'status-cancelled',
          default => 'status-ordered'
        };
        $orderItems = $conn->query("SELECT oi.*, f.name, f.image FROM order_items oi JOIN foods f ON oi.food_id=f.id WHERE oi.order_id={$order['id']}");
      ?>
      <div class="summary-box mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
          <div>
            <span style="font-weight:700;">Order #<?= str_pad($order['id'],5,'0',STR_PAD_LEFT) ?></span>
            <span style="color:var(--text-muted);font-size:0.82rem;margin-left:1rem;"><?= date('M d, Y — h:i A', strtotime($order['created_at'])) ?></span>
          </div>
          <span class="status-badge <?= $statusClass ?>"><?= $order['status'] ?></span>
        </div>
        <div class="d-flex flex-wrap gap-2 mb-3">
          <?php while ($oi = $orderItems->fetch_assoc()): ?>
            <div class="d-flex align-items-center gap-2" style="background:var(--dark3);border-radius:8px;padding:6px 10px;">
              <img src="<?= htmlspecialchars($oi['image']) ?>" style="width:36px;height:36px;border-radius:6px;object-fit:cover;" alt="">
              <span style="font-size:0.82rem;"><?= htmlspecialchars($oi['name']) ?> x<?= $oi['quantity'] ?></span>
            </div>
          <?php endwhile; ?>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <span style="color:var(--text-muted);font-size:0.85rem;"><i class="fas fa-map-marker-alt me-1" style="color:var(--primary);"></i><?= htmlspecialchars(substr($order['address'],0,60)) ?>...</span>
          <span style="font-weight:700;color:var(--accent);"><?= CURRENCY ?> <?= number_format($order['total_amount']) ?></span>
        </div>
      </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
