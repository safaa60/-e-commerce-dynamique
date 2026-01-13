<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$title = "Explorer - K-Store";
require_once __DIR__ . '/../includes/header.php';

/* Ajout panier */
$flash = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_id'])) {
  $flash = addToCart($pdo, (int)$_POST['add_id'], 1);
}

/* Filtres */
$q = trim($_GET['q'] ?? '');
$categoryId = (int)($_GET['category'] ?? 0);
$inStock = (int)($_GET['in_stock'] ?? 0);
$min = $_GET['min'] ?? '';
$max = $_GET['max'] ?? '';
$sort = $_GET['sort'] ?? 'new';

$minVal = ($min === '' ? null : (float)$min);
$maxVal = ($max === '' ? null : (float)$max);

/* catégories */
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

/* SQL dynamique */
$where = ["i.is_active = 1"];
$params = [];

if ($q !== '') {
  $where[] = "(i.name LIKE ? OR i.description LIKE ?)";
  $params[] = "%$q%";
  $params[] = "%$q%";
}

if ($categoryId > 0) {
  $where[] = "i.category_id = ?";
  $params[] = $categoryId;
}

if ($inStock === 1) {
  $where[] = "i.stock > 0";
}

if ($minVal !== null) {
  $where[] = "i.price >= ?";
  $params[] = $minVal;
}

if ($maxVal !== null) {
  $where[] = "i.price <= ?";
  $params[] = $maxVal;
}

$order = "i.id DESC";
if ($sort === 'price_asc') $order = "i.price ASC";
if ($sort === 'price_desc') $order = "i.price DESC";
if ($sort === 'name_asc') $order = "i.name ASC";

$sql = "
SELECT i.*, c.name AS category
FROM items i
LEFT JOIN categories c ON c.id = i.category_id
WHERE " . implode(" AND ", $where) . "
ORDER BY $order
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="container hero">
  <h1>Explorer le magasin 🛍️</h1>
  <p>Recherche avancée • filtres • catégories</p>
</section>

<main class="container">

<?php if ($flash): ?>
  <div class="alert"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<div class="k-filterbar">
  <form method="get" class="k-filters">

    <div class="k-search">
      <span class="k-search-icon">🔎</span>
      <input class="k-input" type="text" name="q" placeholder="Rechercher..." value="<?= htmlspecialchars($q) ?>">
      <?php if ($q !== ''): ?>
        <a class="k-clear" href="explorer.php" title="Effacer">✕</a>
      <?php endif; ?>
    </div>

    <div class="k-row">
      <div class="k-field">
        <label class="k-label">Catégorie</label>
        <select class="k-select" name="category">
          <option value="0">Toutes catégories</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= $categoryId==(int)$c['id']?'selected':'' ?>>
              <?= htmlspecialchars($c['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="k-field">
        <label class="k-label">Trier</label>
        <select class="k-select" name="sort">
          <option value="new" <?= $sort==='new'?'selected':'' ?>>Nouveautés</option>
          <option value="price_asc" <?= $sort==='price_asc'?'selected':'' ?>>Prix ↑</option>
          <option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>Prix ↓</option>
          <option value="name_asc" <?= $sort==='name_asc'?'selected':'' ?>>Nom A-Z</option>
        </select>
      </div>

      <div class="k-field">
        <label class="k-label">Prix</label>
        <div class="k-price">
          <input class="k-input" type="number" step="0.01" name="min" placeholder="Min" value="<?= htmlspecialchars($min) ?>">
          <span class="k-sep">—</span>
          <input class="k-input" type="number" step="0.01" name="max" placeholder="Max" value="<?= htmlspecialchars($max) ?>">
        </div>
      </div>

      <label class="k-check">
        <input type="checkbox" name="in_stock" value="1" <?= $inStock ? 'checked' : '' ?>>
        <span>En stock seulement</span>
      </label>

      <div class="k-actions">
        <button class="btn k-btn" type="submit">Filtrer</button>
        <a class="btn ghost k-btn" href="explorer.php" style="text-decoration:none;">Reset</a>
      </div>
    </div>

  </form>
</div>

<div class="grid" style="margin-top:16px;">
<?php foreach ($items as $item):
  $out = ((int)$item['stock'] <= 0);
  $image = !empty($item['image']) ? $item['image'] : 'placeholder.jpg';
?>
  <article class="card">

    <div class="card-media">
      <img
        src="/-e-commerce-dynamique/assets/img/<?= htmlspecialchars($image) ?>"
        alt="<?= htmlspecialchars($item['name']) ?>"
      >
    </div>

    <div class="card-body">
      <small class="tag"><?= htmlspecialchars($item['category'] ?? 'Sans catégorie') ?></small>
      <?php if ($out): ?>
        <small class="tag" style="background:#ff4d6d;">Épuisé</small>
      <?php endif; ?>

      <h3><?= htmlspecialchars($item['name']) ?></h3>
      <p><?= htmlspecialchars(mb_strimwidth($item['description'] ?? '', 0, 100, '...')) ?></p>

      <div class="row">
        <strong><?= number_format((float)$item['price'], 2) ?> €</strong>
        <span>Stock: <?= (int)$item['stock'] ?></span>
      </div>

      <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap;">
        <a class="btn ghost" href="item.php?id=<?= (int)$item['id'] ?>" style="text-decoration:none;">Voir</a>

        <?php if (!$out): ?>
          <form method="post" style="margin:0;">
            <input type="hidden" name="add_id" value="<?= (int)$item['id'] ?>">
            <button class="btn" type="submit">Ajouter</button>
          </form>
        <?php else: ?>
          <button class="btn" disabled type="button">Indisponible</button>
        <?php endif; ?>
      </div>

    </div>
  </article>
<?php endforeach; ?>
</div>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
