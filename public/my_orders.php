<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$title = "Mes commandes - K-Store";
require_once __DIR__ . '/../includes/header.php';

$userId = (int)$_SESSION['user']['id'];

$stmt = $pdo->prepare("
  SELECT id, total, status, created_at, delivered_at
  FROM orders
  WHERE user_id = ? AND is_archived = 0
  ORDER BY id DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

function statusLabel($s){
  return match($s){
    'pending'   => 'En attente',
    'paid'      => 'Payée',
    'shipped'   => 'Expédiée',
    'delivered' => 'Livrée',
    'cancelled' => 'Annulée',
    default     => $s
  };
}

function statusClass($s){
  return match($s){
    'pending'   => 'st-pending',
    'paid'      => 'st-paid',
    'shipped'   => 'st-shipped',
    'delivered' => 'st-delivered',
    'cancelled' => 'st-cancelled',
    default     => 'st-pending'
  };
}
?>

<header class="container hero">
  <h1>Mes commandes 🧾</h1>
  <p>Suivi en temps réel : payée → expédiée → livrée.</p>
</header>

<main class="container">
  <div class="panel">
    <?php if (empty($orders)): ?>
      <h2>Aucune commande</h2>
      <p>Tu n’as pas encore de commande en cours.</p>
      <a class="btn" href="/-e-commerce-dynamique/public/items.php">Retour catalogue →</a>
    <?php else: ?>
      <div style="display:grid;gap:12px;">
        <?php foreach ($orders as $o): ?>
          <div class="panel" style="padding:14px;">
            <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center;">
              <strong>Commande #<?= (int)$o['id'] ?></strong>
              <span class="status <?= statusClass($o['status']) ?>">
                <?= statusLabel($o['status']) ?>
              </span>
            </div>

            <div style="margin-top:10px;display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
              <span style="opacity:.85;">Date : <?= htmlspecialchars($o['created_at']) ?></span>
              <strong><?= number_format((float)$o['total'], 2) ?> €</strong>
            </div>

            <?php if ($o['status'] === 'delivered' && !empty($o['delivered_at'])): ?>
              <div style="margin-top:8px;opacity:.9;">
                Livrée le : <?= htmlspecialchars($o['delivered_at']) ?>
              </div>
            <?php endif; ?>

            <div style="margin-top:12px;display:flex;gap:10px;flex-wrap:wrap;">
              <a class="btn ghost" href="/-e-commerce-dynamique/public/order_details.php?id=<?= (int)$o['id'] ?>" style="text-decoration:none;">
                Voir détail →
              </a>

              <?php if ($o['status'] === 'delivered'): ?>
                <a class="btn" href="/-e-commerce-dynamique/public/archive_order.php?id=<?= (int)$o['id'] ?>" style="text-decoration:none;">
                  Retirer de la liste
                </a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
