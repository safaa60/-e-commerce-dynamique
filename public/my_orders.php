<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$title = "Mes commandes - K-Store";
require_once __DIR__ . '/../includes/header.php';

$userId = (int)$_SESSION['user']['id'];

$stmt = $pdo->prepare("SELECT id, total, status, created_at
                       FROM orders
                       WHERE user_id = ?
                       ORDER BY id DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();
?>

<header class="container hero">
  <h1>Mes commandes 🧾</h1>
  <p>Historique de tes commandes payées.</p>
</header>

<main class="container">
  <div class="panel">
    <?php if (empty($orders)): ?>
      <h2>Aucune commande</h2>
      <p>Tu n’as pas encore passé de commande.</p>
      <a class="btn" href="/-e-commerce-dynamique/public/items.php">Voir le catalogue →</a>
    <?php else: ?>
      <div class="orders-list" style="display:grid;gap:12px;">
        <?php foreach ($orders as $o): ?>
          <div class="panel" style="padding:14px;">
            <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
              <strong>Commande #<?= (int)$o['id'] ?></strong>
              <span><?= htmlspecialchars($o['created_at']) ?></span>
            </div>
            <div style="margin-top:8px;display:flex;justify-content:space-between;">
              <span>Statut : <?= htmlspecialchars($o['status']) ?></span>
              <strong><?= number_format((float)$o['total'], 2) ?> €</strong>
            </div>
            <div style="margin-top:10px;">
              <a class="btn ghost" href="/-e-commerce-dynamique/public/order_details.php?id=<?= (int)$o['id'] ?>" style="text-decoration:none;">
                Voir détail →
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
