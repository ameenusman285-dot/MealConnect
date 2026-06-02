<?php
$pageTitle = 'Home';
require_once 'includes/header.php';

// Fetch featured foods
$featured = $conn->query("SELECT f.*, c.name as cat_name FROM foods f JOIN categories c ON f.category_id=c.id WHERE f.is_featured=1 AND f.is_available=1 LIMIT 6");
$categories = $conn->query("SELECT * FROM categories LIMIT 6");
$offers = $conn->query("SELECT * FROM special_offers WHERE is_active=1 AND (expires_at IS NULL OR expires_at >= CURDATE()) LIMIT 3");
?>

<!-- HERO -->
<section class="hero">
  <div class="container position-relative">
    <div class="row align-items-center">
      <div class="col-lg-6">
        <div class="fade-up">
          <div class="hero-badge"><i class="fas fa-fire"></i> Premium Quality Food</div>
          <h1 class="hero-title">Taste the <span class="highlight">Fusion</span> of Bold Flavors</h1>
          <p class="hero-subtitle">Handcrafted burgers, artisan pizza, and crispy fried chicken — delivered fresh to your door in 30 minutes or less.</p>
          <div class="d-flex gap-3 flex-wrap">
            <a href="menu.php" class="btn btn-accent btn-lg"><i class="fas fa-utensils me-2"></i>Explore Menu</a>
            <a href="menu.php" class="btn btn-outline-primary btn-lg">View Offers</a>
          </div>
        </div>
      </div>
      <div class="col-lg-6 fade-up delay-2">
        <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=700&h=420&fit=crop" alt="Featured Burger" class="hero-img">
      </div>
    </div>
  </div>
</section>

<!-- STATS -->
<div class="stats-bar">
  <div class="container">
    <div class="row text-center g-3">
      <div class="col-6 col-md-3"><div class="stat-item"><div class="stat-number">50+</div><div class="stat-label">MENU ITEMS</div></div></div>
      <div class="col-6 col-md-3"><div class="stat-item"><div class="stat-number">4.9</div><div class="stat-label">STAR RATING</div></div></div>
      <div class="col-6 col-md-3"><div class="stat-item"><div class="stat-number">30min</div><div class="stat-label">AVG DELIVERY</div></div></div>
      <div class="col-6 col-md-3"><div class="stat-item"><div class="stat-number">10K+</div><div class="stat-label">HAPPY CUSTOMERS</div></div></div>
    </div>
  </div>
</div>

<!-- CATEGORIES -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-4">
      <h2 class="section-title">Browse <span class="title-accent">Categories</span></h2>
      <p class="section-sub">Find exactly what you're craving</p>
    </div>
    <div class="d-flex flex-wrap gap-2 justify-content-center">
      <a href="menu.php" class="cat-pill active">All Items</a>
      <?php while ($cat = $categories->fetch_assoc()): ?>
        <a href="menu.php?cat=<?= $cat['id'] ?>" class="cat-pill">
          <img src="<?= htmlspecialchars($cat['image']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>">
          <?= htmlspecialchars($cat['name']) ?>
        </a>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<!-- FEATURED FOODS -->
<section class="py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <h2 class="section-title">Fan <span class="title-accent">Favourites</span></h2>
        <div class="divider"></div>
        <p class="section-sub mb-0">Our most-loved dishes, ordered again and again</p>
      </div>
      <a href="menu.php" class="btn btn-outline-primary btn-sm">View All</a>
    </div>
    <div class="row g-4">
      <?php while ($food = $featured->fetch_assoc()): ?>
      <div class="col-lg-4 col-md-6">
        <div class="card food-card h-100">
          <img src="<?= htmlspecialchars($food['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($food['name']) ?>">
          <div class="card-body d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start mb-1">
              <h5 class="food-name"><?= htmlspecialchars($food['name']) ?></h5>
              <span class="badge-cat ms-2"><?= htmlspecialchars($food['cat_name']) ?></span>
            </div>
            <p class="food-desc mb-3"><?= htmlspecialchars($food['description']) ?></p>
            <div class="d-flex justify-content-between align-items-center mt-auto">
              <span class="price"><?= CURRENCY ?> <?= number_format($food['price']) ?></span>
              <form action="cart.php" method="POST">
                <input type="hidden" name="food_id" value="<?= $food['id'] ?>">
                <input type="hidden" name="action" value="add">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Add to Cart</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<!-- SPECIAL OFFERS -->
