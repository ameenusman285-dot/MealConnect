<?php
require_once __DIR__ . '/includes/admin_auth.php';
requireAdmin();

$adminTitle = 'Contact Messages';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare('DELETE FROM contact_messages WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $msg = '<div class="alert alert-success">Message deleted.</div>';
    } elseif (isset($_POST['action']) && $_POST['action'] === 'toggle_read' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $is_read = isset($_POST['is_read']) && $_POST['is_read'] == '1' ? 1 : 0;
        $stmt = $conn->prepare('UPDATE contact_messages SET is_read = ? WHERE id = ?');
        $stmt->bind_param('ii', $is_read, $id);
        $stmt->execute();
        $msg = '<div class="alert alert-success">Message updated.</div>';
    }
}

$res = $conn->query('SELECT * FROM contact_messages ORDER BY created_at DESC');

?>
<?php require_once __DIR__ . '/includes/admin_header.php'; ?>

<?= $msg ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 style="font-weight:700;margin:0;">Contact Messages</h5>
  <small style="color:var(--text-muted);">Total: <?= $res->num_rows ?></small>
</div>

<div class="list-group">
  <?php while ($row = $res->fetch_assoc()): ?>
    <div class="list-group-item" style="background:<?= $row['is_read'] ? 'transparent' : 'rgba(52,152,219,0.06)' ?>;">
      <div class="d-flex w-100 justify-content-between">
        <h6 class="mb-1" style="font-weight:600;"><?= htmlspecialchars($row['subject'] ?: '(No Subject)') ?></h6>
        <small style="color:var(--text-muted);"><?= htmlspecialchars($row['created_at']) ?></small>
      </div>
      <p class="mb-1"><?= nl2br(htmlspecialchars($row['message'])) ?></p>
      <small style="color:var(--text-muted);">From: <?= htmlspecialchars($row['name']) ?> &lt;<?= htmlspecialchars($row['email']) ?>&gt;</small>
      <div style="margin-top:0.75rem;display:flex;gap:8px;">
        <form method="POST" style="display:inline-block;">
          <input type="hidden" name="action" value="toggle_read">
          <input type="hidden" name="id" value="<?= $row['id'] ?>">
          <input type="hidden" name="is_read" value="<?= $row['is_read'] ? '0' : '1' ?>">
          <button type="submit" class="btn btn-sm btn-outline-primary"><?= $row['is_read'] ? 'Mark Unread' : 'Mark Read' ?></button>
        </form>

        <form method="POST" onsubmit="return confirm('Delete this message?');" style="display:inline-block;">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= $row['id'] ?>">
          <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
