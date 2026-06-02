<?php
$adminTitle = 'Dashboard';
require_once __DIR__ . '/includes/admin_auth.php';
requireAdmin();
require_once __DIR__ . '/includes/admin_header.php';

$totalOrders = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$totalRevenue = $conn->query("SELECT SUM(total_amount) as t FROM orders WHERE status='Delivered'")->fetch_assoc()['t'] ?? 0;
$totalUsers = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$pendingOrders = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='Ordered'")->fetch_assoc()['c'];
$unreadMsgs = $conn->query("SELECT COUNT(*) as c FROM contact_messages WHERE is_read=0")->fetch_assoc()['c'];
$pendingRes = $conn->query("SELECT COUNT(*) as c FROM reservations WHERE status='Pending'")->fetch_assoc()['c'];
$recentOrders = $conn->query("SELECT o.*, u.name as customer FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC LIMIT 8");
$popularFoods = $conn->query("SELECT f.name, f.image, SUM(oi.quantity) as sold FROM order_items oi JOIN foods f ON oi.food_id=f.id GROUP BY oi.food_id ORDER BY sold DESC LIMIT 5");
?>

<div class="row g-3 mb-4">
  <div class="col-md-3 col-6"><div class="stat-card"><div class="icon icon-red"><i class="fas fa-receipt"></i></div><div style="font-size:1.8rem;font-weight:700;font-family:'Playfair Display',serif;"><?= $totalOrders ?></div><div style="color:var(--text-muted);font-size:0.82rem;">Total Orders</div></div></div>
  <div class="col-md-3 col-6"><div class="stat-card"><div class="icon icon-gold"><i class="fas fa-coins"></i></div><div style="font-size:1.8rem;font-weight:700;font-family:'Playfair Display',serif;"><?= number_format($totalRevenue) ?></div><div style="color:var(--text-muted);font-size:0.82rem;">Revenue (PKR)</div></div></div>
  <div class="col-md-3 col-6"><div class="stat-card"><div class="icon icon-blue"><i class="fas fa-users"></i></div><div style="font-size:1.8rem;font-weight:700;font-family:'Playfair Display',serif;"><?= $totalUsers ?></div><div style="color:var(--text-muted);font-size:0.82rem;">Customers</div></div></div>
  <div class="col-md-3 col-6"><div class="stat-card"><div class="icon icon-green"><i class="fas fa-clock"></i></div><div style="font-size:1.8rem;font-weight:700;font-family:'Playfair Display',serif;"><?= $pendingOrders ?></div><div style="color:var(--text-muted);font-size:0.82rem;">Pending Orders</div></div></div>
</div>

<?php if ($unreadMsgs > 0 || $pendingRes > 0): ?>
<div class="d-flex gap-3 mb-4 flex-wrap">
  <?php if ($unreadMsgs > 0): ?><a href="messages.php" class="btn btn-sm" style="background:rgba(243,156,18,0.15);color:var(--accent);border:1px solid rgba(243,156,18,0.3);"><i class="fas fa-envelope me-2"></i><?= $unreadMsgs ?> Unread Messages</a><?php endif; ?>
  <?php if ($pendingRes > 0): ?><a href="reservations.php" class="btn btn-sm" style="background:rgba(52,152,219,0.15);color:#3498DB;border:1px solid rgba(52,152,219,0.3);"><i class="fas fa-calendar me-2"></i><?= $pendingRes ?> Pending Reservations</a><?php endif; ?>
</div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="summary-box">
      <h5 style="font-weight:700;margin-bottom:1.2rem;">Recent Orders</h5>
      <table class="table mb-0">
        <thead><tr><th>Order ID</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th><th></th></tr></thead>
        <tbody>
          <?php while ($o = $recentOrders->fetch_assoc()): ?>
          <?php $sc = match($o['status']){'On Delivery'=>'status-delivery','Delivered'=>'status-delivered','Cancelled'=>'status-cancelled',default=>'status-ordered'}; ?>
          <tr>
            <td style="font-weight:600;">#<?= str_pad($o['id'],5,'0',STR_PAD_LEFT) ?></td>
            <td><?= htmlspecialchars($o['customer']) ?></td>
            <td style="color:var(--accent);font-weight:600;">PKR <?= number_format($o['total_amount']) ?></td>
            <td><span class="status-badge <?= $sc ?>"><?= $o['status'] ?></span></td>
            <td style="color:var(--text-muted);font-size:0.8rem;"><?= date('M d, h:i A', strtotime($o['created_at'])) ?></td>
            <td><a href="orders.php?id=<?= $o['id'] ?>" class="btn btn-sm" style="background:var(--surface2);color:var(--text);border:none;font-size:0.75rem;">Manage</a></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <div class="mt-3"><a href="orders.php" class="btn btn-outline-primary btn-sm">View All Orders</a></div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="summary-box">
      <h5 style="font-weight:700;margin-bottom:1.2rem;">Top Selling Items</h5>
      <?php while ($f = $popularFoods->fetch_assoc()): ?>
      <div class="d-flex align-items-center gap-3 mb-3">
        <img src="<?= htmlspecialchars($f['image']) ?>" style="width:44px;height:44px;border-radius:8px;object-fit:cover;" alt="">
        <div class="flex-grow-1">
          <div style="font-size:0.85rem;font-weight:600;"><?= htmlspecialchars($f['name']) ?></div>
          <div style="font-size:0.75rem;color:var(--text-muted);"><?= $f['sold'] ?> sold</div>
        </div>
        <div style="background:rgba(192,57,43,0.15);color:var(--primary-light);border-radius:50px;padding:3px 10px;font-size:0.75rem;font-weight:700;">#<?= $popularFoods->field_count ?></div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
