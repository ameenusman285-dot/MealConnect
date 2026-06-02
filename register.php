<?php
$pageTitle = 'Register';
require_once 'includes/db_connect.php';
if (isLoggedIn()) redirect('dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($conn, $_POST['name']);
    $email = sanitize($conn, $_POST['email']);
    $phone = sanitize($conn, $_POST['phone']);
    $pass = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    if (!$name || !$email || !$pass) { $error = 'All fields are required.'; }
    elseif ($pass !== $confirm) { $error = 'Passwords do not match.'; }
    elseif (strlen($pass) < 6) { $error = 'Password must be at least 6 characters.'; }
    else {
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param('s', $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) { $error = 'Email already registered.'; }
        else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?,?,?,?)");
            $stmt->bind_param('ssss', $name, $email, $phone, $hashed);
            $stmt->execute();
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_name'] = $name;
            redirect('dashboard.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Register | MealConnect</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="auth-page" style="align-items: center center; padding-left: 35%;">
  <div class="container">
    <div class="auth-box">
      <div class="auth-logo">MealConnect</div>
      <p style="text-align:center;color:var(--text-muted);font-size:0.85rem;margin-bottom:1.5rem;">Create your account and start ordering</p>
      <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
      <form method="POST" >
        <div class="mb-3"><label class="form-label">Full Name</label><input type="text" name="name" class="form-control" required placeholder="Your full name"></div>
        <div class="mb-3"><label class="form-label">Email Address</label><input type="email" name="email" class="form-control" required placeholder="you@email.com"></div>
        <div class="mb-3"><label class="form-label">Phone Number</label><input type="tel" name="phone" class="form-control" placeholder="+92 300 0000000"></div>
        <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required placeholder="Min. 6 characters"></div>
        <div class="mb-4"><label class="form-label">Confirm Password</label><input type="password" name="confirm_password" class="form-control" required placeholder="Repeat password"></div>
        <button type="submit" class="btn btn-primary w-100" style="padding:0.8rem;">Create Account</button>
      </form>
      <p style="text-align:center;margin-top:1.2rem;font-size:0.85rem;color:var(--text-muted);">Already have an account? <a href="login.php" style="color:var(--primary-light);">Sign in</a></p>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
