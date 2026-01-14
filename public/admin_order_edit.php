<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

if (!isset($_SESSION['user']) || (($_SESSION['user']['role'] ?? '') !== 'admin')) {
  header("Location: /-e-commerce-dynamique/public/items.php");
  exit;
}

$orderId = (int)($_GET['id'] ?? 0);
if ($orderId <= 0) {
  header("Location: /-e-commerce-dynamique/public/admin_orders.php");
  exit;
}

$stmt = $pdo->prepare("
  SELECT id, customer_name, customer_email, status, total, created_at, delivered_at
  FROM orders
  WHERE id = ?
  LIMIT 1
");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) die("Commande introuvable");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $status = strtolower(trim((string)($_POST['status'] ?? '')));

  $allowed = ['pending','paid','shipped','delivered','cancelled'];
  if (!in_array($status, $allowed, true)) {
    $status = strtolower((string)$order['status']);
    if (!in_array($status, $allowed, true)) $status = 'paid';
  }

  $deliveredAt = $order['delivered_at'] ?? null;
  if ($status === 'delivered' && empty($deliveredAt)) {
    $deliveredAt = date('Y-m-d H:i:s');
  }
  if ($status !== 'delivered') {
    // option : remettre à null si pas livré
    $deliveredAt = null;
  }

  $upd = $pdo->prepare("UPDATE orders SET status = ?, delivered_at = ? WHERE id = ?");
  $upd->execute([$status, $deliveredAt, $orderId]);

  header("Location: /-e-commerce-dynamique/public/admin_orders.php");
  exit;
}

$title = "Modifier commande #".$orderId;
require_once __DIR__ . '/../includes/header.php';

function statusLabel(string $s): string {
  $s = strtolower(trim($s));
  return match($s) {
    'pending'   => 'En attente',
    'paid'      => 'Payée',
    'shipped'   => 'Expédiée',
    'delivered' => 'Livrée',
    'cancelled' => 'Annulée',
    default     => $s
  };
}
?>

<header class="container hero">
  <h1>Modifier commande #<?= (int)$orderId ?></h1>
  <p>
    Client : <strong><?= htmlspecialchars($order['customer_name'] ?? '—') ?></strong>
    • <?= htmlspecialchars($order['customer_email'] ?? '—') ?>
    • Statut actuel : <strong><?= htmlspecialchars(statusLabel((string)$order['status'])) ?></strong>
  </p>
</header>

<main class="container">
  <div class="panel" style="padding:16px;max-width:900px;">

    <form method="post" style="display:flex;flex-direction:column;gap:14px;">
      <label style="display:flex;flex-direction:column;gap:6px;font-weight:800;">
        Statut
        <select name="status" style="padding:10px;border-radius:10px;">
          <option value="pending" <?= strtolower((string)$order['status'])==='pending'?'selected':'' ?>>En attente</option>
          <option value="paid" <?= strtolower((string)$order['status'])==='paid'?'selected':'' ?>>Payée</option>
          <option value="shipped" <?= strtolower((string)$order['status'])==='shipped'?'selected':'' ?>>Expédiée</option>
          <option value="delivered" <?= strtolower((string)$order['status'])==='delivered'?'selected':'' ?>>Livrée</option>
          <option value="cancelled" <?= strtolower((string)$order['status'])==='cancelled'?'selected':'' ?>>Annulée</option>
        </select>
      </label>

      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <button class="btn" type="submit">Enregistrer</button>
        <a class="btn ghost" href="/-e-commerce-dynamique/public/admin_orders.php" style="text-decoration:none;">← Retour</a>
      </div>
    </form>

  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
