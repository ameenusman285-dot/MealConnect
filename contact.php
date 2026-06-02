<?php
$pageTitle = 'Contact Us';
require_once 'includes/db_connect.php';
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($conn, $_POST['name']);
    $email = sanitize($conn, $_POST['email']);
    $subject = sanitize($conn, $_POST['subject']);
    $message = sanitize($conn, $_POST['message']);
    if (!$name || !$email || !$message) { $error = 'Please fill in all required fields.'; }
    else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $name, $email, $subject, $message);
        $stmt->execute();
        $success = 'Your message has been sent! We will get back to you shortly.';
    }
}
require_once 'includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">Contact</li></ol></nav>
    <h1>Get In <span class="title-accent">Touch</span></h1>
    <p>We'd love to hear from you</p>
  </div>
</div>
<br><br>
<section class="pb-5">
  <div class="container">
    <div class="row g-5 align-items-start">
      <div class="col-lg-5">
        <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&h=400&fit=crop" style="width:100%;border-radius:16px;object-fit:cover;height:280px;margin-bottom:2rem;" alt="Restaurant">
        <div class="d-flex flex-column gap-3">
          <div class="d-flex gap-3 align-items-start">
            <div style="background:rgba(192,57,43,0.15);color:var(--primary-light);width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-map-marker-alt"></i></div>
            <div><div style="font-weight:600;font-size:0.9rem;">Location</div><div style="color:var(--text-muted);font-size:0.85rem;">123 Flavour Street, DHA Phase 5, Karachi</div></div>
          </div>
          <div class="d-flex gap-3 align-items-start">
            <div style="background:rgba(192,57,43,0.15);color:var(--primary-light);width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-phone"></i></div>
            <div><div style="font-weight:600;font-size:0.9rem;">Phone</div><div style="color:var(--text-muted);font-size:0.85rem;">+92 300 0000000</div></div>
          </div>
          <div class="d-flex gap-3 align-items-start">
            <div style="background:rgba(192,57,43,0.15);color:var(--primary-light);width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-clock"></i></div>
            <div><div style="font-weight:600;font-size:0.9rem;">Hours</div><div style="color:var(--text-muted);font-size:0.85rem;">Daily: 10:00 AM – 11:00 PM</div></div>
          </div>
        </div>
      </div>
      <div class="col-lg-7">
        <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <div class="summary-box">
          <h4 style="font-weight:700;margin-bottom:1.5rem;">Send Us a Message</h4>
          <form method="POST">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Your Name *</label><input type="text" name="name" class="form-control" required></div>
              <div class="col-md-6"><label class="form-label">Email Address *</label><input type="email" name="email" class="form-control" required></div>
              <div class="col-12"><label class="form-label">Subject</label><input type="text" name="subject" class="form-control" placeholder="What is it about?"></div>
              <div class="col-12"><label class="form-label">Message *</label><textarea name="message" class="form-control" rows="5" required placeholder="Write your message here..."></textarea></div>
              <div class="col-12"><button type="submit" class="btn btn-primary" style="padding:0.75rem 2rem;"><i class="fas fa-paper-plane me-2"></i>Send Message</button></div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
