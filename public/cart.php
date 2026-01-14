<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$title = "Panier - K-Store";
require_once __DIR__ . '/../includes/header.php';

/* Flash message */
$flash = $_SESSION['flash_cart'] ?? null;
unset($_SESSION['flash_cart']);

// actions panier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // 1) Changement de taille
  if (isset($_POST['size']) && is_array($_POST['size'])) {
    foreach ($_POST['size'] as $key => $sizeId) {
      $key = (string)$key;
      $sizeId = ($sizeId === '' ? null : (int)$sizeId);
      $msg = changeCartSize($pdo, $key, $sizeId);
      if ($msg) $_SESSION['flash_cart'] = $msg;
    }
  }

  // 2) Update quantités
  if (isset($_POST['update'])) {
    foreach (($_POST['qty'] ?? []) as $key => $qty) {
      $msg = updateCartQty($pdo, (string)$key, (int)$qty);
      if ($msg) $_SESSION['flash_cart'] = $msg;
    }
  }

  // 3) Remove
  if (isset($_POST['remove'])) {
    $key = (string)($_POST['remove'] ?? '');
    if ($key !== '') removeFromCart($key);
  }

  // 4) Clear
  if (isset($_POST['clear'])) {
    clearCart();
  }

  header("Location: cart.php");
  exit;
}

$items = getCartItems($pdo);
$total = getCartTotal($items);
?>

<header class="container hero">
  <h1>Votre panier 🛒</h1>
  <p>Vérifie tes produits avant de commander.</p>
</header>

<main class="container">

  <?php if ($flash): ?>
    <div class="alert" style="margin-bottom:14px;"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($items)): ?>
    <div class="panel">
      <h2>Panier vide</h2>
      <p>Ajoute des produits depuis le catalogue.</p>
      <a class="btn" href="/-e-commerce-dynamique/public/items.php">Retour catalogue →</a>
    </div>
  <?php else: ?>

    <form method="post" class="panel" style="padding:16px;">
      <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:820px;">
          <thead>
            <tr style="text-align:left;border-bottom:1px solid rgba(255,255,255,.12);">
              <th style="padding:12px 8px;">Produit</th>
              <th style="padding:12px 8px;">Taille</th>
              <th style="padding:12px 8px;">Prix</th>
              <th style="padding:12px 8px;">Quantité</th>
              <th style="padding:12px 8px;">Total</th>
              <th style="padding:12px 8px;"></th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($items as $it): ?>
              <?php
                $hasSizes = itemHasSizes($pdo, (int)$it['id']);
                $sizes = $hasSizes ? getItemSizes($pdo, (int)$it['id']) : [];
              ?>

              <tr style="border-bottom:1px solid rgba(255,255,255,.08);">
                <td style="padding:12px 8px;">
                  <div style="display:flex;gap:10px;align-items:center;">
                    <img src="/-e-commerce-dynamique/assets/img/<?= htmlspecialchars($it['image']) ?>"
                         alt="<?= htmlspecialchars($it['name']) ?>"
                         style="width:46px;height:46px;object-fit:cover;border-radius:12px;border:1px solid rgba(255,255,255,.14);">
                    <strong><?= htmlspecialchars($it['name']) ?></strong>
                  </div>
                </td>

                <td style="padding:12px 8px;">
                  <?php if ($hasSizes && !empty($sizes)): ?>
                    <select
                      name="size[<?= htmlspecialchars($it['cart_key']) ?>]"
                      style="padding:8px;border-radius:10px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.08);color:#fff;">
                      <?php foreach ($sizes as $s): ?>
                        <option value="<?= (int)$s['id'] ?>" <?= ((int)$it['size_id'] === (int)$s['id']) ? 'selected' : '' ?>>
                          <?= htmlspecialchars($s['code']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  <?php else: ?>
                    <?= $it['size_code'] ? htmlspecialchars($it['size_code']) : '-' ?>
                  <?php endif; ?>
                </td>

                <td style="padding:12px 8px;">
                  <?= number_format((float)$it['price'], 2) ?> €
                </td>

                <td style="padding:12px 8px;">
                  <input type="number" min="1" max="<?= (int)$it['stock'] ?>"
                         name="qty[<?= htmlspecialchars($it['cart_key']) ?>]"
                         value="<?= (int)$it['qty'] ?>"
                         style="width:90px;padding:8px;border-radius:10px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.08);color:#fff;">
                  <div style="font-size:12px;opacity:.75;margin-top:6px;">Stock : <?= (int)$it['stock'] ?></div>
                </td>

                <td style="padding:12px 8px;">
                  <strong><?= number_format((float)$it['total'], 2) ?> €</strong>
                </td>

                <td style="padding:12px 8px;text-align:right;">
                  <button class="btn ghost" type="submit" name="remove" value="<?= htmlspecialchars($it['cart_key']) ?>">
                    Supprimer
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-top:14px;">
        <strong style="font-size:18px;">Total : <?= number_format((float)$total, 2) ?> €</strong>

        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          <button class="btn" type="submit" name="update" value="1">Mettre à jour</button>
          <button class="btn ghost" type="submit" name="clear" value="1">Vider le panier</button>

          <?php if (isset($_SESSION['user'])): ?>
            <a class="btn ghost" href="/-e-commerce-dynamique/public/my_orders.php" style="text-decoration:none;">
              Mes commandes
            </a>
            <a class="btn" href="/-e-commerce-dynamique/public/checkout.php" style="text-decoration:none;">
              Commander
            </a>
          <?php else: ?>
            <a class="btn" href="/-e-commerce-dynamique/public/login.php" style="text-decoration:none;">
              Se connecter pour commander
            </a>
          <?php endif; ?>
        </div>
      </div>
    </form>

  <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
