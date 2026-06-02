<?php
$adminTitle = 'Settings';
require_once __DIR__ . '/includes/admin_auth.php';
requireAdmin();

$msg = '';
$adminId = (int)($_SESSION['admin_id'] ?? 0);
$admin = ['username' => $_SESSION['admin_username'] ?? 'admin'];

$stmt = $conn->prepare('SELECT username FROM admin WHERE id = ?');
$stmt->bind_param('i', $adminId);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $admin = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($conn, $_POST['username'] ?? $admin['username']);
    $newPassword = trim($_POST['new_password'] ?? '');

    if ($newPassword !== '') {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = $conn->prepare('UPDATE admin SET username = ?, password = ? WHERE id = ?');
        $update->bind_param('ssi', $username, $hash, $adminId);
        $update->execute();
    } else {
        $update = $conn->prepare('UPDATE admin SET username = ? WHERE id = ?');
        $update->bind_param('si', $username, $adminId);
        $update->execute();
    }

    $_SESSION['admin_username'] = $username;
    $admin['username'] = $username;
    $msg = '<div class="alert alert-success">Admin settings updated.</div>';
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<?= $msg ?>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="summary-box">
      <h5 style="font-weight:700;margin-bottom:1rem;">Admin Account</h5>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($admin['username']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">New Password</label>
          <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current password">
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
      </form>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="summary-box">
      <h5 style="font-weight:700;margin-bottom:1rem;">Quick Notes</h5>
      <p style="color:var(--text-muted);margin-bottom:0.6rem;">This page updates the admin login account only.</p>
      <p style="color:var(--text-muted);margin-bottom:0;">If you change the password, use the new one at the admin login page immediately.</p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>