<?php
$adminTitle = 'Manage Categories';
require_once __DIR__ . '/includes/admin_auth.php';
requireAdmin();

$msg = '';
function admin_save_category_image(string $fieldName, string $fallback = ''): string {
  $uploadDir = __DIR__ . '/../images/uploads/';
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }

  if (!empty($_FILES[$fieldName]['name']) && is_uploaded_file($_FILES[$fieldName]['tmp_name'])) {
    $originalName = basename($_FILES[$fieldName]['name']);
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($extension, $allowed, true)) {
      $safeName = 'cat_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
      move_uploaded_file($_FILES[$fieldName]['tmp_name'], $uploadDir . $safeName);
      return 'images/uploads/' . $safeName;
    }
  }

  return $fallback;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $name = sanitize($conn, $_POST['name'] ?? '');
  $currentImage = sanitize($conn, $_POST['current_image'] ?? '');
  $imageUrl = sanitize($conn, $_POST['image_url'] ?? '');
  $image = admin_save_category_image('image_file', $imageUrl ?: $currentImage);
    if ($action === 'add') {
        $stmt = $conn->prepare("INSERT INTO categories (name, image) VALUES (?,?)");
        $stmt->bind_param('ss', $name, $image);
        $stmt->execute();
        $msg = '<div class="alert alert-success">Category added.</div>';
    } elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
    if ($image === '') {
      $image = $currentImage;
    }
    $stmt = $conn->prepare("UPDATE categories SET name = ?, image = ? WHERE id = ?");
    $stmt->bind_param('ssi', $name, $image, $id);
    $stmt->execute();
        $msg = '<div class="alert alert-success">Category updated.</div>';
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM categories WHERE id=$id");
        $msg = '<div class="alert alert-success">Category deleted.</div>';
    }
}

$cats = $conn->query("SELECT * FROM categories ORDER BY id DESC");
require_once __DIR__ . '/includes/admin_header.php';
?>

<?= $msg ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 style="font-weight:700;margin:0;">All Categories</h5>
  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#catModal"><i class="fas fa-plus me-2"></i>Add Category</button>
</div>

<div class="row g-3">
  <?php while ($cat = $cats->fetch_assoc()): ?>
  <div class="col-md-3 col-6">
    <div class="card" style="padding:1rem;text-align:center;">
      <img src="<?= htmlspecialchars($cat['image']) ?>" style="width:70px;height:70px;border-radius:50%;object-fit:cover;margin:0 auto 0.8rem;" alt="">
      <h6 style="font-weight:600;margin-bottom:0.8rem;"><?= htmlspecialchars($cat['name']) ?></h6>
      <div class="d-flex gap-2 justify-content-center">
        <button class="btn btn-sm" style="background:rgba(52,152,219,0.15);color:#3498DB;border:none;" onclick="editCat(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['name']) ?>', '<?= htmlspecialchars($cat['image']) ?>')"><i class="fas fa-edit"></i></button>
        <form method="POST" onsubmit="return confirm('Delete this category?')">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= $cat['id'] ?>">
          <button type="submit" class="btn btn-sm" style="background:rgba(192,57,43,0.15);color:var(--primary-light);border:none;"><i class="fas fa-trash"></i></button>
        </form>
      </div>
    </div>
  </div>
  <?php endwhile; ?>
</div>

<div class="modal fade" id="catModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="background:var(--surface);border:1px solid var(--border);">
      <div class="modal-header" style="border-color:var(--border);">
        <h5 class="modal-title" id="catModalTitle" style="font-weight:700;">Add Category</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="action" id="catAction" value="add">
          <input type="hidden" name="id" id="catId">
          <input type="hidden" name="current_image" id="catCurrentImage">
          <div class="mb-3"><label class="form-label">Category Name *</label><input type="text" name="name" id="catName" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Image from Computer</label><input type="file" name="image_file" id="catImageFile" class="form-control" accept="image/*"></div>
          <div class="mb-3"><label class="form-label">Or Image URL</label><input type="url" name="image_url" id="catImageUrl" class="form-control" placeholder="https://..."></div>
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
function editCat(id, name, image) {
  document.getElementById('catModalTitle').textContent = 'Edit Category';
  document.getElementById('catAction').value = 'edit';
  document.getElementById('catId').value = id;
  document.getElementById('catName').value = name;
  document.getElementById('catCurrentImage').value = image || '';
  document.getElementById('catImageUrl').value = image && !image.startsWith('images/uploads/') ? image : '';
  document.getElementById('catImageFile').value = '';
  new bootstrap.Modal(document.getElementById('catModal')).show();
}
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
