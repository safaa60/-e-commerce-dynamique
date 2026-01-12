<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/admin_guard.php';

requireAdmin();

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId <= 0) {
  header("Location: /-e-commerce-dynamique/admin/orders.php");
  exit;
}

$title = "Admin - Détail commande";
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->prepare("
  SELECT o.*, u.fullname, u.email
  FROM orders o
  LEFT JOIN users u ON u.id = o.user_id
  WHERE o.id = ?
");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
  echo "<main class='container'><div class='panel'><h2>Commande introuvable</h2></div></main>";
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
$lines = $stmt->fetchAll();

function statusLabel($s){
  return match($s){
    'pending' => 'En attente',
    'paid' => 'Payée',
    'shipped' => 'Expédiée',
    'delivered' => 'Livrée',
    'cancelled' => 'Annulée',
    default => $s
  };
}
?>

<header class="container hero">
  <h1>Commande #<?= (int)$order['id'] ?> 🧾</h1>
  <p>Client : <strong><?= htmlspecialchars($order['fullname'] ?? '—') ?></strong> — <?= htmlspecialchars($order['email'] ?? '—') ?></p>
</header>

<main class="container">
  <div class="panel" style="padding:16px;">
    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
      <div>
        <div><strong>Statut :</strong> <?= statusLabel($order['status']) ?></div>
        <div style="opacity:.85;"><strong>Total :</strong> <?= number_format((float)$order['total'], 2) ?> €</div>
        <div style="opacity:.85;"><strong>Date :</strong> <?= htmlspecialchars($order['created_at']) ?></div>
        <div style="opacity:.85;"><strong>Livrée le :</strong> <?= $order['delivered_at'] ? htmlspecialchars($order['delivered_at']) : '—' ?></div>
        <div style="opacity:.85;"><strong>Archivées :</strong> <?= (int)$order['is_archived'] === 1 ? 'Oui' : 'Non' ?></div>
      </div>

      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a class="btn ghost" href="/-e-commerce-dynamique/admin/orders.php" style="text-decoration:none;">← Liste</a>
        <a class="btn" href="/-e-commerce-dynamique/admin/order_edit.php?id=<?= (int)$order['id'] ?>" style="text-decoration:none;">Modifier</a>
      </div>
    </div>
  </div>

  <div class="panel" style="margin-top:14px;padding:16px;">
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
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
