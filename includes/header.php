<?php
require_once __DIR__ . '/db_connect.php';
$cartCount = getCartCount($conn);
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? $pageTitle . ' | ' . SITE_NAME : SITE_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?php
$depth = substr_count(str_replace(SITE_URL, '', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']), '/');
$prefix = str_repeat('../', max(0, $depth - 1));
?>
<link rel="stylesheet" href="<?= $prefix ?>css/style.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍔</text></svg>">
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="<?= $prefix ?>index.php">MealConnect</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto align-items-center gap-1">
        <li class="nav-item"><a class="nav-link <?= $currentPage=='index.php'?'active':'' ?>" href="<?= $prefix ?>index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link <?= $currentPage=='menu.php'?'active':'' ?>" href="<?= $prefix ?>menu.php">Menu</a></li>
        <li class="nav-item"><a class="nav-link <?= $currentPage=='contact.php'?'active':'' ?>" href="<?= $prefix ?>contact.php">Contact</a></li>
        <?php if (isLoggedIn()): ?>
          <li class="nav-item"><a class="nav-link <?= $currentPage=='dashboard.php'?'active':'' ?>" href="<?= $prefix ?>dashboard.php">My Orders</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= $prefix ?>cart.php">
            <i class="fas fa-shopping-cart"></i>
            <?php if ($cartCount > 0): ?><span class="cart-badge"><?= $cartCount ?></span><?php endif; ?>
          </a></li>
          <li class="nav-item"><a class="nav-link" href="<?= $prefix ?>logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= $prefix ?>cart.php"><i class="fas fa-shopping-cart"></i></a></li>
          <li class="nav-item ms-1"><a class="btn btn-primary btn-sm" href="<?= $prefix ?>login.php">Sign In</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
