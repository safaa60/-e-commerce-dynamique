<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$title = "Commander - K-Store";
require_once __DIR__ . '/../includes/header.php';

$items = getCartItems($pdo);
$total = getCartTotal($items);

if (empty($items)) {
  echo '<main class="container"><div class="panel"><h2>Panier vide</h2></div></main>';
  require_once __DIR__ . '/../includes/footer.php';
  exit;
}

$userId = (int)$_SESSION['user']['id'];
$userEmail = $_SESSION['user']['email'] ?? '';

$errors = [];

$first = $_POST['first_name'] ?? '';
$last  = $_POST['last_name'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$postal = $_POST['postal'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (strlen($first) < 2) $errors[] = "Prénom invalide.";
  if (strlen($last) < 2) $errors[] = "Nom invalide.";
  if (strlen($phone) < 6) $errors[] = "Téléphone invalide.";
  if (strlen($address) < 8) $errors[] = "Adresse invalide.";
  if (strlen($postal) < 4) $errors[] = "Code postal invalide.";

  foreach ($items as $it) {
    if ((int)$it['qty'] > (int)$it['stock']) {
      $errors[] = "Stock insuffisant pour ".$it['name'];
    }
  }

  if (empty($errors)) {
    try {
      $pdo->beginTransaction();

      $stmt = $pdo->prepare("
        INSERT INTO orders 
        (user_id, customer_firstname, customer_lastname, customer_phone, customer_email, customer_address, customer_postal, total, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'paid')
      ");

      $stmt->execute([
        $userId, $first, $last, $phone, $userEmail, $address, $postal, $total
      ]);

      $orderId = (int)$pdo->lastInsertId();

      $stmtLine = $pdo->prepare("
        INSERT INTO order_items (order_id, item_id, quantity, unit_price, line_total)
        VALUES (?, ?, ?, ?, ?)
      ");

      $stmtStock = $pdo->prepare("
        UPDATE items SET stock = stock - ? WHERE id = ? AND stock >= ?
      ");

      foreach ($items as $it) {
        $stmtLine->execute([$orderId, $it['id'], $it['qty'], $it['price'], $it['total']]);
        $stmtStock->execute([$it['qty'], $it['id'], $it['qty']]);

        if ($stmtStock->rowCount() === 0) throw new Exception("Stock insuffisant");
      }

      $pdo->commit();
      clearCart();

      header("Location: /-e-commerce-dynamique/public/order_success.php?id=".$orderId);
      exit;

    } catch (Exception $e) {
      $pdo->rollBack();
      $errors[] = "Erreur commande : ".$e->getMessage();
    }
  }
}

function field($v){ return htmlspecialchars($v ?? '', ENT_QUOTES); }
?>

<header class="container hero">
  <h1>Commander</h1>
  <p>Connecté : <strong><?= htmlspecialchars($userEmail) ?></strong></p>
</header>

<main class="container">

<div class="panel">
<h2>Infos de livraison</h2>

<?php if ($errors): ?>
<div class="callout">
<ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<form method="post" style="display:grid;gap:12px;max-width:600px">

<div style="display:flex;gap:12px">
  <input name="first_name" placeholder="Prénom" value="<?= field($first) ?>" required class="k-input">
  <input name="last_name" placeholder="Nom" value="<?= field($last) ?>" required class="k-input">
</div>

<input name="phone" placeholder="Téléphone" value="<?= field($phone) ?>" required class="k-input">

<textarea name="address" placeholder="Adresse" rows="3" required class="k-input"><?= field($address) ?></textarea>

<input name="postal" placeholder="Code postal" value="<?= field($postal) ?>" required class="k-input">

<button class="btn" type="submit">Payer & Valider la commande</button>

<div style="display:flex;gap:10px">
<a class="btn ghost" href="/-e-commerce-dynamique/public/cart.php">← Retour panier</a>
<a class="btn ghost" href="/-e-commerce-dynamique/public/my_orders.php">Mes commandes →</a>
</div>

</form>
</div>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
