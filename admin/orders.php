<?php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/admin_guard.php';

requireAdmin();

$title = "Admin - Commandes";
require_once __DIR__ . '/../includes/header.php';

$status   = $_GET['status'] ?? 'all';
$archived = $_GET['archived'] ?? '0';

$where = [];
$params = [];

// Archivées
if ($archived === '1') {
  $where[] = "o.is_archived = 1";
} else {
  $where[] = "o.is_archived = 0";
}

// Statut
if ($status !== 'all') {
  $where[] = "o.status = ?";
  $params[] = $status;
}

$whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

$stmt = $pdo->prepare("
  SELECT 
    o.id, o.total, o.status, o.created_at, o.delivered_at, o.is_archived,
    u.fullname, u.email
  FROM orders o
  LEFT JOIN users u ON u.id = o.user_id
  $whereSql
  ORDER BY o.id DESC
");
$stmt->execute($params);
$orders = $stmt->fetchAll();

function statusLabel(string $s): string {
  switch ($s) {
    case 'pending':   return 'En attente';
    case 'paid':      return 'Payée';
    case 'shipped':   return 'Expédiée';
    case 'delivered': return 'Livrée';
    case 'cancelled': return 'Annulée';
    default:          return $s;
  }
}
?>

<header class="container hero">
  <h1>Admin — Commandes 🧾</h1>
  <p>Liste des commandes + accès détail + modification du statut.</p>
</header>

<main class="container">
  <div class="panel" style="padding:16px;">

    <form method="get" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:14px;">
      <label style="display:flex;gap:8px;align-items:center;">
        Statut
        <select name="status" style="padding:8px 10px;border-radius:10px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.08);color:#fff;">
          <option value="all" <?= $status==='all'?'selected':'' ?>>Tous</option>
          <option value="pending" <?= $status==='pending'?'selected':'' ?>>En attente</option>
          <option value="paid" <?= $status==='paid'?'selected':'' ?>>Payée</option>
          <option value="shipped" <?= $status==='shipped'?'selected':'' ?>>Expédiée</option>
          <option value="delivered" <?= $status==='delivered'?'selected':'' ?>>Livrée</option>
          <option value="cancelled" <?= $status==='cancelled'?'selected':'' ?>>Annulée</option>
        </select>
      </label>

      <label style="display:flex;gap:8px;align-items:center;">
        Archivées
        <select name="archived" style="padding:8px 10px;border-radius:10px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.08);color:#fff;">
          <option value="0" <?= $archived==='0'?'selected':'' ?>>Non</option>
          <option value="1" <?= $archived==='1'?'selected':'' ?>>Oui</option>
        </select>
      </label>

      <button class="btn" type="submit">Filtrer</button>

      <a class="btn ghost" href="/-e-commerce-dynamique/public/items.php" style="text-decoration:none;">
        Retour site
      </a>
    </form>

    <?php if (empty($orders)): ?>
      <h2>Aucune commande</h2>
      <p>Aucun résultat pour ces filtres.</p>
    <?php else: ?>
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
                <td style="padding:12px 8px;"><?= htmlspecialchars($o['fullname'] ?? '—') ?></td>
                <td style="padding:12px 8px;"><?= htmlspecialchars($o['email'] ?? '—') ?></td>
                <td style="padding:12px 8px;"><?= htmlspecialchars($o['created_at']) ?></td>
                <td style="padding:12px 8px;"><strong><?= statusLabel($o['status']) ?></strong></td>
                <td style="padding:12px 8px;"><strong><?= number_format((float)$o['total'], 2) ?> €</strong></td>
                <td style="padding:12px 8px;"><?= $o['delivered_at'] ? htmlspecialchars($o['delivered_at']) : '—' ?></td>

                <td style="padding:12px 8px;text-align:right;white-space:nowrap;">
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
    <?php endif; ?>

  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
