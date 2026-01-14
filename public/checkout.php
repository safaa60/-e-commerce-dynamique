<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin(); // compte obligatoire

$title = "Commander - K-Store";
require_once __DIR__ . '/../includes/header.php';

$items = getCartItems($pdo);
$total = getCartTotal($items);

if (empty($items)) {
  echo '<main class="container"><div class="panel"><h2>Panier vide</h2><p>Ajoute des produits avant de commander.</p>
        <a class="btn" href="/-e-commerce-dynamique/public/items.php">Catalogue →</a></div></main>';
  require_once __DIR__ . '/../includes/footer.php';
  exit;
}

$errors = [];

$userId = (int)($_SESSION['user']['id'] ?? 0);
$userName = $_SESSION['user']['fullname'] ?? '';
$userEmail = $_SESSION['user']['email'] ?? '';

if ($userId <= 0) {
  header("Location: /-e-commerce-dynamique/public/login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $address = trim($_POST['address'] ?? '');

  if ($address === '' || mb_strlen($address) < 8) {
    $errors[] = "Adresse invalide.";
  }

  // Vérif stock avant commande
  if (empty($errors)) {
    foreach ($items as $it) {
      if ((int)$it['qty'] > (int)$it['stock']) {
        $errors[] = "Stock insuffisant pour : " . $it['name'];
      }
    }
  }

  if (empty($errors)) {
    try {
      $pdo->beginTransaction();

      // 1) créer la commande (✅ conforme à ta table orders)
      $stmt = $pdo->prepare("
        INSERT INTO orders (
          user_id,
          customer_name,
          customer_email,
          customer_address,
          status,
          total,
          created_at,
          delivered_at,
          is_archived
        )
        VALUES (?, ?, ?, ?, ?, ?, NOW(), NULL, 0)
      ");
      $stmt->execute([
        $userId,
        $userName,
        $userEmail,
        $address,
        'paid',
        $total
      ]);

      $orderId = (int)$pdo->lastInsertId();

      // 2) lignes commande + décrément stock
      $stmtLine = $pdo->prepare("
        INSERT INTO order_items (order_id, item_id, quantity, unit_price, line_total)
        VALUES (?, ?, ?, ?, ?)
      ");

      $stmtStock = $pdo->prepare("
        UPDATE items
        SET stock = stock - ?
        WHERE id = ? AND stock >= ?
      ");

      foreach ($items as $it) {
        $qty = (int)$it['qty'];
        $price = (float)$it['price'];
        $lineTotal = (float)$it['total'];

        $stmtLine->execute([$orderId, (int)$it['id'], $qty, $price, $lineTotal]);

        $stmtStock->execute([$qty, (int)$it['id'], $qty]);
        if ($stmtStock->rowCount() === 0) {
          throw new Exception("Stock insuffisant pendant la commande.");
        }
      }

      $pdo->commit();

      clearCart();
      header("Location: /-e-commerce-dynamique/public/order_success.php?id=" . $orderId);
      exit;

    } catch (Exception $e) {
      $pdo->rollBack();
      $errors[] = "Erreur commande : " . $e->getMessage();
    }
  }
}

$oldAddress = htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES);
?>

<header class="container hero">
  <h1>Commander ✅</h1>
  <p>Tu es connecté(e) en tant que <strong><?= htmlspecialchars($userEmail) ?></strong></p>
</header>

<main class="container">

  <div class="panel">
    <h2>Récapitulatif</h2>

    <?php foreach ($items as $it): ?>
      <div style="display:flex;justify-content:space-between;gap:14px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.12)">
        <span><?= htmlspecialchars($it['name']) ?> × <?= (int)$it['qty'] ?></span>
        <strong><?= number_format((float)$it['total'], 2) ?> €</strong>
      </div>
    <?php endforeach; ?>

    <div style="display:flex;justify-content:space-between;margin-top:12px;font-size:18px">
      <span>Total</span>
      <strong><?= number_format((float)$total, 2) ?> €</strong>
    </div>
  </div>

  <div class="panel" style="margin-top:18px;">
    <h2>Livraison</h2>

    <?php if (!empty($errors)): ?>
      <div class="callout" style="border-color:rgba(255,91,91,.35);background:rgba(255,91,91,.12)">
        <strong>Corrige :</strong>
        <ul>
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" style="display:grid;gap:12px;max-width:520px;">
      <label>
        Adresse de livraison
        <textarea name="address" required rows="3"
                  style="width:100%;padding:10px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.08);color:#fff"><?= $oldAddress ?></textarea>
      </label>

      <button class="btn" type="submit">Payer & Valider la commande</button>

      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a class="btn ghost" href="/-e-commerce-dynamique/public/cart.php"
           style="text-decoration:none;text-align:center">← Retour panier</a>
        <a class="btn ghost" href="/-e-commerce-dynamique/public/my_orders.php"
           style="text-decoration:none;text-align:center">Mes commandes →</a>
      </div>
    </form>
  </div>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
