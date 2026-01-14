<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

if (!isset($_SESSION['user']) || (($_SESSION['user']['role'] ?? '') !== 'admin')) {
  header("Location: /-e-commerce-dynamique/public/items.php");
  exit;
}

$title = "Admin commandes - K-Store";
require_once __DIR__ . '/../includes/header.php';

/* filtres */
$status = $_GET['status'] ?? 'all';
$archived = $_GET['archived'] ?? '0';

$where = [];
$params = [];

if ($status !== 'all') {
  $where[] = "status = ?";
  $params[] = $status;
}

$where[] = "is_archived = ?";
$params[] = (int)$archived;

$sql = "
  SELECT id, user_id, customer_name, customer_email, customer_address,
         status, total, created_at, delivered_at, is_archived
  FROM orders
  WHERE " . implode(" AND ", $where) . "
  ORDER BY id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
  <h1>Admin commandes</h1>
  <p>Gérer le statut des commandes</p>
</header>

<main class="container">
  <div class="panel" style="padding:16px;">

    <form method="get" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;margin-bottom:14px;">
      <label style="display:flex;flex-direction:column;gap:6px;">
        Statut
        <select name="status" style="padding:8px;border-radius:10px;">
          <option value="all" <?= $status==='all'?'selected':'' ?>>Tous</option>
          <option value="pending" <?= $status==='pending'?'selected':'' ?>>En attente</option>
          <option value="paid" <?= $status==='paid'?'selected':'' ?>>Payée</option>
          <option value="shipped" <?= $status==='shipped'?'selected':'' ?>>Expédiée</option>
          <option value="delivered" <?= $status==='delivered'?'selected':'' ?>>Livrée</option>
          <option value="cancelled" <?= $status==='cancelled'?'selected':'' ?>>Annulée</option>
        </select>
      </label>

      <label style="display:flex;flex-direction:column;gap:6px;">
        Archivées
        <select name="archived" style="padding:8px;border-radius:10px;">
          <option value="0" <?= $archived==='0'?'selected':'' ?>>Non</option>
          <option value="1" <?= $archived==='1'?'selected':'' ?>>Oui</option>
        </select>
      </label>

      <button class="btn" type="submit">Filtrer</button>
      <a class="btn ghost" href="/-e-commerce-dynamique/public/items.php" style="text-decoration:none;">Retour site</a>
    </form>

    <div style="overflow:auto;">
      <table style="width:100%;border-collapse:collapse;min-width:980px;">
        <thead>
          <tr style="text-align:left;border-bottom:1px solid rgba(255,255,255,.12);">
            <th style="padding:12px 8px;">#</th>
            <th style="padding:12px 8px;">Client</th>
            <th style="padding:12px 8px;">Email</th>
            <th style="padding:12px 8px;">Date</th>
            <th style="padding:12px 8px;">Statut</th>
            <th style="padding:12px 8px;">Total</th>
            <th style="padding:12px 8px;">Livrée</th>
            <th style="padding:12px 8px;"></th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($orders as $o): ?>
            <tr style="border-bottom:1px solid rgba(255,255,255,.08);">
              <td style="padding:12px 8px;"><strong>#<?= (int)$o['id'] ?></strong></td>
              <td style="padding:12px 8px;"><?= htmlspecialchars($o['customer_name'] ?? '—') ?></td>
              <td style="padding:12px 8px;"><?= htmlspecialchars($o['customer_email'] ?? '—') ?></td>
              <td style="padding:12px 8px;"><?= htmlspecialchars($o['created_at']) ?></td>
              <td style="padding:12px 8px;"><strong><?= htmlspecialchars(statusLabel((string)$o['status'])) ?></strong></td>
              <td style="padding:12px 8px;"><strong><?= number_format((float)$o['total'], 2) ?> €</strong></td>
              <td style="padding:12px 8px;"><?= !empty($o['delivered_at']) ? htmlspecialchars($o['delivered_at']) : '—' ?></td>

              <td style="padding:12px 8px;text-align:right;white-space:nowrap;">
                <a class="btn ghost" href="/-e-commerce-dynamique/public/order_details_admin.php?id=<?= (int)$o['id'] ?>" style="text-decoration:none;">Détail</a>
                <a class="btn" href="/-e-commerce-dynamique/public/admin_order_edit.php?id=<?= (int)$o['id'] ?>" style="text-decoration:none;">Modifier</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>

      </table>
    </div>

  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
