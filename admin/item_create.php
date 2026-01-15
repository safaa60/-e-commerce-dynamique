<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/admin_guard.php';

requireAdmin();

$title = "Admin - Ajouter un article";
require_once __DIR__ . '/../includes/header.php';

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
$msg = null;

$name = '';
$description = '';
$price = '0.00';
$stock = '0';
$categoryId = 0;
$isActive = 1;

function uploadImage(): ?string {
  if (empty($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) return null;
  if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) return null;

  $tmp = $_FILES['image']['tmp_name'];
  $orig = basename($_FILES['image']['name']);
  $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));

  $allowed = ['jpg','jpeg','png','webp'];
  if (!in_array($ext, $allowed, true)) return null;

  $newName = 'item_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

  $destDir = __DIR__ . '/../public/uploads';
  if (!is_dir($destDir)) @mkdir($destDir, 0777, true);

  $dest = $destDir . '/' . $newName;
  if (!move_uploaded_file($tmp, $dest)) return null;

  return $newName;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $price = (string)($_POST['price'] ?? '0');
  $stock = (string)($_POST['stock'] ?? '0');
  $categoryId = (int)($_POST['category_id'] ?? 0);
  $isActive = isset($_POST['is_active']) ? 1 : 0;

  if ($name === '' || mb_strlen($name) < 2) $errors[] = "Nom invalide.";
  if (!is_numeric($price) || (float)$price < 0) $errors[] = "Prix invalide.";
  if (!ctype_digit((string)$stock) || (int)$stock < 0) $errors[] = "Stock invalide.";

  $img = uploadImage();
  if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE && $img === null) {
    $errors[] = "Image invalide (jpg/png/webp).";
  }

  if (empty($errors)) {
    $stmt = $pdo->prepare("
      INSERT INTO items (name, description, price, stock, category_id, image, is_active)
      VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
      $name,
      $description,
      (float)$price,
      (int)$stock,
      $categoryId > 0 ? $categoryId : null,
      $img,
      $isActive
    ]);

    header("Location: /-e-commerce-dynamique/admin/items.php");
    exit;
  }
}
?>

<header class="container hero">
  <h1>Ajouter un article</h1>
  <p>Nom • description • prix • stock • image</p>
</header>

<main class="container">
  <div class="panel" style="padding:16px;max-width:900px;margin:0 auto;">

    <?php if (!empty($errors)): ?>
      <div class="alert" style="margin-bottom:12px;border:1px solid rgba(255,90,90,.35);background:rgba(255,90,90,.12);">
        <ul><?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul>
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" style="display:grid;gap:12px;">
      <label>Nom
        <input class="k-input" type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
      </label>

      <label>Description
        <textarea class="k-input" name="description" rows="4"><?= htmlspecialchars($description) ?></textarea>
      </label>

      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <label style="flex:1;min-width:200px;">Prix (€)
          <input class="k-input" type="number" step="0.01" name="price" value="<?= htmlspecialchars($price) ?>" required>
        </label>

        <label style="flex:1;min-width:200px;">Stock
          <input class="k-input" type="number" min="0" name="stock" value="<?= htmlspecialchars($stock) ?>" required>
        </label>
      </div>

      <label>Catégorie
        <select class="k-select" name="category_id">
          <option value="0">Aucune</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= $categoryId==(int)$c['id']?'selected':'' ?>>
              <?= htmlspecialchars($c['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Image (jpg/png/webp)
        <input type="file" name="image" accept="image/*">
      </label>

      <label class="k-check">
        <input type="checkbox" name="is_active" value="1" <?= $isActive ? 'checked' : '' ?>>
        <span>Actif (visible sur le site)</span>
      </label>

      <div style="display:fl
