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
if ($orderId <= 0) die("Commande introuvable");

$stmt = $pdo->prepare("
  SELECT *
  FROM orders
  WHERE id = ?
  LIMIT 1
");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) die("Commande introuvable");

$stmt = $pdo->prepare("
  SELECT oi.quantity, oi.unit_price, oi.line_total, i.name
  FROM order_items oi
  JOIN items i ON i.id = oi.item_id
  WHERE oi.order_id = ?
  ORDER BY oi.id DESC
");
$stmt->execute([$orderId]);
$lines = $stmt->fetchAll(PDO::FETCH_ASSOC);

$title = "Détail commande #".$orderId;
require_once __DIR__ . '/../includes/header.php';
?>

<header class="container hero">
  <h1>Commande #<?= (int)$order['id'] ?></h1>
  <p>
    Client : <strong><?= htmlspecialchars($order['customer_name'] ?? '—') ?></strong>
    • <?= htmlspecialchars($order['customer_email'] ?? '—') ?>
    • Statut : <strong><?= htmlspecialchars($order['status'] ?? '') ?></strong>
  </p>
</header>

<main class="container">
  <div class="panel">
    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
      <span>Date : <?= htmlspecialchars($order['created_at']) ?></span>
      <strong>Total : <?= number_format((float)$order['total'], 2) ?> €</strong>
    </div>
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

    <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;">
      <a class="btn ghost" href="/-e-commerce-dynamique/public/admin_orders.php" style="text-decoration:none;">← Retour liste</a>
      <a class="btn" href="/-e-commerce-dynamique/public/admin_order_edit.php?id=<?= (int)$orderId ?>" style="text-decoration:none;">Modifier statut</a>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
