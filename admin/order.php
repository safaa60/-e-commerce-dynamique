<?php
//chaque commandes
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/admin_guard.php';

requireAdmin();

$orderId = (int)($_GET['id'] ?? 0);
if ($orderId <= 0) {
  header("Location: /-e-commerce-dynamique/admin/orders.php");
  exit;
}

$allowed = ['paid','shipped','delivered','cancelled'];
$errors = [];
$flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // 1) update statut
  if (isset($_POST['update_status'])) {
    $status = strtolower(trim($_POST['status'] ?? ''));

    if (!in_array($status, $allowed, true)) {
      $errors[] = "Statut invalide.";
    } else {
      // gérer delivered_at automatiquement
      $stmt = $pdo->prepare("SELECT delivered_at FROM orders WHERE id = ? LIMIT 1");
      $stmt->execute([$orderId]);
      $currentDeliveredAt = $stmt->fetchColumn();

      $deliveredAt = $currentDeliveredAt;
      if ($status === 'delivered') {
        if (empty($deliveredAt)) $deliveredAt = date('Y-m-d H:i:s');
      } else {
        $deliveredAt = null;
      }

      if (!$errors) {
        $up = $pdo->prepare("UPDATE orders SET status = ?, delivered_at = ? WHERE id = ?");
        $up->execute([$status, $deliveredAt, $orderId]);
        $flash = "Statut mis à jour.";
      }
    }
  }

  // 2) archive toggle
  if (isset($_POST['toggle_archive'])) {
    $new = (int)($_POST['is_archived'] ?? 0) === 1 ? 1 : 0;
    $up = $pdo->prepare("UPDATE orders SET is_archived = ? WHERE id = ?");
    $up->execute([$new, $orderId]);
    $flash = $new ? "Commande archivée." : "Commande désarchivée.";
  }
}

$stmt = $pdo->prepare("
  SELECT o.*, u.fullname, u.email AS user_email
  FROM orders o
  LEFT JOIN users u ON u.id = o.user_id
  WHERE o.id = ?
  LIMIT 1
");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
  header("Location: /-e-commerce-dynamique/admin/orders.php");
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

$title = "Admin - Commande #".$orderId;
require_once __DIR__ . '/../includes/header.php';

$customerName = trim(($order['customer_firstname'] ?? '') . ' ' . ($order['customer_lastname'] ?? ''));
if ($customerName === '') $customerName = ($order['fullname'] ?? '—');
?>

<header class="container hero">
  <h1>Commande #<?= (int)$order['id'] ?> 🧾</h1>
  <p>Client : <strong><?= htmlspecialchars($customerName) ?></strong> — <?= htmlspecialchars($order['customer_email'] ?? ($order['user_email'] ?? '—')) ?></p>
</header>

<main class="container">

  <div class="panel" style="padding:16px;">

    <?php if ($flash): ?>
      <div class="alert" style="margin-bottom:12px;"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <?php if ($errors): ?>
      <div class="alert" style="margin-bottom:12px;border:1px solid rgba(255,90,90,.35);background:rgba(255,90,90,.12);">
        <ul><?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul>
      </div>
    <?php endif; ?>

    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
      <div>
        <div><strong>Statut :</strong> <?= htmlspecialchars(statusLabel((string)$order['status'])) ?></div>
        <div style="opacity:.85;"><strong>Total :</strong> <?= number_format((float)$order['total'], 2) ?> €</div>
        <div style="opacity:.85;"><strong>Date :</strong> <?= htmlspecialchars($order['created_at']) ?></div>
        <div style="opacity:.85;"><strong>Livrée le :</strong> <?= !empty($order['delivered_at']) ? htmlspecialchars($order['delivered_at']) : '—' ?></div>
        <div style="opacity:.85;"><strong>Archivée :</strong> <?= (int)$order['is_archived'] === 1 ? 'Oui' : 'Non' ?></div>
      </div>

      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a class="btn ghost" href="/-e-commerce-dynamique/admin/orders.php" style="text-decoration:none;">← Liste</a>
      </div>
    </div>

    <hr style="margin:16px 0;border:none;border-top:1px solid rgba(255,255,255,.12);">

    <form method="post" style="display:grid;gap:12px;max-width:520px;">
      <input type="hidden" name="update_status" value="1">

      <label>Changer le statut
        <select name="status" class="k-select" required>
          <?php foreach (['paid','shipped','delivered','cancelled'] as $s): ?>
            <option value="<?= htmlspecialchars($s) ?>" <?= ((string)$order['status'] === $s) ? 'selected' : '' ?>>
              <?= htmlspecialchars(statusLabel($s)) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <button class="btn" type="submit">Enregistrer</button>
    </form>

    <form method="post" style="margin-top:12px;">
      <input type="hidden" name="toggle_archive" value="1">
      <input type="hidden" name="is_archived" value="<?= ((int)$order['is_archived'] === 1) ? 0 : 1 ?>">
      <button class="btn ghost" type="submit">
        <?= ((int)$order['is_archived'] === 1) ? 'Désarchiver' : 'Archiver' ?>
      </button>
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
  </div>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
