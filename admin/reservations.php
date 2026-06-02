<?php
$adminTitle = 'Reservations';
require_once __DIR__ . '/includes/admin_auth.php';
requireAdmin();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'], $_POST['status'])) {
    $id = (int)$_POST['reservation_id'];
    $status = sanitize($conn, $_POST['status']);
    $stmt = $conn->prepare('UPDATE reservations SET status = ? WHERE id = ?');
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();
    $msg = '<div class="alert alert-success">Reservation updated.</div>';
}

$reservations = $conn->query('SELECT * FROM reservations ORDER BY created_at DESC');
require_once __DIR__ . '/includes/admin_header.php';
?>

<?= $msg ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h5 style="font-weight:700;margin:0;">Reservations</h5>
  <small style="color:var(--text-muted);">Total: <?= $reservations->num_rows ?></small>
</div>

<div class="summary-box" style="padding:0;overflow:hidden;">
  <table class="table mb-0">
    <thead>
      <tr><th>Name</th><th>Phone</th><th>Date</th><th>Time</th><th>Guests</th><th>Notes</th><th>Status</th><th>Update</th></tr>
    </thead>
    <tbody>
      <?php while ($r = $reservations->fetch_assoc()): ?>
      <tr>
        <td style="font-weight:600;"><?= htmlspecialchars($r['name']) ?><div style="font-size:0.75rem;color:var(--text-muted);"><?= htmlspecialchars($r['email']) ?></div></td>
        <td><?= htmlspecialchars($r['phone']) ?></td>
        <td><?= htmlspecialchars($r['date']) ?></td>
        <td><?= htmlspecialchars($r['time']) ?></td>
        <td><?= (int)$r['guests'] ?></td>
        <td style="max-width:220px;color:var(--text-muted);font-size:0.82rem;"><?= htmlspecialchars($r['notes']) ?></td>
        <td><span class="status-badge status-ordered"><?= htmlspecialchars($r['status']) ?></span></td>
        <td>
          <form method="POST" class="d-flex gap-1 align-items-center">
            <input type="hidden" name="reservation_id" value="<?= $r['id'] ?>">
            <select name="status" class="form-select form-select-sm" style="min-width:120px;font-size:0.78rem;">
              <?php foreach (['Pending','Confirmed','Cancelled'] as $s): ?>
                <option <?= $r['status']===$s ? 'selected' : '' ?>><?= $s ?></option>
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