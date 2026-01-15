<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/admin_guard.php';

requireAdmin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
  header("Location: /-e-commerce-dynamique/admin/items.php");
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) {
  header("Location: /-e-commerce-dynamique/admin/items.php");
  exit;
}

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
$msg = null;

$name = $item['name'] ?? '';
$description = $item['description'] ?? '';
$price = (string)($item['price'] ?? '0.00');
$categoryId = (int)($item['category_id'] ?? 0);
$isActive = (int)($item['is_active'] ?? 1);

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
  $categoryId = (int)($_POST['category_id'] ?? 0);
  $isActive = isset($_POST['is_active']) ? 1 : 0;

  if ($name === '' || mb_strlen($name) < 2) $errors[] = "Nom invalide.";
  if (!is_numeric($price) || (float)$price < 0) $errors[] = "Prix invalide.";

  $newImg = uploadImage();
  if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE && $newImg === null) {
    $errors[] = "Image invalide (jpg/png/webp).";
  }

  if (empty($errors)) {
    $imgToSave = $item['image'];

    if ($newImg !== null) {
      // supprime ancienne image si elle est dans public/uploads
      if (!empty($imgToSave)) {
        $oldPath = __DIR__ . '/../public/uploads/' . basename($imgToSave);
        if (is_file($oldPath)) @unlink($oldPath);
      }
      $imgToSave = $newImg;
    }

    $upd = $pdo->prepare("
      UPDATE items
      SET name = ?, description = ?, price = ?, category_id = ?, image = ?, is_active = ?
      WHERE id = ?
    ");
    $upd->execute([
      $name,
      $description,
      (float)$price,
      $categoryId > 0 ? $categoryId : null,
      $imgToSave,
      $isActive,
      $id
    ]);

    $msg = "Article mis à jour ✅";

    // refresh
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
  }
}

$title = "Admin - Modifier article";
require_once __DIR__ . '/../includes/header.php';
?>

<header class="container hero">
  <h1>Modifier article</h1>
  <p><strong><?= htmlspecialchars($item['name']) ?></strong></p>
</header>

<main class="container">
  <div class="panel" style="padding:16px;max-width:900px;margin:0 auto;">

    <?php if ($msg): ?>
      <div class="alert" style="margin-bottom:12px;"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="alert" style="margin-bottom:12px;border:1px solid rgba(255,90,90,.35);background:rgba(255,90,90,.12);">
        <ul><?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul>
      </div>
    <?php endif; ?>

    <?php
      $img = $item['image'] ?: 'placeholder.jpg';
      $u = "/-e-commerce-dynamique/public/uploads/" . rawurlencode($img);
      $a = "/-e-commerce-dynamique/assets/img/" . rawurlencode($img);
    ?>
    <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:center;margin-bottom:14px;">
      <img src="<?= htmlspecialchars($u) ?>" onerror="this.onerror=null;this.src='<?= htmlspecialchars($a) ?>';"
           style="width:120px;height:120px;object-fit:cover;border-radius:14px;border:1px solid rgba(255,255,255,.14);">
      <div style="opacity:.85;">Image actuelle</div>
    </div>

    <form method="post" enctype="multipart/form-data" style="display:grid;gap:12px;">
      <label>Nom
        <input class="k-input" type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
      </label>

      <label>Description
        <textarea class="k-input" name="description" rows="4"><?= htmlspecialchars($description) ?></textarea>
      </label>

      <label>Prix (€)
        <input class="k-input" type="number" step="0.01" name="price" value="<?= htmlspecialchars($price) ?>" required>
      </label>

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

      <label>Remplacer l’image (jpg/png/webp)
        <input type="file" name="image" accept="image/*">
      </label>

      <label class="k-check">
        <input type="checkbox" name="is_active" value="1" <?= $isActive ? 'checked' : '' ?>>
        <span>Actif (visible sur le site)</span>
      </label>

      <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:6px;">
        <button class="btn" type="submit">Enregistrer</button>
        <a class="btn ghost" href="/-e-commerce-dynamique/admin/items.php" style="text-decoration:none;">← Retour</a>
      </div>
    </form>

  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
