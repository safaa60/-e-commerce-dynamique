<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("Produit introuvable");

$stmt = $pdo->prepare("
  SELECT i.*, c.name AS category
  FROM items i
  LEFT JOIN categories c ON c.id = i.category_id
  WHERE i.id = ? AND i.is_active = 1
");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) die("Produit introuvable");

$title = $item['name'] . " - K-Store";
require_once __DIR__ . '/../includes/header.php';
?>

<header class="container hero">
  <h1><?= htmlspecialchars($item['name']) ?></h1>
  <p><?= htmlspecialchars($item['category'] ?? 'Produit') ?></p>
</header>

<main class="container">

  <div class="card">
    <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>

    <div class="row" style="margin-top:10px;">
      <strong><?= number_format((float)$item['price'], 2) ?> €</strong>
      <span>Stock: <?= (int)$item['stock'] ?></span>
    </div>

    <form method="post" action="/-e-commerce-dynamique/public/cart_add.php" style="margin-top:15px;">
      <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
      <button type="submit" class="btn">Ajouter au panier 🛒</button>
    </form>
  </div>

  <div style="margin-top:16px;">
    <a href="/-e-commerce-dynamique/public/items.php" style="color:#fff;opacity:.9;">
      ← Retour au catalogue
    </a>
  </div>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
