<?php
$pageTitle = 'Reserve a Table';
require_once 'includes/db_connect.php';
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($conn, $_POST['name']);
    $email = sanitize($conn, $_POST['email']);
    $phone = sanitize($conn, $_POST['phone']);
    $date = sanitize($conn, $_POST['date']);
    $time = sanitize($conn, $_POST['time']);
    $guests = (int)$_POST['guests'];
    $notes = sanitize($conn, $_POST['notes'] ?? '');
    if (!$name || !$phone || !$date || !$time || !$guests) { $error = 'Please fill in all required fields.'; }
    else {
    $stmt = $conn->prepare("INSERT INTO reservations (name, email, phone, date, time, guests, notes) VALUES (?,?,?,?,?,?,?)");
        // types: s=name, s=email, s=phone, s=date, s=time, i=guests, s=notes
        $stmt->bind_param('sssssis', $name, $email, $phone, $date, $time, $guests, $notes);
        $stmt->execute();
        $success = 'Reservation submitted! We will confirm via phone shortly.';
    }
}
require_once 'includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">Reservation</li></ol></nav>
    <h1>Reserve Your <span class="title-accent">Table</span></h1>
    <p>Book ahead and enjoy a premium dine-in experience</p>
  </div>
</div>
<br>
<section class="pb-5">
  <div class="container">
    <div class="row g-5 align-items-start">
      <div class="col-lg-5">
        <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&h=420&fit=crop" style="width:100%;border-radius:16px;object-fit:cover;height:320px;margin-bottom:1.5rem;" alt="Restaurant Interior">
        <div class="why-card">
          <h5 style="margin-bottom:0.8rem;">Reservation Policy</h5>
          <ul style="color:var(--text-muted);font-size:0.85rem;padding-left:1.2rem;line-height:1.9;">
            <li>Book at least 2 hours in advance</li>
            <li>Tables held for 15 mins past reservation time</li>
            <li>Groups above 10: call us directly</li>
            <li>Cancellations: notify 1 hour before</li>
          </ul>
        </div>
      </div>
      <div class="col-lg-7">
        <?php if ($success): ?><div class="alert alert-success mb-4"><?= $success ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger mb-4"><?= $error ?></div><?php endif; ?>
        <div class="summary-box">
          <h4 style="font-weight:700;margin-bottom:1.5rem;">Book a Table</h4>
          <form method="POST">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Full Name *</label><input type="text" name="name" class="form-control" required value="<?= isLoggedIn() ? htmlspecialchars($_SESSION['user_name']) : '' ?>"></div>
              <div class="col-md-6"><label class="form-label">Phone *</label><input type="tel" name="phone" class="form-control" required placeholder="+92 300 0000000"></div>
              <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" placeholder="optional"></div>
              <div class="col-md-6"><label class="form-label">Number of Guests *</label>
                <select name="guests" class="form-select" required>
                  <?php for ($i=1;$i<=10;$i++): ?><option value="<?=$i?>"><?=$i?> <?=$i==1?'Guest':'Guests'?></option><?php endfor; ?>
                </select>
              </div>
              <div class="col-md-6"><label class="form-label">Date *</label><input type="date" name="date" class="form-control" required min="<?= date('Y-m-d') ?>"></div>
              <div class="col-md-6"><label class="form-label">Preferred Time *</label>
                <select name="time" class="form-select" required>
                  <option value="">Select time</option>
                  <?php
                  $times = ['10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00'];
                  foreach ($times as $t) echo "<option value='$t'>".date('g:i A', strtotime($t))."</option>";
                  ?>
                </select>
              </div>
              <div class="col-12"><label class="form-label">Special Requests</label><textarea name="notes" class="form-control" rows="3" placeholder="Any dietary needs, occasion, seating preference..."></textarea></div>
              <div class="col-12"><button type="submit" class="btn btn-primary" style="padding:0.75rem 2rem;"><i class="fas fa-calendar-check me-2"></i>Confirm Reservation</button></div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
