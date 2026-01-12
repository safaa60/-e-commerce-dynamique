<?php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/admin_guard.php';

requireAdmin();

$title = "Admin - Stock Produits";
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->prepare("
  SELECT i.id, i.name, i.stock, i.restock_at, i.is_active, c.name AS category
  FROM items i
  LEFT JOIN categories c ON c.id = i.category_id
  ORDER BY i.id DESC
");
$stmt->execute();
$items = $stmt->fetchAll();

function fmtDate($d){
  if (!$d) return '—';
  return date('d/m/Y', strtotime($d));
}
?>

<header class="container hero">
  <h1>Admin — Stock Produits 📦</h1>
  <p>Ajouter du stock, mettre en rupture, et date de restock.</p>
</header>

<main class="container">
  <div class="panel" style="padding:16px;">
    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
      <a class="btn ghost" href="/-e-commerce-dynamique/admin/orders.php" style="text-decoration:none;">← Admin Commandes</a>
      <a class="btn ghost" href="/-e-commerce-dynamique/public/items.php" style="text-decoration:none;">Voir Catalogue</a>
    </div>

    <div style="overflow:auto;">
      <table style="width:100%;border-collapse:collapse;min-width:900px;">
        <thead>
          <tr style="text-align:left;border-bottom:1px solid rgba(255,255,255,.12);">
            <th style="padding:12px 8px;">ID</th>
            <th style="padding:12px 8px;">Produit</th>
            <th style="padding:12px 8px;">Catégorie</th>
            <th style="padding:12px 8px;">Stock</th>
            <th style="padding:12px 8px;">Restock</th>
            <th style="padding:12px 8px;">Actif</th>
            <th style="padding:12px 8px;"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $it): ?>
            <?php $out = ((int)$it['stock'] <= 0); ?>
            <tr style="border-bottom:1px solid rgba(255,255,255,.08);">
              <td style="padding:12px 8px;"><strong>#<?= (int)$it['id'] ?></strong></td>

              <td style="padding:12px 8px;">
                <?= htmlspecialchars($it['name']) ?>
                <?php if ($out): ?>
                  <span class="tag" style="margin-left:8px;background:rgba(255,90,90,.18);border:1px solid rgba(255,90,90,.35);color:#ffd0d0;">
                    Rupture
                  </span>
                <?php endif; ?>
              </td>

              <td style="padding:12px 8px;"><?= htmlspecialchars($it['category'] ?? '—') ?></td>
              <td style="padding:12px 8px;"><strong><?= (int)$it['stock'] ?></strong></td>
              <td style="padding:12px 8px;"><?= fmtDate($it['restock_at']) ?></td>
              <td style="padding:12px 8px;"><?= (int)$it['is_active'] ? 'Oui' : 'Non' ?></td>

              <td style="padding:12px 8px;text-align:right;white-space:nowrap;">
                <a class="btn" href="/-e-commerce-dynamique/admin/item_edit.php?id=<?= (int)$it['id'] ?>" style="text-decoration:none;">
                  Gérer stock
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
