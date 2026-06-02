<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= isset($adminTitle) ? $adminTitle . ' | Admin' : 'Admin Panel' ?> — MealConnect</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-panel">

<div class="dash-sidebar">
  <a class="dash-logo" href="index.php">MealConnect<br><small style="font-size:0.6rem;font-weight:400;color:var(--text-muted);letter-spacing:1px;">ADMIN PANEL</small></a>
  
  <?php $ap = basename($_SERVER['PHP_SELF']); ?>
  <a href="index.php" class="dash-nav-link <?= $ap=='index.php'?'active':'' ?>"><i class="fas fa-chart-line" style="width:18px;"></i> Dashboard</a>
  <a href="foods.php" class="dash-nav-link <?= $ap=='foods.php'?'active':'' ?>"><i class="fas fa-hamburger" style="width:18px;"></i> Foods</a>
  <a href="categories.php" class="dash-nav-link <?= $ap=='categories.php'?'active':'' ?>"><i class="fas fa-th-large" style="width:18px;"></i> Categories</a>
  <a href="orders.php" class="dash-nav-link <?= $ap=='orders.php'?'active':'' ?>"><i class="fas fa-receipt" style="width:18px;"></i> Orders</a>
  <a href="reservations.php" class="dash-nav-link <?= $ap=='reservations.php'?'active':'' ?>"><i class="fas fa-calendar-check" style="width:18px;"></i> Reservations</a>
  <a href="offers.php" class="dash-nav-link <?= $ap=='offers.php'?'active':'' ?>"><i class="fas fa-percent" style="width:18px;"></i> Special Offers</a>
  <a href="messages.php" class="dash-nav-link <?= $ap=='messages.php'?'active':'' ?>"><i class="fas fa-envelope" style="width:18px;"></i> Messages</a>
  <a href="settings.php" class="dash-nav-link <?= $ap=='settings.php'?'active':'' ?>"><i class="fas fa-cog" style="width:18px;"></i> Settings</a>
  
  <div style="margin-top:auto;padding:1rem 1.5rem;border-top:1px solid var(--border);">
    <a href="../index.php" class="dash-nav-link" style="padding:0.5rem 0;border:none;"><i class="fas fa-external-link-alt" style="width:18px;"></i> View Site</a>
    <a href="logout.php" class="dash-nav-link" style="padding:0.5rem 0;border:none;color:#E74C3C;"><i class="fas fa-sign-out-alt" style="width:18px;"></i> Logout</a>
  </div>
</div>

<div class="dash-content">
<div class="admin-topbar">
  <span class="page-title"><?= isset($adminTitle) ? $adminTitle : 'Dashboard' ?></span>
  <div style="display:flex;align-items:center;gap:12px;">
    <span style="font-size:0.82rem;color:var(--text-muted);"><i class="fas fa-user-shield me-1" style="color:var(--primary-light);"></i>Admin</span>
  </div>
</div>
<div style="padding:1.5rem;">
