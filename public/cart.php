<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$title = "Panier - K-Store";
require_once __DIR__ . '/../includes/header.php';

$items = getCartItems($pdo);
$total = getCartTotal($items);
?>

<header class="container hero">
  <h1>Votre panier 🛒</h1>
  <p>Modifiez les quantités, supprimez un article, puis passez commande.</p>
</header>

<main class="container cart">

<?php if (empty($items)): ?>
  <div class="panel">
    <h2>Panier vide</h2>
    <p>Retour au catalogue pour ajouter des produits.</p>
    <a class="btn" href="/-e-commerce-dynamique/public/items.php">Voir le catalogue →</a>
  </div>
<?php else: ?>

  <div class="panel">
    <div class="cart-table">
      <div class="cart-head">
        <div>Produit</div>
        <div>Prix</div>
        <div>Quantité</div>
        <div>Sous-total</div>
        <div></div>
      </div>

      <?php foreach ($items as $it): ?>
        <div class="cart-row">
          <div class="cart-product">
            <div class="thumb">🇰🇷</div>
            <div>
              <div class="pname"><?= htmlspecialchars($it['name']) ?></div>
              <div class="pmuted">Stock: <?= (int)$it['stock'] ?></div>
            </div>
          </div>

          <div class="cart-price"><?= number_format((float)$it['price'], 2) ?> €</div>

          <div class="cart-qty">
            <form method="post" action="/-e-commerce-dynamique/public/cart_action.php">
              <input type="hidden" name="action" value="minus">
              <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
              <button class="qty-btn" type="submit">−</button>
            </form>

            <div class="qty-value"><?= (int)$it['qty'] ?></div>

            <form method="post" action="/-e-commerce-dynamique/public/cart_action.php">
              <input type="hidden" name="action" value="plus">
              <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
              <button class="qty-btn" type="submit">+</button>
            </form>
          </div>

          <div class="cart-subtotal"><?= number_format((float)$it['total'], 2) ?> €</div>

          <div class="cart-remove">
            <form method="post" action="/-e-commerce-dynamique/public/cart_action.php">
              <input type="hidden" name="action" value="remove">
              <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
              <button class="remove-btn" type="submit">Supprimer</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="cart-summary">
      <div class="sum-line">
        <span>Total</span>
        <strong><?= number_format((float)$total, 2) ?> €</strong>
      </div>

      <div class="sum-actions">
        <a class="btn ghost" href="/-e-commerce-dynamique/public/items.php">Continuer mes achats</a>
        <a class="btn" href="/-e-commerce-dynamique/public/checkout.php">Passer commande ✅</a>
      </div>
    </div>
  </div>

<?php endif; ?>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
