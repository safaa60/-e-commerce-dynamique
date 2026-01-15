<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/admin_guard.php';

requireAdmin();

$orderId = (int)($_GET['id'] ?? 0);
if ($orderId <= 0) {
  header("Location: /-e-commerce-dynamique/admin/orders.php");
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
  header("Location: /-e-commerce-dynamique/admin/orders.php");
  exit;
}

$errors = [];
$msg = null;

$allowed = ['paid','shipped','delivered','cancelled'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $status = strtolower(trim($_POST['status'] ?? ''));

  if (!in_array($status, $allowed, true)) {
    $errors[] = "Statut invalide.";
  }

  $deliveredAt = $order['delivered_at'];
  if ($status === 'delivered') {
    // si on passe à livrée et qu’il n’y a pas de date, on met maintenant
    if (empty($deliveredAt)) {
      $deliveredAt = date('Y-m-d H:i:s');
    }
  } else {
    // si ce n’est pas livré, on enlève la date de livraison
    $deliveredAt = null;
  }

  if (empty($errors)) {
    $up = $pdo->prepare("UPDATE orders SET status = ?, delivered_at = ? WHERE id = ?");
    $up->execute([$status, $deliveredAt, $orderId]);

    // refresh
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

  }
}

function statusLabel(string $s): string {
  $s = strtolower(trim($s));
  return match($s) {
    'paid'      => 'Payée',
    'shipped'   => 'Expédiée',
    'delivered' => 'Livrée',
    'cancelled' => 'Annulée',
    default     => $s
  };
}

$title = "Admin - Modifier commande #".$orderId;
require_once __DIR__ . '/../includes/header.php';
?>

<header class="container hero">
  <h1>Modifier commande #<?= (int)$order['id'] ?></h1>
  <p>
    Client : <strong><?= htmlspecialchars($order['customer_name'] ?? '—') ?></strong>
    • <?= htmlspecialchars($order['customer_email'] ?? '—') ?>
  </p>
</header>

<main class="container">
  <div class="panel" style="padding:16px;max-width:820px;margin:0 auto;">

    <?php if ($msg): ?>
      <div class="alert" style="margin-bottom:12px;"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="alert" style="margin-bottom:12px;border:1px solid rgba(255,90,90,.35);background:rgba(255,90,90,.12);">
        <ul><?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul>
      </div>
    <?php endif; ?>

    <form method="post" style="display:grid;gap:12px;">
      <label>Statut
        <select name="status" class="k-select" required>
          <?php foreach ($allowed as $s): ?>
            <option value="<?= htmlspecialchars($s) ?>" <?= ($order['status'] === $s) ? 'selected' : '' ?>>
              <?= htmlspecialchars(statusLabel($s)) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <div style="opacity:.85;">
        Statut actuel : <strong><?= htmlspecialchars(statusLabel((string)$order['status'])) ?></strong><br>
        Livrée le : <strong><?= $order['delivered_at'] ? htmlspecialchars($order['delivered_at']) : '—' ?></strong>
      </div>

      <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:6px;">
        <button class="btn" type="submit">Enregistrer</button>
        <a class="btn ghost" href="/-e-commerce-dynamique/admin/orders.php" style="text-decoration:none;">← Retour</a>
      </div>
    </form>

  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
