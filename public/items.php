<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$title = "Catalogue - K-Store";
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->prepare("
  SELECT i.id, i.name, i.description, i.price, i.stock, i.image, c.name AS category
  FROM items i
  LEFT JOIN categories c ON c.id = i.category_id
  WHERE i.is_active = 1
  ORDER BY i.id DESC
");
$stmt->execute();
$items = $stmt->fetchAll();
?>

<header class="container hero">
  <div class="badge">
    <span class="flag">🇰🇷</span>
    <span class="hangul">케이스토어</span>
    <span class="dot">•</span>
    <span class="subtitle">K-Store KR</span>
  </div>

  <h1>K-Store <span class="kr">KR</span></h1>
  <p>Boutique de produits coréens • snacks • ramen • k-beauty • k-pop</p>
</header>

<main class="container grid">
<?php foreach ($items as $item): ?>
  <a class="card card-link" href="/-e-commerce-dynamique/public/item.php?id=<?= (int)$item['id'] ?>">
    <div class="card-body">
      <small class="tag"><?= htmlspecialchars($item['category'] ?? 'Sans catégorie') ?></small>
      <h2><?= htmlspecialchars($item['name']) ?></h2>
      <p><?= htmlspecialchars(mb_strimwidth($item['description'], 0, 120, '...')) ?></p>
      <div class="row">
        <strong><?= number_format((float)$item['price'], 2) ?> €</strong>
        <span>Stock: <?= (int)$item['stock'] ?></span>
      </div>
    </div>
  </a>
<?php endforeach; ?>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
