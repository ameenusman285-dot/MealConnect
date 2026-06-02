<?php
$pageTitle = 'Menu';
require_once 'includes/header.php';

$catId = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$search = isset($_GET['q']) ? sanitize($conn, $_GET['q']) : '';

$where = "f.is_available=1";
if ($catId > 0) $where .= " AND f.category_id=$catId";
if ($search) $where .= " AND (f.name LIKE '%$search%' OR f.description LIKE '%$search%')";

$foods = $conn->query("SELECT f.*, c.name as cat_name FROM foods f JOIN categories c ON f.category_id=c.id WHERE $where ORDER BY f.is_featured DESC, f.id ASC");
$categories = $conn->query("SELECT * FROM categories");
?>

<div class="page-header">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">Menu</li></ol></nav>
    <h1>Our <span class="title-accent">Menu</span></h1>
    <p>Explore our full collection of handcrafted dishes</p>
  </div>
</div>
<br>
<section class="pb-5">
  <div class="container">
    <!-- Search & Filter -->
    <div class="row g-3 mb-4 align-items-center">
      <div class="col-md-6">
        <form method="GET" class="d-flex gap-2">
          <input type="text" name="q" class="form-control" placeholder="Search dishes..." value="<?= htmlspecialchars($search) ?>">
          <?php if ($catId): ?><input type="hidden" name="cat" value="<?= $catId ?>"><?php endif; ?>
          <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
        </form>
      </div>
      <div class="col-12">
        <div class="categories-row">
          <div class="categories-scroll">
            <a href="menu.php" class="cat-pill <?= !$catId ? 'active' : '' ?>">All</a>
            <?php while ($cat = $categories->fetch_assoc()): ?>
              <a href="menu.php?cat=<?= $cat['id'] ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="cat-pill <?= $catId == $cat['id'] ? 'active' : '' ?>">
                <img src="<?= htmlspecialchars($cat['image']) ?>" alt="">
                <?= htmlspecialchars($cat['name']) ?>
              </a>
            <?php endwhile; ?>
          </div>
        </div>
      </div>
    </div>

    <?php if ($foods->num_rows === 0): ?>
      <div class="text-center py-5">
        <img src="https://images.unsplash.com/photo-1606788075761-26d1f7e6acca?w=200&h=200&fit=crop" style="width:120px;height:120px;border-radius:50%;object-fit:cover;margin-bottom:1.5rem;opacity:0.5;" alt="">
        <h4>No dishes found</h4>
        <p style="color:var(--text-muted);">Try a different search or browse all categories</p>
        <a href="menu.php" class="btn btn-primary mt-2">View All</a>
      </div>
    <?php else: ?>
    <div class="row g-4">
      <?php while ($food = $foods->fetch_assoc()): ?>
      <div class="col-lg-4 col-md-6">
        <div class="card food-card h-100">
          <div style="position:relative;">
            <img src="<?= htmlspecialchars($food['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($food['name']) ?>">
            <?php if ($food['is_featured']): ?>
              <span style="position:absolute;top:10px;left:10px;background:var(--accent);color:#1A1A1A;border-radius:50px;padding:3px 12px;font-size:0.72rem;font-weight:700;letter-spacing:0.5px;">POPULAR</span>
            <?php endif; ?>
          </div>
          <div class="card-body d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h5 class="food-name"><?= htmlspecialchars($food['name']) ?></h5>
              <span class="badge-cat ms-2"><?= htmlspecialchars($food['cat_name']) ?></span>
            </div>
            <p class="food-desc mb-3"><?= htmlspecialchars($food['description']) ?></p>
            <div class="d-flex justify-content-between align-items-center mt-auto">
              <span class="price"><?= CURRENCY ?> <?= number_format($food['price']) ?></span>
              <form action="cart.php" method="POST">
                <input type="hidden" name="food_id" value="<?= $food['id'] ?>">
                <input type="hidden" name="action" value="add">
                <?php if (isLoggedIn()): ?>
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Add to Cart</button>
                <?php else: ?>
                  <a href="login.php" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Add to Cart</a>
                <?php endif; ?>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
