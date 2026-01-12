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

$title = "Admin - Modifier commande";
require_once __DIR__ . '/../includes/header.php';

// commande + client
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

// lignes commande
$stmt = $pdo->prepare("
  SELECT oi.quantity, oi.unit_price, oi.line_total, i.name
  FROM order_items oi
  JOIN items i ON i.id = oi.item_id
  WHERE oi.order_id = ?
  ORDER BY oi.id DESC
");
$stmt->execute([$orderId]);
$lines = $stmt->fetchAll();

$errors = [];
$success = false;

$allowed = ['pending','paid','shipped','delivered','cancelled'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newStatus = $_POST['status'] ?? $order['status'];
  $archive = isset($_POST['is_archived']) ? 1 : 0;

  if (!in_array($newStatus, $allowed, true)) {
    $errors[] = "Statut invalide.";
  }

  if (empty($errors)) {
    // si livré, on met delivered_at si vide
    $deliveredAt = $order['delivered_at'];

    if ($newStatus === 'delivered' && empty($deliveredAt)) {
      $deliveredAt = date('Y-m-d H:i:s');
    }
    // si on repasse à autre statut, on peut vider delivered_at (optionnel)
    if ($newStatus !== 'delivered') {
      $deliveredAt = null;
      // et on évite l’archivage si pas livré
      if ($archive === 1) $archive = 0;
    }

    $stmt = $pdo->prepare("
      UPDATE orders
      SET status = ?, delivered_at = ?, is_archived = ?
      WHERE id = ?
    ");
    $stmt->execute([$newStatus, $deliveredAt, $archive, $orderId]);

    // refresh
    $stmt = $pdo->prepare("
      SELECT o.*, u.fullname, u.email
      FROM orders o
      LEFT JOIN users u ON u.id = o.user_id
      WHERE o.id = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();

    $success = true;
  }
}

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
  <h1>Modifier commande #<?= (int)$order['id'] ?> ✍️</h1>
  <p>Client : <strong><?= htmlspecialchars($order['fullname'] ?? '—') ?></strong> (<?= htmlspecialchars($order['email'] ?? '—') ?>)</p>
</header>

<main class="container">

  <div class="panel" style="padding:16px;">
    <?php if ($success): ?>
      <div class="callout" style="border-color:rgba(72,255,164,.25);background:rgba(72,255,164,.10);">
        ✅ Modifications enregistrées.
      </div>
    <?php endif; ?>

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

    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
      <div>
        <div><strong>Total :</strong> <?= number_format((float)$order['total'], 2) ?> €</div>
        <div style="opacity:.85;"><strong>Date :</strong> <?= htmlspecialchars($order['created_at']) ?></div>
        <div style="opacity:.85;"><strong>Livrée le :</strong> <?= $order['delivered_at'] ? htmlspecialchars($order['delivered_at']) : '—' ?></div>
        <div style="opacity:.85;"><strong>Archivées :</strong> <?= (int)$order['is_archived'] === 1 ? 'Oui' : 'Non' ?></div>
      </div>

      <a class="btn ghost" href="/-e-commerce-dynamique/admin/orders.php" style="text-decoration:none;">
        ← Retour liste
      </a>
    </div>

    <form method="post" style="margin-top:14px;display:grid;gap:12px;max-width:520px;">
      <label>
        Statut de commande
        <select name="status" style="width:100%;padding:10px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.08);color:#fff;">
          <?php foreach ($allowed as $st): ?>
            <option value="<?= $st ?>" <?= $order['status']===$st?'selected':'' ?>>
              <?= statusLabel($st) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label style="display:flex;gap:10px;align-items:center;">
        <input type="checkbox" name="is_archived" value="1" <?= ((int)$order['is_archived']===1) ? 'checked' : '' ?>>
        Archiver (retirer côté client) — seulement si “Livrée”
      </label>

      <button class="btn" type="submit">Enregistrer</button>
    </form>
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

    <div style="margin-top:14px;">
      <a class="btn ghost" href="/-e-commerce-dynamique/public/order_details.php?id=<?= (int)$order['id'] ?>" style="text-decoration:none;">
        Voir côté client →
      </a>
    </div>
  </div>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
