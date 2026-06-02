<?php
$pageTitle = 'Sign In';
require_once 'includes/db_connect.php';
if (isLoggedIn()) redirect('dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($conn, $_POST['email']);
    $pass = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        redirect('dashboard.php');
    } else { $error = 'Invalid email or password.'; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sign In | MealConnect</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="auth-page"  style="align-items: center; center; padding-left: 35%;">
  <div class="container" align-items: center>
    <div class="auth-box">
      <div class="auth-logo">MealConnect</div>
      <p style="text-align:center;color:var(--text-muted);font-size:0.85rem;margin-bottom:1.5rem;">Welcome back — sign in to your account</p>
      <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
      <form method="POST" align-items="center">
        <div class="mb-3"><label class="form-label">Email Address</label><input type="email" name="email" class="form-control" required placeholder="you@email.com"></div>
        <div class="mb-4"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required placeholder="Enter password"></div>
        <button type="submit" class="btn btn-primary w-100" style="padding:0.8rem;">Sign In</button>
      </form>
      <p style="text-align:center;margin-top:1.2rem;font-size:0.85rem;color:var(--text-muted);">No account yet? <a href="register.php" style="color:var(--primary-light);">Register here</a></p>
      <p style="text-align:center;margin-top:0.5rem;font-size:0.82rem;"><a href="index.php" style="color:var(--text-muted);"><i class="fas fa-arrow-left me-1"></i>Back to Home</a></p>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
