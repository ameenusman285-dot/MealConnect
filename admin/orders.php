<?php
$adminTitle = 'Manage Orders';
require_once __DIR__ . '/includes/admin_auth.php';
requireAdmin();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $oid = (int)$_POST['order_id'];
    $status = sanitize($conn, $_POST['status']);
    $conn->query("UPDATE orders SET status='$status' WHERE id=$oid");
    $msg = '<div class="alert alert-success">Order status updated.</div>';
}

$filter = isset($_GET['status']) ? sanitize($conn, $_GET['status']) : '';
$where = $filter ? "WHERE o.status='$filter'" : '';
$orders = $conn->query("SELECT o.*, u.name as customer, u.phone as cphone FROM orders o JOIN users u ON o.user_id=u.id $where ORDER BY o.created_at DESC");

require_once __DIR__ . '/includes/admin_header.php';
?>

<?= $msg ?>

<div class="d-flex gap-2 mb-4 flex-wrap align-items-center">
  <h5 style="font-weight:700;margin:0;flex-grow:1;">All Orders</h5>
  <a href="orders.php" class="btn btn-sm <?= !$filter?'btn-primary':'btn-outline-primary' ?>">All</a>
  <a href="orders.php?status=Ordered" class="btn btn-sm <?= $filter=='Ordered'?'btn-primary':'btn-outline-primary' ?>">Ordered</a>
  <a href="orders.php?status=On Delivery" class="btn btn-sm <?= $filter=='On Delivery'?'btn-primary':'btn-outline-primary' ?>">On Delivery</a>
  <a href="orders.php?status=Delivered" class="btn btn-sm <?= $filter=='Delivered'?'btn-primary':'btn-outline-primary' ?>">Delivered</a>
  <a href="orders.php?status=Cancelled" class="btn btn-sm <?= $filter=='Cancelled'?'btn-primary':'btn-outline-primary' ?>">Cancelled</a>
</div>

<div class="summary-box" style="padding:0;overflow:hidden;">
  <table class="table mb-0">
    <thead><tr><th>Order ID</th><th>Customer</th><th>Phone</th><th>Address</th><th>Amount</th><th>Status</th><th>Date</th><th>Update</th></tr></thead>
    <tbody>
      <?php while ($o = $orders->fetch_assoc()):
        $sc = match($o['status']){'On Delivery'=>'status-delivery','Delivered'=>'status-delivered','Cancelled'=>'status-cancelled',default=>'status-ordered'};
        $items = $conn->query("SELECT oi.quantity, f.name FROM order_items oi JOIN foods f ON oi.food_id=f.id WHERE oi.order_id={$o['id']}");
        $itemList = [];
        while ($i = $items->fetch_assoc()) $itemList[] = $i['name'] . ' x' . $i['quantity'];
      ?>
      <tr>
        <td style="font-weight:700;">#<?= str_pad($o['id'],5,'0',STR_PAD_LEFT) ?></td>
        <td>
          <div style="font-weight:600;font-size:0.85rem;"><?= htmlspecialchars($o['customer']) ?></div>
          <div style="font-size:0.75rem;color:var(--text-muted);"><?= htmlspecialchars(implode(', ', $itemList)) ?></div>
        </td>
        <td style="font-size:0.82rem;"><?= htmlspecialchars($o['cphone']) ?></td>
        <td style="font-size:0.78rem;color:var(--text-muted);max-width:150px;"><?= htmlspecialchars(substr($o['address'],0,50)) ?>...</td>
        <td style="color:var(--accent);font-weight:600;">PKR <?= number_format($o['total_amount']) ?></td>
        <td><span class="status-badge <?= $sc ?>"><?= $o['status'] ?></span></td>
        <td style="font-size:0.78rem;color:var(--text-muted);"><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
        <td>
          <form method="POST" class="d-flex gap-1 align-items-center">
            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
            <select name="status" class="form-select form-select-sm" style="min-width:120px;font-size:0.78rem;">
              <?php foreach (['Ordered','On Delivery','Delivered','Cancelled'] as $s): ?>
                <option <?= $o['status']==$s?'selected':'' ?>><?= $s ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-sm btn-primary" style="font-size:0.75rem;padding:0.25rem 0.6rem;">Update</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
