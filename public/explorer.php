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
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();

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
$items = $stmt->fetchAll();
?>

<header class="container hero">
  <h1>Explorer le magasin 🛍️</h1>
  <p>Recherche avancée • filtres • catégories</p>
</header>

<main class="container">

<?php if ($flash): ?>
  <div class="alert"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<div class="panel" style="padding:16px;">
<form method="get" class="filters">

  <input type="text" name="q" placeholder="Rechercher..." value="<?= htmlspecialchars($q) ?>">

  <select name="category">
    <option value="0">Toutes catégories</option>
    <?php foreach ($categories as $c): ?>
      <option value="<?= $c['id'] ?>" <?= $categoryId==$c['id']?'selected':'' ?>>
        <?= htmlspecialchars($c['name']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <select name="sort">
    <option value="new">Nouveautés</option>
    <option value="price_asc">Prix ↑</option>
    <option value="price_desc">Prix ↓</option>
    <option value="name_asc">Nom A-Z</option>
  </select>

  <input type="number" step="0.01" name="min" placeholder="Prix min" value="<?= htmlspecialchars($min) ?>">
  <input type="number" step="0.01" name="max" placeholder="Prix max" value="<?= htmlspecialchars($max) ?>">

  <label>
    <input type="checkbox" name="in_stock" value="1" <?= $inStock?'checked':'' ?>>
    En stock seulement
  </label>

  <button class="btn">Filtrer</button>
  <a class="btn ghost" href="explorer.php">Reset</a>

</form>
</div>

<div class="grid" style="margin-top:16px;">
<?php foreach ($items as $item): 
  $out = $item['stock'] <= 0;
?>
  <article class="card">
    <div class="card-body">
      <small class="tag"><?= htmlspecialchars($item['category']) ?></small>
      <?php if ($out): ?>
        <small class="tag" style="background:#ff4d6d;">Épuisé</small>
      <?php endif; ?>

      <h2><?= htmlspecialchars($item['name']) ?></h2>
      <p><?= htmlspecialchars(mb_strimwidth($item['description'],0,100,'...')) ?></p>

      <div class="row">
        <strong><?= number_format($item['price'],2) ?> €</strong>
        <span>Stock: <?= (int)$item['stock'] ?></span>
      </div>

      <div style="margin-top:10px;display:flex;gap:8px;">
        <a class="btn ghost" href="item.php?id=<?= $item['id'] ?>">Voir</a>

        <?php if (!$out): ?>
          <form method="post">
            <input type="hidden" name="add_id" value="<?= $item['id'] ?>">
            <button class="btn">Ajouter</button>
          </form>
        <?php else: ?>
          <button class="btn" disabled>Indisponible</button>
        <?php endif; ?>
      </div>

    </div>
  </article>
<?php endforeach; ?>
</div>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
