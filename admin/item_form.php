<?php
// page pour modifier un article
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/admin_guard.php';

requireAdmin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: /e-commerce-dynamique/admin/items.php");
    exit;
}

$item = null;
$msg = null;
$err = null;

$stmt = $pdo->prepare("SELECT i.*, c.name AS category_name FROM items i LEFT JOIN categories c ON c.id=i.category_id WHERE i.id=?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    header("Location: /e-commerce-dynamique/admin/items.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($name) || $price <= 0 || $category_id <= 0) {
        $err = "Nom, prix et catégorie obligatoires.";
    } else {
        $image = $item['image']; // Garde l'ancienne
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Supprime l'ancienne image si nouvelle
            if ($image) {
                $old_path = __DIR__ . '/../public/uploads/' . basename($image);
                if (is_file($old_path)) unlink($old_path);
            }
            $upload_dir = __DIR__ . '/../public/uploads/';
            $image = 'img_' . time() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
        }

        try {
            $stmt = $pdo->prepare("UPDATE items SET name=?, price=?, stock=?, category_id=?, is_active=?, image=? WHERE id=?");
            $stmt->execute([$name, $price, $stock, $category_id, $is_active, $image, $id]);
            $msg = "Article mis à jour !";
            // Recharge les données
            $selectStmt = $pdo->prepare("SELECT i.*, c.name AS category_name FROM items i LEFT JOIN categories c ON c.id=i.category_id WHERE i.id=?");
            $selectStmt->execute([$id]);
            $item = $selectStmt->fetch();
        } catch (Exception $e) {
            $err = "Erreur mise à jour : " . $e->getMessage();
        }
    }
}

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$title = "Admin - Modifier article";
require_once __DIR__ . '/../includes/header.php';
?>

<header class="container hero">
  <h1>Modifier article</h1>
  <p class="subtitle"><?= htmlspecialchars($item['name']) ?></p>
</header>

<main class="container">
  <div class="panel" style="max-width:600px;margin:0 auto;">
    <?php if ($msg): ?>
      <div class="callout" style="background:rgba(69,255,209,.14);border-color:rgba(69,255,209,.25);color:var(--text);">
        <?= htmlspecialchars($msg) ?>
      </div>
    <?php endif; ?>
    <?php if ($err): ?>
      <div class="callout" style="background:rgba(255,90,90,.12);border-color:rgba(255,90,90,.25);color:var(--text);">
        <?= htmlspecialchars($err) ?>
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="filters">
      <div class="filters-row">
        <div class="field">
          <label for="name">Nom</label>
          <input type="text" id="name" name="name" required value="<?= htmlspecialchars($item['name']) ?>">
        </div>

        <div class="field">
          <label for="price">Prix (€)</label>
          <input type="number" id="price" name="price" step="0.01" min="0" required value="<?= $item['price'] ?>">
        </div>

        <div class="field">
          <label for="category_id">Catégorie</label>
          <select id="category_id" name="category_id" required>
            <option value="">Choisir...</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= ($item['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="stock">Stock</label>
          <input type="number" id="stock" name="stock" min="0" value="<?= $item['stock'] ?>">
        </div>

        <div class="field" style="grid-column:span 2;">
          <label for="image">Image actuelle : <?= htmlspecialchars($item['image'] ?? 'Aucune') ?></label>
          <input type="file" id="image" name="image" accept="image/*">
          <small style="display:block;margin-top:4px;color:var(--muted);font-size:12px;">Remplacer l'image existante (optionnel)</small>
        </div>

        <div class="field check">
          <label for="is_active">
            <input type="checkbox" id="is_active" name="is_active" <?= ($item['is_active'] == 1) ? 'checked' : '' ?>> Actif
          </label>
        </div>

        <div class="field actions" style="grid-column:span 4;">
          <button class="btn" type="submit">Enregistrer</button>
        </div>
      </div>
    </form>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>