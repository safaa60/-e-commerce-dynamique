<?php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("Produit introuvable");

$msg = null;

// Ajout au panier (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
  $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
  $qty = max(1, $qty);

  // addToCart limite déjà au stock
  $msg = addToCart($pdo, $id, $qty);
}

$stmt = $pdo->prepare("
  SELECT i.id, i.name, i.description, i.price, i.stock, i.restock_at, i.image, i.is_active,
         c.name AS category
  FROM items i
  LEFT JOIN categories c ON c.id = i.category_id
  WHERE i.id = ? AND i.is_active = 1
");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) die("Produit introuvable");

$isOut = ((int)$item['stock'] <= 0);
$restock = $item['restock_at'] ? date('d/m/Y', strtotime($item['restock_at'])) : null;

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
  <?php if ($msg): ?>
    <div class="alert" style="margin-bottom:14px;">
      <?= htmlspecialchars($msg) ?>
    </div>
  <?php endif; ?>

  <div class="card" style="padding:18px;">
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

    <p style="line-height:1.6;">
      <?= nl2br(htmlspecialchars($item['description'])) ?>
    </p>

    <div style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
      <a class="btn ghost" href="/-e-commerce-dynamique/public/items.php" style="text-decoration:none;">
        ← Retour catalogue
      </a>

      <?php if (!$isOut): ?>
        <form method="post" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin:0;">
          <input type="hidden" name="add_to_cart" value="1">
          <label style="display:flex;gap:8px;align-items:center;">
            Quantité
            <input type="number" name="qty" value="1" min="1" max="<?= (int)$item['stock'] ?>" style="width:90px;">
          </label>
          <button class="btn" type="submit">Ajouter au panier</button>
        </form>
      <?php else: ?>
        <!-- pas de formulaire -->
        <button class="btn" type="button" disabled style="opacity:.55;cursor:not-allowed;">
          Indisponible
        </button>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
