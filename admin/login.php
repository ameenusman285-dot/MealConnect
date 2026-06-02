<?php
require_once __DIR__ . '/includes/admin_auth.php';
// Note: admin_auth includes config and db_connect and session handling
if (isAdmin()) redirect('index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($conn, $_POST['username']);
    $pass = $_POST['password'];
    $admin = $conn->query("SELECT * FROM admin WHERE username='$username'")->fetch_assoc();
    if ($admin && password_verify($pass, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        redirect('index.php');
    } else { $error = 'Invalid credentials.'; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login | MealConnect</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="auth-page" style="align-items: center; padding-left: 35%; ">
  <div class="container" >
    <div class="auth-box">
      <div class="auth-logo">MealConnect</div>
      <p style="text-align:center;color:var(--text-muted);font-size:0.82rem;margin-bottom:0.5rem;letter-spacing:1px;text-transform:uppercase;">Admin Panel Access</p>
      <hr style="border-color:var(--border);margin-bottom:1.5rem;">
      <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
      <form method="POST" style="align-items: center;">
        <div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" class="form-control" required autofocus placeholder="admin"></div>
        <div class="mb-4"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required placeholder="Enter admin password"></div>
        <button type="submit" class="btn btn-primary w-100" style="padding:0.8rem;"><i class="fas fa-lock me-2"></i>Access Admin Panel</button>
      </form>
      <p style="text-align:center;margin-top:1.2rem;"><a href="../index.php" style="font-size:0.82rem;color:var(--text-muted);"><i class="fas fa-arrow-left me-1"></i>Back to website</a></p>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
