<?php
$adminTitle = 'Special Offers';
require_once __DIR__ . '/includes/admin_auth.php';
requireAdmin();

$msg = '';
function admin_save_offer_image(string $fieldName, string $fallback = ''): string {
    $uploadDir = __DIR__ . '/../images/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (!empty($_FILES[$fieldName]['name']) && is_uploaded_file($_FILES[$fieldName]['tmp_name'])) {
        $originalName = basename($_FILES[$fieldName]['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($extension, $allowed, true)) {
            $safeName = 'offer_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
            move_uploaded_file($_FILES[$fieldName]['tmp_name'], $uploadDir . $safeName);
            return 'images/uploads/' . $safeName;
        }
    }

    return $fallback;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare('DELETE FROM special_offers WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $msg = '<div class="alert alert-success">Offer deleted.</div>';
    } elseif ($action === 'add' || $action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $title = sanitize($conn, $_POST['title'] ?? '');
        $description = sanitize($conn, $_POST['description'] ?? '');
        $discount = (int)($_POST['discount_percent'] ?? 0);
        $expiresAt = sanitize($conn, $_POST['expires_at'] ?? '');
        $currentImage = sanitize($conn, $_POST['current_image'] ?? '');
        $imageUrl = sanitize($conn, $_POST['image_url'] ?? '');
        $image = admin_save_offer_image('image_file', $imageUrl ?: $currentImage);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($action === 'add') {
            $stmt = $conn->prepare('INSERT INTO special_offers (title, description, discount_percent, image, is_active, expires_at) VALUES (?,?,?,?,?,?)');
            $stmt->bind_param('ssisis', $title, $description, $discount, $image, $isActive, $expiresAt);
            $stmt->execute();
            $msg = '<div class="alert alert-success">Offer added.</div>';
        } else {
            if ($image === '') {
                $image = $currentImage;
            }
            $stmt = $conn->prepare('UPDATE special_offers SET title = ?, description = ?, discount_percent = ?, image = ?, is_active = ?, expires_at = ? WHERE id = ?');
            $stmt->bind_param('ssisisi', $title, $description, $discount, $image, $isActive, $expiresAt, $id);
            $stmt->execute();
            $msg = '<div class="alert alert-success">Offer updated.</div>';
        }
    }
}

$offers = $conn->query('SELECT * FROM special_offers ORDER BY created_at DESC');
require_once __DIR__ . '/includes/admin_header.php';
?>

<?= $msg ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h5 style="font-weight:700;margin:0;">Special Offers</h5>
  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#offerModal"><i class="fas fa-plus me-2"></i>Add Offer</button>
</div>

<div class="row g-3">
  <?php while ($o = $offers->fetch_assoc()): ?>
  <div class="col-lg-4 col-md-6">
    <div class="card h-100" style="border:1px solid var(--border);border-radius:18px;overflow:hidden;">
      <img src="<?= htmlspecialchars($o['image']) ?>" alt="" style="width:100%;height:180px;object-fit:cover;">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start gap-2">
          <h6 style="font-weight:700;margin-bottom:0.4rem;"><?= htmlspecialchars($o['title']) ?></h6>
          <span class="status-badge status-delivery"><?= (int)$o['discount_percent'] ?>% OFF</span>
        </div>
        <p style="color:var(--text-muted);font-size:0.85rem;"><?= htmlspecialchars($o['description']) ?></p>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <small style="color:var(--text-muted);"><?= $o['is_active'] ? 'Active' : 'Inactive' ?></small>
          <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-primary" onclick='editOffer(<?= json_encode($o, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>Edit</button>
            <form method="POST" onsubmit="return confirm('Delete this offer?');">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= $o['id'] ?>">
              <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endwhile; ?>
</div>

<div class="modal fade" id="offerModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="background:var(--surface);border:1px solid var(--border);">
      <div class="modal-header" style="border-color:var(--border);">
        <h5 class="modal-title" id="offerModalTitle" style="font-weight:700;">Add Offer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="action" id="offerAction" value="add">
          <input type="hidden" name="id" id="offerId">
          <input type="hidden" name="current_image" id="offerCurrentImage">
          <div class="row g-3">
            <div class="col-md-8"><label class="form-label">Title *</label><input type="text" name="title" id="offerTitle" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Discount % *</label><input type="number" name="discount_percent" id="offerDiscount" class="form-control" min="1" max="100" required></div>
            <div class="col-12"><label class="form-label">Description</label><textarea name="description" id="offerDescription" class="form-control" rows="3"></textarea></div>
            <div class="col-md-6"><label class="form-label">Image from Computer</label><input type="file" name="image_file" id="offerImageFile" class="form-control" accept="image/*"></div>
            <div class="col-md-6"><label class="form-label">Or Image URL</label><input type="url" name="image_url" id="offerImageUrl" class="form-control" placeholder="https://..."></div>
            <div class="col-md-6"><label class="form-label">Expires At</label><input type="date" name="expires_at" id="offerExpiresAt" class="form-control"></div>
            <div class="col-md-6 d-flex align-items-end"><div class="form-check"><input type="checkbox" name="is_active" id="offerActive" class="form-check-input" checked><label class="form-check-label" for="offerActive" style="color:var(--text-muted);">Active</label></div></div>
          </div>
        </div>
        <div class="modal-footer" style="border-color:var(--border);">
          <button type="button" class="btn btn-outline-primary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function editOffer(o) {
  document.getElementById('offerModalTitle').textContent = 'Edit Offer';
  document.getElementById('offerAction').value = 'edit';
  document.getElementById('offerId').value = o.id;
  document.getElementById('offerCurrentImage').value = o.image || '';
  document.getElementById('offerTitle').value = o.title || '';
  document.getElementById('offerDescription').value = o.description || '';
  document.getElementById('offerDiscount').value = o.discount_percent || 0;
  document.getElementById('offerExpiresAt').value = o.expires_at || '';
  document.getElementById('offerImageUrl').value = o.image && !String(o.image).startsWith('images/uploads/') ? o.image : '';
  document.getElementById('offerImageFile').value = '';
  document.getElementById('offerActive').checked = o.is_active == 1;
  new bootstrap.Modal(document.getElementById('offerModal')).show();
}
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>