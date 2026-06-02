<?php
$adminTitle = 'Manage Foods';
require_once __DIR__ . '/includes/admin_auth.php';
requireAdmin();

$msg = '';
function admin_save_image_upload(string $fieldName, string $fallback = ''): string {
  $uploadDir = __DIR__ . '/../images/uploads/';
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }

  // 1) If user uploaded a file, save it and return the saved relative path
  if (!empty($_FILES[$fieldName]['name']) && is_uploaded_file($_FILES[$fieldName]['tmp_name'])) {
    $originalName = basename($_FILES[$fieldName]['name']);
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($extension, $allowed, true)) {
      $safeName = 'food_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
      move_uploaded_file($_FILES[$fieldName]['tmp_name'], $uploadDir . $safeName);
      return 'images/uploads/' . $safeName;
    }
  }

  // 2) If no file uploaded, keep fallback (can be an uploads path or an external URL)
  return $fallback;
}

function normalize_image_value(string $value): string {
  $value = trim($value);
  return $value === '' ? '' : $value;
}

function is_probably_url(string $value): bool {
  return (bool)preg_match('#^https?://#i', $value);
}

function sanitize_image_input($conn, string $dbValue): string {
  // Use existing sanitize() for safety (dbValue can be a URL or relative path)
  return sanitize($conn, $dbValue);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    if ($action === 'add' || $action === 'edit') {
        $name = sanitize($conn, $_POST['name']);
        $desc = sanitize($conn, $_POST['description']);
        $price = (float)$_POST['price'];
        $catId = (int)$_POST['category_id'];
        $currentImage = sanitize($conn, $_POST['current_image'] ?? '');
        $imageUrl = sanitize($conn, $_POST['image_url'] ?? '');

        // If no upload selected, keep either URL (add) or current image (edit)
        $fallback = $imageUrl !== '' ? $imageUrl : $currentImage;
        $image = admin_save_image_upload('image_file', $fallback);
        $featured = isset($_POST['is_featured']) ? 1 : 0;
        $available = isset($_POST['is_available']) ? 1 : 0;
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO foods (category_id, name, description, price, image, is_featured, is_available) VALUES (?,?,?,?,?,?,?)");
      $stmt->bind_param('issdsii', $catId, $name, $desc, $price, $image, $featured, $available);
            $stmt->execute();
            $msg = '<div class="alert alert-success">Food item added successfully.</div>';
        } else {
            $id = (int)$_POST['id'];
      if ($image === '') {
        $image = $currentImage;
      }
      $stmt = $conn->prepare("UPDATE foods SET category_id = ?, name = ?, description = ?, price = ?, image = ?, is_featured = ?, is_available = ? WHERE id = ?");
      $stmt->bind_param('issdsiii', $catId, $name, $desc, $price, $image, $featured, $available, $id);
      $stmt->execute();
            $msg = '<div class="alert alert-success">Food item updated.</div>';
        }
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM foods WHERE id=$id");
        $msg = '<div class="alert alert-success">Food item deleted.</div>';
    }
}

$foods = $conn->query("SELECT f.*, c.name as cat_name FROM foods f JOIN categories c ON f.category_id=c.id ORDER BY f.id DESC");
$categories = $conn->query("SELECT * FROM categories");
$catOptions = '';
while ($c = $categories->fetch_assoc()) $catOptions .= "<option value='{$c['id']}'>{$c['name']}</option>";

require_once __DIR__ . '/includes/admin_header.php';
?>

<?= $msg ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h5 style="font-weight:700;margin:0;">All Foods</h5>
  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-2"></i>Add Food</button>
</div>