<?php if ($offers && $offers->num_rows > 0): ?>
<section class="py-5" style="background:var(--dark2)">
  <div class="container">
    <div class="text-center mb-4">
      <h2 class="section-title">Special <span class="title-accent">Offers</span></h2>
      <div class="divider mx-auto"></div>
      <p class="section-sub">Limited-time deals you don't want to miss</p>
    </div>
    <div class="row g-4">
      <?php while ($offer = $offers->fetch_assoc()): ?>
      <div class="col-lg-4">
        <div class="card h-100" style="border-color:rgba(192,57,43,0.2);">
          <div style="position:relative;">
            <img src="<?= htmlspecialchars($offer['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($offer['title']) ?>">
            <span style="position:absolute;top:12px;right:12px;background:var(--primary);color:#fff;border-radius:50px;padding:4px 14px;font-weight:700;font-size:0.85rem;"><?= $offer['discount_percent'] ?>% OFF</span>
          </div>
          <div class="card-body">
            <h5 style="font-weight:600;margin-bottom:0.5rem;"><?= htmlspecialchars($offer['title']) ?></h5>
            <p style="color:var(--text-muted);font-size:0.85rem;margin-bottom:1rem;"><?= htmlspecialchars($offer['description']) ?></p>
            <?php if ($offer['expires_at']): ?>
              <p style="font-size:0.78rem;color:var(--accent);"><i class="fas fa-clock me-1"></i>Expires: <?= date('M d, Y', strtotime($offer['expires_at'])) ?></p>
            <?php endif; ?>
            <a href="menu.php" class="btn btn-primary btn-sm mt-2">Order Now</a>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- WHY CHOOSE US -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-4">
      <h2 class="section-title">Why <span class="title-accent">MealConnect</span></h2>
      <div class="divider mx-auto"></div>
      <p class="section-sub">We don't just serve food — we craft experiences</p>
    </div>
    <div class="row g-4">
      <div class="col-md-3 col-6">
        <div class="why-card">
          <div class="why-icon"><img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=60&h=60&fit=crop" style="width:60px;height:60px;border-radius:50%;object-fit:cover;" alt="Fresh"></div>
          <h5>100% Fresh</h5>
          <p>Ingredients sourced daily from local farms and trusted suppliers</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="why-card">
          <div class="why-icon"><img src="https://images.unsplash.com/photo-1526367790999-0150786686a2?w=60&h=60&fit=crop" style="width:60px;height:60px;border-radius:50%;object-fit:cover;" alt="Fast"></div>
          <h5>Fast Delivery</h5>
          <p>Hot food at your doorstep in 30 minutes or your next order is free</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="why-card">
          <div class="why-icon"><img src="https://images.unsplash.com/photo-1556909172-54557c7e4fb7?w=60&h=60&fit=crop" style="width:60px;height:60px;border-radius:50%;object-fit:cover;" alt="Hygiene"></div>
          <h5>Hygienic Kitchen</h5>
          <p>ISO-certified food preparation with daily sanitation and safety checks</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="why-card">
          <div class="why-icon"><img src="https://images.unsplash.com/photo-1514190051997-0f6f39ca5cde?w=60&h=60&fit=crop" style="width:60px;height:60px;border-radius:50%;object-fit:cover;" alt="Support"></div>
          <h5>24/7 Support</h5>
          <p>Our team and AI assistant are always here to help you</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="py-5" style="background:var(--dark2)">
  <div class="container">
    <div class="text-center mb-4">
      <h2 class="section-title">What Our <span class="title-accent">Customers Say</span></h2>
      <div class="divider mx-auto"></div>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="testimonial-card">
          <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400&h=200&fit=crop" style="width:100%;height:120px;object-fit:cover;border-radius:8px;margin-bottom:1rem;" alt="">
          <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
          <p>"The BBQ Bacon Stacker is absolutely insane. Best burger I've had in Karachi — hands down."</p>
          <div class="author">— Ahmed K., Karachi</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial-card">
          <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&h=200&fit=crop" style="width:100%;height:120px;object-fit:cover;border-radius:8px;margin-bottom:1rem;" alt="">
          <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
          <p>"The Pepperoni Feast pizza arrived piping hot in 25 minutes. The crust was perfectly crisp!"</p>
          <div class="author">— Sara M., DHA</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial-card">
          <img src="https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?w=400&h=200&fit=crop" style="width:100%;height:120px;object-fit:cover;border-radius:8px;margin-bottom:1rem;" alt="">
          <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></div>
          <p>"That Nashville Hot chicken is something else. The AI chatbot helped me pick the spice level perfectly!"</p>
          <div class="author">— Bilal R., Clifton</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- RESERVATION CTA -->
<section class="py-5">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-6">
        <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&h=380&fit=crop" alt="Restaurant" style="width:100%;border-radius:16px;object-fit:cover;height:320px;">
      </div>
      <div class="col-lg-6">
        <div class="hero-badge"><i class="fas fa-calendar-check"></i> Dine In With Us</div>
        <h2 class="section-title mt-2">Reserve Your <span class="title-accent">Table</span></h2>
        <div class="divider"></div>
        <p style="color:var(--text-muted);margin-bottom:1.5rem;">Skip the wait. Book a table for your next dine-in experience and we'll have everything ready when you arrive.</p>
        <a href="reservation.php" class="btn btn-accent btn-lg me-3"><i class="fas fa-calendar me-2"></i>Make a Reservation</a>
        <a href="menu.php" class="btn btn-outline-primary btn-lg">Order Online</a>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
