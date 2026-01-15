<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/admin_guard.php';

requireAdmin();

$title = "Admin - Commandes";
require_once __DIR__ . '/../includes/header.php';

function statusLabel(string $s): string {
  $s = strtolower(trim($s));
  return match($s) {
    'pending'   => 'En attente',
    'paid'      => 'Payée',
    'shipped'   => 'Expédiée',
    'delivered' => 'Livrée',
    'cancelled' => 'Annulée',
    default     => ($s === '' ? '—' : $s)
  };
}

/* filtres */
$status = trim($_GET['status'] ?? '');
$archived = trim($_GET['archived'] ?? '0'); // 0 = non archivées

$where = [];
$params = [];

if ($status !== '' && $status !== 'all') {
  $where[] = "o.status = ?";
  $params[] = $status;
}

if ($archived === '1') {
  $where[] = "o.is_archived = 1";
} elseif ($archived === '0') {
  $where[] = "o.is_archived = 0";
}

$sql = "
  SELECT
    o.id, o.customer_name, o.customer_email,
    o.created_at, o.status, o.total, o.delivered_at, o.is_archived
  FROM orders o
";

if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY o.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<header class="container hero">
  <h1>Admin • Commandes</h1>
  <p>Liste des commandes + accès détail + modification du statut</p>
</header>

<main class="container">
  <div class="panel" style="padding:16px;">

    <form method="get" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:14px;">
      <label style="display:flex;gap:10px;align-items:center;">
        Statut
        <select name="status" class="k-select">
          <option value="all" <?= ($status==='' || $status==='all')?'selected':'' ?>>Tous</option>
          <option value="paid" <?= ($status==='paid')?'selected':'' ?>>Payée</option>
          <option value="shipped" <?= ($status==='shipped')?'selected':'' ?>>Expédiée</option>
          <option value="delivered" <?= ($status==='delivered')?'selected':'' ?>>Livrée</option>
          <option value="cancelled" <?= ($status==='cancelled')?'selected':'' ?>>Annulée</option>
        </select>
      </label>

      <label style="display:flex;gap:10px;align-items:center;">
        Archivées
        <select name="archived" class="k-select">
          <option value="0" <?= ($archived==='0')?'selected':'' ?>>Non</option>
          <option value="1" <?= ($archived==='1')?'selected':'' ?>>Oui</option>
          <option value="all" <?= ($archived==='all')?'selected':'' ?>>Toutes</option>
        </select>
      </label>

      <button class="btn" type="submit">Filtrer</button>

      <a class="btn ghost" href="/-e-commerce-dynamique/admin/orders.php" style="text-decoration:none;">
        Reset
      </a>

      <a class="btn ghost" href="/-e-commerce-dynamique/public/items.php" style="text-decoration:none;">
        Retour site
      </a>
    </form>

    <div style="overflow:auto;">
      <table style="width:100%;border-collapse:collapse;min-width:1050px;">
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
              <td style="padding:12px 8px;"><?= htmlspecialchars($o['customer_name'] ?: '—') ?></td>
              <td style="padding:12px 8px;"><?= htmlspecialchars($o['customer_email'] ?: '—') ?></td>
              <td style="padding:12px 8px;"><?= htmlspecialchars($o['created_at']) ?></td>
              <td style="padding:12px 8px;"><strong><?= htmlspecialchars(statusLabel((string)$o['status'])) ?></strong></td>
              <td style="padding:12px 8px;"><?= number_format((float)$o['total'], 2) ?> €</td>
              <td style="padding:12px 8px;"><?= $o['delivered_at'] ? htmlspecialchars($o['delivered_at']) : '—' ?></td>

              <td style="padding:12px 8px;text-align:right;white-space:nowrap;">
                <!-- ✅ IMPORTANT : liens CORRECTS -->
                <a class="btn ghost"
                   href="/-e-commerce-dynamique/admin/order_details.php?id=<?= (int)$o['id'] ?>"
                   style="text-decoration:none;">
                  Détail
                </a>

                <a class="btn"
                   href="/-e-commerce-dynamique/admin/order_edit.php?id=<?= (int)$o['id'] ?>"
                   style="text-decoration:none;">
                  Modifier
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>

      </table>
    </div>

  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