<div class="summary-box" style="padding:0;overflow:hidden;">
  <table class="table mb-0">
    <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Featured</th><th>Available</th><th>Actions</th></tr></thead>
    <tbody>
      <?php while ($f = $foods->fetch_assoc()): ?>
      <tr>
        <td>
          <?php $img = $f['image'] ?? ''; ?>
          <img
            src="<?= htmlspecialchars($img !== '' ? $img : 'https://via.placeholder.com/100x100?text=No+Image') ?>"
            style="width:50px;height:50px;border-radius:8px;object-fit:cover;"
            alt="">
        </td>
        <td style="font-weight:600;font-size:0.88rem;"><?= htmlspecialchars($f['name']) ?></td>
        <td><span class="badge-cat"><?= htmlspecialchars($f['cat_name']) ?></span></td>
        <td style="color:var(--accent);font-weight:600;">PKR <?= number_format($f['price']) ?></td>
        <td><?= $f['is_featured'] ? '<span style="color:#27AE60;font-size:0.8rem;"><i class="fas fa-check-circle"></i></span>' : '<span style="color:#555;font-size:0.8rem;"><i class="fas fa-times-circle"></i></span>' ?></td>
        <td><?= $f['is_available'] ? '<span style="color:#27AE60;font-size:0.8rem;"><i class="fas fa-check-circle"></i></span>' : '<span style="color:#E74C3C;font-size:0.8rem;"><i class="fas fa-times-circle"></i></span>' ?></td>
        <td>
          <button class="btn btn-sm me-1" style="background:rgba(52,152,219,0.15);color:#3498DB;border:none;" onclick="editFood(<?= htmlspecialchars(json_encode($f)) ?>)"><i class="fas fa-edit"></i></button>
          <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this food item?')">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $f['id'] ?>">
            <button type="submit" class="btn btn-sm" style="background:rgba(192,57,43,0.15);color:var(--primary-light);border:none;"><i class="fas fa-trash"></i></button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="background:var(--surface);border:1px solid var(--border);">
      <div class="modal-header" style="border-color:var(--border);">
        <h5 class="modal-title" id="modalTitle" style="font-weight:700;">Add New Food</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="foodForm" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="action" id="formAction" value="add">
          <input type="hidden" name="id" id="foodId">
          <input type="hidden" name="current_image" id="foodCurrentImage">
          <div class="row g-3">
            <div class="col-md-8"><label class="form-label">Food Name *</label><input type="text" name="name" id="foodName" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Category *</label><select name="category_id" id="foodCat" class="form-select" required><?= $catOptions ?></select></div>
            <div class="col-12"><label class="form-label">Description</label><textarea name="description" id="foodDesc" class="form-control" rows="2"></textarea></div>
            <div class="col-md-4"><label class="form-label">Price (PKR) *</label><input type="number" name="price" id="foodPrice" class="form-control" step="0.01" required></div>
            <div class="col-md-8"><label class="form-label">Image from Computer</label><input type="file" name="image_file" id="foodImageFile" class="form-control" accept="image/*"></div>
            <div class="col-12"><label class="form-label">Or Image URL</label><input type="url" name="image_url" id="foodImageUrl" class="form-control" placeholder="https://..."></div>
            <div class="col-md-6"><div class="form-check"><input type="checkbox" name="is_featured" id="foodFeatured" class="form-check-input"><label class="form-check-label" for="foodFeatured" style="color:var(--text-muted);">Mark as Featured</label></div></div>
            <div class="col-md-6"><div class="form-check"><input type="checkbox" name="is_available" id="foodAvailable" class="form-check-input" checked><label class="form-check-label" for="foodAvailable" style="color:var(--text-muted);">Available</label></div></div>
          </div>
        </div>
        <div class="modal-footer" style="border-color:var(--border);">
          <button type="button" class="btn btn-outline-primary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm">Save Food</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(function () {
  // Ensure the modal always opens when user clicks "Add Food"
  const addModalEl = document.getElementById('addModal');

  function openAddModal() {
    if (window.bootstrap && bootstrap.Modal && addModalEl) {
      new bootstrap.Modal(addModalEl).show();
    } else if (addModalEl) {
      addModalEl.classList.add('show');
      addModalEl.style.display = 'block';
    }
  }

  // Patch the "Add Food" button (in case data-bs attributes are not working)
  const addBtn = document.querySelector('[data-bs-target="#addModal"]');
  if (addBtn) addBtn.addEventListener('click', openAddModal);

  window.editFood = function editFood(f) {
    document.getElementById('modalTitle').textContent = 'Edit Food';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('foodId').value = f.id;
    document.getElementById('foodCurrentImage').value = f.image || '';
    document.getElementById('foodName').value = f.name;
    document.getElementById('foodDesc').value = f.description;
    document.getElementById('foodPrice').value = f.price;

    // If current image is a saved uploads file, keep it via current_image.
    // If it's an external URL, store it in image_url.
    document.getElementById('foodImageUrl').value = (f.image && !f.image.startsWith('images/uploads/')) ? f.image : '';

    document.getElementById('foodImageFile').value = '';
    document.getElementById('foodCat').value = f.category_id;
    document.getElementById('foodFeatured').checked = f.is_featured == 1;
    document.getElementById('foodAvailable').checked = f.is_available == 1;

    openAddModal();
  };
})();
</script>
