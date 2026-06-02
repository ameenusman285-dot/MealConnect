<?php
$pageTitle = 'Cart';
require_once 'includes/db_connect.php';

// Handle cart actions before header
if (!isLoggedIn()) { redirect('login.php'); }

$userId = $_SESSION['user_id'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $fid = (int)$_POST['food_id'];
        $stmt = $conn->prepare("INSERT INTO cart (user_id, food_id, quantity) VALUES (?,?,1) ON DUPLICATE KEY UPDATE quantity=quantity+1");
        $stmt->bind_param('ii', $userId, $fid);
        $stmt->execute();
        redirect('cart.php');
    } elseif ($action === 'update') {
        $cid = (int)$_POST['cart_id'];
        $qty = (int)$_POST['quantity'];
        if ($qty < 1) {
            $conn->query("DELETE FROM cart WHERE id=$cid AND user_id=$userId");
        } else {
            $conn->query("UPDATE cart SET quantity=$qty WHERE id=$cid AND user_id=$userId");
        }
        redirect('cart.php');
    } elseif ($action === 'remove') {
        $cid = (int)$_POST['cart_id'];
        $conn->query("DELETE FROM cart WHERE id=$cid AND user_id=$userId");
        redirect('cart.php');
    } elseif ($action === 'clear') {
        $conn->query("DELETE FROM cart WHERE user_id=$userId");
        redirect('cart.php');
    }
}

$cartItems = $conn->query("SELECT c.*, f.name, f.price, f.image, f.description FROM cart c JOIN foods f ON c.food_id=f.id WHERE c.user_id=$userId");
$subtotal = 0;
$items = [];
while ($item = $cartItems->fetch_assoc()) { $items[] = $item; $subtotal += $item['price'] * $item['quantity']; }
$delivery = $subtotal > 0 ? 99 : 0;
$total = $subtotal + $delivery;

require_once 'includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">Cart</li></ol></nav>
    <h1>Your <span class="title-accent">Cart</span></h1>
  </div>
</div>

<section class="pb-5">
  <div class="container">
    <?php if (empty($items)): ?>
      <div class="text-center py-5">
        <img src="https://images.unsplash.com/photo-1491553895911-0055eca6402d?w=200&h=200&fit=crop" style="width:130px;height:130px;border-radius:50%;object-fit:cover;margin-bottom:1.5rem;opacity:0.5;" alt="">
        <h4>Your cart is empty</h4>
        <p style="color:var(--text-muted);">Looks like you haven't added anything yet.</p>
        <a href="menu.php" class="btn btn-primary mt-2">Browse Menu</a>
      </div>
    <?php else: ?>
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="cart-table">
          <table class="table mb-0">
            <thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr></thead>
            <tbody>
              <?php foreach ($items as $item): ?>
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-3">
                    <img src="<?= htmlspecialchars($item['image']) ?>" style="width:60px;height:60px;border-radius:8px;object-fit:cover;" alt="">
                    <div>
                      <div style="font-weight:600;font-size:0.9rem;"><?= htmlspecialchars($item['name']) ?></div>
                      <div style="font-size:0.78rem;color:var(--text-muted);"><?= substr(htmlspecialchars($item['description']),0,50) ?>...</div>
                    </div>
                  </div>
                </td>
                <td style="color:var(--accent);font-weight:600;"><?= CURRENCY ?> <?= number_format($item['price']) ?></td>
                <td>
                  <form method="POST" class="d-flex align-items-center gap-1">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                    <button type="submit" name="quantity" value="<?= $item['quantity']-1 ?>" class="btn btn-sm" style="background:var(--surface2);color:var(--text);border:none;width:28px;height:28px;padding:0;">-</button>
                    <span style="min-width:24px;text-align:center;font-weight:600;"><?= $item['quantity'] ?></span>
                    <button type="submit" name="quantity" value="<?= $item['quantity']+1 ?>" class="btn btn-sm" style="background:var(--surface2);color:var(--text);border:none;width:28px;height:28px;padding:0;">+</button>
                  </form>
                </td>
                <td style="font-weight:600;"><?= CURRENCY ?> <?= number_format($item['price'] * $item['quantity']) ?></td>
                <td>
                  <form method="POST">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                    <button type="submit" class="btn btn-sm" style="background:rgba(192,57,43,0.15);color:var(--primary-light);border:none;width:32px;height:32px;padding:0;"><i class="fas fa-trash-alt" style="font-size:0.8rem;"></i></button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="d-flex gap-2 mt-3">
          <a href="menu.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-arrow-left me-2"></i>Continue Shopping</a>
          <form method="POST"><input type="hidden" name="action" value="clear"><button type="submit" class="btn btn-sm" style="background:rgba(192,57,43,0.15);color:var(--primary-light);border:1px solid rgba(192,57,43,0.3);">Clear Cart</button></form>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="summary-box">
          <h5 style="font-weight:700;margin-bottom:1.5rem;">Order Summary</h5>
          <div class="summary-row"><span style="color:var(--text-muted);">Subtotal</span><span><?= CURRENCY ?> <?= number_format($subtotal) ?></span></div>
          <div class="summary-row"><span style="color:var(--text-muted);">Delivery Fee</span><span><?= CURRENCY ?> <?= number_format($delivery) ?></span></div>
          <div class="summary-row total"><span>Total</span><span><?= CURRENCY ?> <?= number_format($total) ?></span></div>
          <a href="checkout.php" class="btn btn-primary w-100 mt-3" style="padding:0.8rem;"><i class="fas fa-credit-card me-2"></i>Proceed to Checkout</a>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
