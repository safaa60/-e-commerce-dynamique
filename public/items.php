<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_id'])) {
  $id = (int)$_POST['add_id'];
  $msg = addToCart($pdo, $id, 1);
}

$stmt = $pdo->prepare("
  SELECT i.id, i.name, i.description, i.price, i.stock, i.restock_at, i.image, i.is_active,
         c.name AS category
  FROM items i
  LEFT JOIN categories c ON c.id = i.category_id
  WHERE i.is_active = 1
  ORDER BY i.published_at DESC
");
$stmt->execute();
$items = $stmt->fetchAll();

$title = "Catalogue - K-Store";
require_once __DIR__ . '/../includes/header.php';
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

<main class="container">
  <?php if ($msg): ?>
    <div class="alert" style="margin-bottom:14px;">
      <?= htmlspecialchars($msg) ?>
    </div>
  <?php endif; ?>

  <div class="grid">
    <?php foreach ($items as $item): ?>
      <?php
        $isOut = ((int)$item['stock'] <= 0);
        $restock = $item['restock_at'] ? date('d/m/Y', strtotime($item['restock_at'])) : null;
      ?>
      <article class="card">
        <div class="card-body">
          <small class="tag"><?= htmlspecialchars($item['category'] ?? 'Sans catégorie') ?></small>

          <?php if ($isOut): ?>
            <small class="tag" style="margin-left:8px;background:rgba(255,90,90,.18);border:1px solid rgba(255,90,90,.35);color:#ffd0d0;">
              Épuisé
            </small>
          <?php endif; ?>

          <h2 style="margin-top:10px;">
            <a href="/-e-commerce-dynamique/public/item.php?id=<?= (int)$item['id'] ?>" style="color:inherit;text-decoration:none;">
              <?= htmlspecialchars($item['name']) ?>
            </a>
          </h2>

          <p><?= htmlspecialchars(mb_strimwidth($item['description'], 0, 120, '...')) ?></p>

          <div class="row">
            <strong><?= number_format((float)$item['price'], 2) ?> €</strong>
            <span>Stock: <?= (int)$item['stock'] ?></span>
          </div>

          <?php if ($isOut && $restock): ?>
            <div style="margin-top:8px;opacity:.9;">
              <small>Restock prévu : <strong><?= htmlspecialchars($restock) ?></strong></small>
            </div>
          <?php endif; ?>

          <div style="margin-top:12px;display:flex;gap:10px;flex-wrap:wrap;">
            <a class="btn ghost" href="/-e-commerce-dynamique/public/item.php?id=<?= (int)$item['id'] ?>" style="text-decoration:none;">
              Voir
            </a>

            <?php if (!$isOut): ?>
              <form method="post" style="margin:0;">
                <input type="hidden" name="add_id" value="<?= (int)$item['id'] ?>">
                <button class="btn" type="submit">Ajouter au panier</button>
              </form>
            <?php else: ?>
              <button class="btn" type="button" disabled style="opacity:.55;cursor:not-allowed;">
                Indisponible
              </button>
            <?php endif; ?>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
