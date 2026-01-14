<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$title = "Détail commande - K-Store";
require_once __DIR__ . '/../includes/header.php';

$userId = (int)($_SESSION['user']['id'] ?? 0);
$orderId = (int)($_GET['id'] ?? 0);

if ($orderId <= 0) {
  echo "<main class='container'><div class='panel'><h2>Commande introuvable</h2></div></main>";
  require_once __DIR__ . '/../includes/footer.php';
  exit;
}

$stmt = $pdo->prepare("
  SELECT id, user_id, status, total, created_at, delivered_at
  FROM orders
  WHERE id = ? AND user_id = ?
  LIMIT 1
");
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
  echo "<main class='container'><div class='panel'><h2>Accès refusé</h2><p>Cette commande ne t’appartient pas.</p></div></main>";
  require_once __DIR__ . '/../includes/footer.php';
  exit;
}

$stmt = $pdo->prepare("
  SELECT oi.quantity, oi.unit_price, oi.line_total, i.name
  FROM order_items oi
  JOIN items i ON i.id = oi.item_id
  WHERE oi.order_id = ?
  ORDER BY oi.id DESC
");
$stmt->execute([$orderId]);
$lines = $stmt->fetchAll(PDO::FETCH_ASSOC);

function statusLabel($s){
  $s = strtolower(trim((string)$s));
  return match($s){
    'pending'   => 'En attente',
    'paid'      => 'Payée',
    'shipped'   => 'Expédiée',
    'delivered' => 'Livrée',
    'cancelled' => 'Annulée',
    default     => ($s === '' ? 'Payée' : $s)
  };
}

$rawStatus = (string)($order['status'] ?? '');
?>

<header class="container hero">
  <h1>Commande #<?= (int)$order['id'] ?></h1>
  <p>Statut : <strong><?= htmlspecialchars(statusLabel($rawStatus)) ?></strong></p>
</header>

<main class="container">
  <div class="panel">
    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
      <span>Date : <?= htmlspecialchars($order['created_at']) ?></span>
      <strong>Total : <?= number_format((float)$order['total'], 2) ?> €</strong>
    </div>

    <?php if (strtolower((string)$order['status']) === 'delivered' && !empty($order['delivered_at'])): ?>
      <div style="margin-top:8px;opacity:.9;">
        Livrée le : <strong><?= htmlspecialchars($order['delivered_at']) ?></strong>
      </div>
    <?php endif; ?>
  </div>

  <div class="panel" style="margin-top:14px;">
    <h2>Articles</h2>

    <div style="overflow:auto;">
      <table style="width:100%;border-collapse:collapse;min-width:640px;">
        <thead>
          <tr style="text-align:left;border-bottom:1px solid rgba(255,255,255,.12);">
            <th style="padding:12px 8px;">Produit</th>
            <th style="padding:12px 8px;">Prix</th>
            <th style="padding:12px 8px;">Qté</th>
            <th style="padding:12px 8px;">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($lines as $l): ?>
            <tr style="border-bottom:1px solid rgba(255,255,255,.08);">
              <td style="padding:12px 8px;"><strong><?= htmlspecialchars($l['name']) ?></strong></td>
              <td style="padding:12px 8px;"><?= number_format((float)$l['unit_price'], 2) ?> €</td>
              <td style="padding:12px 8px;"><?= (int)$l['quantity'] ?></td>
              <td style="padding:12px 8px;"><strong><?= number_format((float)$l['line_total'], 2) ?> €</strong></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div style="margin-top:14px;">
      <a class="btn ghost" href="/-e-commerce-dynamique/public/my_orders.php" style="text-decoration:none;">← Retour à mes commandes</a>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
