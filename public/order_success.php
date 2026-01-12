<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$title = "Commande confirmée - K-Store";
require_once __DIR__ . '/../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("Commande introuvable");

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) die("Commande introuvable");
?>

<header class="container hero">
  <h1>Merci ! 🎉</h1>
  <p>Ta commande est confirmée.</p>
</header>

<main class="container">
  <div class="panel">
    <h2>Commande #<?= (int)$order['id'] ?></h2>
    <p><strong>Total :</strong> <?= number_format((float)$order['total'], 2) ?> €</p>
    <p><strong>Statut :</strong> <?= htmlspecialchars($order['status']) ?></p>
    <p><strong>Livraison :</strong> <?= htmlspecialchars($order['customer_address']) ?></p>

    <div class="cta-row" style="margin-top:14px;">
      <a class="btn" href="/-e-commerce-dynamique/public/items.php">Retour catalogue →</a>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
