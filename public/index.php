<?php
$title = "Accueil - K-Store";
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->prepare("
  SELECT i.id, i.name, i.description, i.price, c.name AS category
  FROM items i
  LEFT JOIN categories c ON c.id = i.category_id
  WHERE i.is_active = 1
  ORDER BY i.published_at DESC
  LIMIT 6
");
$stmt->execute();
$items = $stmt->fetchAll();
?>

<header class="container hero">
  <div class="badge">
    <span class="flag">🇰🇷</span>
    <span class="hangul">케이스토어</span>
    <span class="dot">•</span>
    <span class="subtitle">K-Store</span>
  </div>
  <h1>K-Store</h1>
  <p>Produits coréens : ramen, snacks, boissons, k-beauty, k-pop 🌸</p>
</header>

<main class="container">
  <h2 style="margin:0 0 12px;">Nouveautés</h2>
  <section class="grid">
    <?php foreach ($items as $item): ?>
      <article class="card">
        <small class="tag"><?= htmlspecialchars($item['category'] ?? 'Produit') ?></small>
        <h2><?= htmlspecialchars($item['name']) ?></h2>
        <p><?= htmlspecialchars(mb_strimwidth($item['description'], 0, 110, '...')) ?></p>
        <div class="row">
          <strong><?= number_format((float)$item['price'], 2) ?> €</strong>
          <a href="/-e-commerce-dynamique/public/item.php?id=<?= (int)$item['id'] ?>" style="color:#fff;opacity:.9">Voir →</a>
        </div>
      </article>
    <?php endforeach; ?>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
