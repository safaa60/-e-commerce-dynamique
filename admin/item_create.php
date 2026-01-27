<?php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/admin_guard.php';

requireAdmin();

$msg = null;
$err = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($name) || $price <= 0 || $category_id <= 0) {
        $err = "Nom, prix et catégorie obligatoires.";
    } else {
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../public/uploads/';
            $image = 'img_' . time() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image)) {
                $err = "Erreur upload image.";
            }
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO items (name, price, stock, category_id, is_active, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $price, $stock, $category_id, $is_active, $image]);
            $msg = "Article créé avec succès !";
        } catch (Exception $e) {
            $err = "Erreur création : " . $e->getMessage();
        }
    }
}

// Récup catégories pour le select
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$title = "Admin - Ajouter un article";
require_once __DIR__ . '/../includes/header.php';
?>

<header class="container hero">
  <h1>Ajouter un article</h1>
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
          <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>

        <div class="field">
          <label for="price">Prix (€)</label>
          <input type="number" id="price" name="price" step="0.01" min="0" required value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
        </div>

        <div class="field">
          <label for="category_id">Catégorie</label>
          <select id="category_id" name="category_id" required>
            <option value="">Choisir...</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="stock">Stock initial</label>
          <input type="number" id="stock" name="stock" min="0" value="<?= htmlspecialchars($_POST['stock'] ?? 0) ?>">
        </div>

        <div class="field" style="grid-column:span 2;">
          <label for="image">Image (optionnel)</label>
          <input type="file" id="image" name="image" accept="image/*">
        </div>

        <div class="field check">
          <label for="is_active">
            <input type="checkbox" id="is_active" name="is_active" <?= (isset($_POST['is_active']) ? 'checked' : '') ?>> Actif
          </label>
        </div>

        <div class="field actions" style="grid-column:span 4;">
          <button class="btn" type="submit">Créer</button>
        </div>
      </div>
    </form>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>