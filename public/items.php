<?php
session_start();
require_once __DIR__ . '/../config/db.php';

/**
 * 6 produits à la une (les plus récents)
 */
try {
    $stmt = $pdo->prepare("
        SELECT i.id, i.name, i.description, i.price, i.stock, i.restock_at, i.image,
               c.name AS category
        FROM items i
        LEFT JOIN categories c ON c.id = i.category_id
        WHERE i.is_active = 1
        ORDER BY i.published_at DESC, i.id DESC
        LIMIT 6
    ");
    $stmt->execute();
} catch (PDOException $e) {
    $stmt = $pdo->prepare("
        SELECT i.id, i.name, i.description, i.price, i.stock, i.restock_at, i.image,
               c.name AS category
        FROM items i
        LEFT JOIN categories c ON c.id = i.category_id
        WHERE i.is_active = 1
        ORDER BY i.id DESC
        LIMIT 6
    ");
    $stmt->execute();
}

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$title = "K-Store - Catalogue";
require_once __DIR__ . '/../includes/header.php';
?>

<section class="container hero">
  <div class="badge">
    <span class="flag">🇰🇷</span>
    <span class="hangul">케이스토어</span>
    <span class="dot">•</span>
    <span class="subtitle">K-Store KR</span>
  </div>

  <h1>K-Store <span class="kr">KR</span></h1>
  <p>Le meilleur de la Corée, livré chez toi 🇰🇷✨</p>

  <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
    <a class="btn" href="/-e-commerce-dynamique/public/explorer.php" style="text-decoration:none;">
      Explorer tout le magasin →
    </a>
    <a class="btn ghost" href="/-e-commerce-dynamique/public/cart.php" style="text-decoration:none;">
      Voir le panier
    </a>
  </div>
</section>

<main class="container">
  <div style="display:flex;justify-content:space-between;align-items:end;gap:12px;flex-wrap:wrap;margin-bottom:10px;">
    <h2 style="margin:0;">Produits à la une ✨</h2>
  </div>

  <div class="grid">
    <?php foreach ($items as $item): ?>
      <?php
        $isOut = ((int)$item['stock'] <= 0);
        $restock = !empty($item['restock_at']) ? date('d/m/Y', strtotime($item['restock_at'])) : null;

        $image = !empty($item['image']) ? $item['image'] : 'placeholder.jpg';
        $desc = $item['description'] ?? '';
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

          <?php if ($isOut): ?>
            <small class="tag" style="margin-left:8px;background:rgba(255,90,90,.18);border:1px solid rgba(255,90,90,.35);color:#ffd0d0;">
              Épuisé
            </small>
          <?php endif; ?>

          <h3 style="margin-top:10px;"><?= htmlspecialchars($item['name']) ?></h3>

          <?php if (!empty($desc)): ?>
            <p><?= htmlspecialchars(mb_strimwidth($desc, 0, 120, '...')) ?></p>
          <?php endif; ?>

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
            <a class="btn ghost"
               href="/-e-commerce-dynamique/public/item.php?id=<?= (int)$item['id'] ?>"
               style="text-decoration:none;">
              Voir
            </a>

            <?php if (!$isOut): ?>
              <form method="post" action="/-e-commerce-dynamique/public/cart_add.php" style="margin:0;">
                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                <input type="hidden" name="qty" value="1">
                <button class="btn" type="submit">Ajouter</button>
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
