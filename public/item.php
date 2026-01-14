<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) die("Produit introuvable");

$stmt = $pdo->prepare("
  SELECT i.id, i.name, i.description, i.price, i.stock, i.restock_at, i.image, i.is_active,
         c.name AS category
  FROM items i
  LEFT JOIN categories c ON c.id = i.category_id
  WHERE i.id = ? AND i.is_active = 1
");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) die("Produit introuvable");

$isOut  = ((int)$item['stock'] <= 0);
$restock= $item['restock_at'] ? date('d/m/Y', strtotime($item['restock_at'])) : null;
$image  = !empty($item['image']) ? $item['image'] : 'placeholder.jpg';

/* tailles dispos */
$sizesStmt = $pdo->prepare("
  SELECT s.id, s.code
  FROM item_sizes isz
  JOIN sizes s ON s.id = isz.size_id
  WHERE isz.item_id = ?
  ORDER BY s.code
");
$sizesStmt->execute([$id]);
$sizes = $sizesStmt->fetchAll(PDO::FETCH_ASSOC);

$title = htmlspecialchars($item['name']) . " - K-Store";
require_once __DIR__ . '/../includes/header.php';
?>

<header class="container hero">
  <div class="badge">
    <span class="flag">🇰🇷</span>
    <span class="hangul">케이스토어</span>
    <span class="dot">•</span>
    <span class="subtitle">K-Store KR</span>
  </div>

  <h1 style="margin-top:10px;"><?= htmlspecialchars($item['name']) ?></h1>
  <p><?= htmlspecialchars($item['category'] ?? 'Produit') ?></p>
</header>

<main class="container" style="max-width:980px;">
  <div class="panel" style="display:grid;grid-template-columns: 1fr 1fr;gap:18px;align-items:start;">
    <div>
      <div class="card-media" style="height:340px;">
        <img src="/-e-commerce-dynamique/assets/img/<?= htmlspecialchars($image) ?>"
             alt="<?= htmlspecialchars($item['name']) ?>">
      </div>
    </div>

    <div>
      <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:flex-start;">
        <div>
          <?php if ($isOut): ?>
            <span class="tag" style="background:rgba(255,90,90,.18);border:1px solid rgba(255,90,90,.35);color:#ffd0d0;">
              Épuisé
            </span>
            <?php if ($restock): ?>
              <span class="tag" style="margin-left:8px;">
                Restock prévu : <?= htmlspecialchars($restock) ?>
              </span>
            <?php endif; ?>
          <?php else: ?>
            <span class="tag">En stock</span>
          <?php endif; ?>
        </div>

        <div style="text-align:right;">
          <div style="opacity:.8;">Prix</div>
          <div style="font-size:26px;font-weight:800;">
            <?= number_format((float)$item['price'], 2) ?> €
          </div>
          <div style="opacity:.85;">Stock : <?= (int)$item['stock'] ?></div>
        </div>
      </div>

      <hr style="border:none;border-top:1px solid rgba(255,255,255,.10);margin:14px 0;">

      <div style="line-height:1.6;">
        <?= nl2br(htmlspecialchars($item['description'] ?? '')) ?>
      </div>

      <div style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <a class="btn ghost" href="/-e-commerce-dynamique/public/items.php" style="text-decoration:none;">
          ← Retour catalogue
        </a>

        <?php if (!$isOut): ?>
          <form method="post" action="/-e-commerce-dynamique/public/cart_add.php"
                style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;margin:0;">
            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">

            <?php if (!empty($sizes)): ?>
              <label style="display:flex;flex-direction:column;gap:6px;font-weight:800;">
                Taille / pointure
                <select name="size_id" style="padding:8px;border-radius:10px;">
                  <option value="">Auto</option>
                  <?php foreach ($sizes as $s): ?>
                    <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['code']) ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
            <?php endif; ?>

            <label style="display:flex;flex-direction:column;gap:6px;font-weight:800;">
              Quantité
              <input type="number" name="qty" value="1" min="1" max="<?= (int)$item['stock'] ?>" style="width:90px;">
            </label>

            <button class="btn" type="submit">Ajouter au panier</button>
            <a class="btn ghost" href="/-e-commerce-dynamique/public/cart.php" style="text-decoration:none;">Voir panier</a>
          </form>
        <?php else: ?>
          <button class="btn" type="button" disabled style="opacity:.55;cursor:not-allowed;">
            Indisponible
          </button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
