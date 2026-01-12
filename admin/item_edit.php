<?php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/admin_guard.php';

requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  header("Location: /-e-commerce-dynamique/admin/items.php");
  exit;
}

$msg = null;
$err = null;

$stmt = $pdo->prepare("SELECT i.*, c.name AS category FROM items i LEFT JOIN categories c ON c.id=i.category_id WHERE i.id=?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
  header("Location: /-e-commerce-dynamique/admin/items.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
  if ($stock < 0) $stock = 0;

  $restock_at = trim($_POST['restock_at'] ?? '');
  $restock_at = ($restock_at === '') ? null : $restock_at;

  if (isset($_POST['set_out'])) $stock = 0;
  if (isset($_POST['add_10'])) $stock += 10;
  if (isset($_POST['add_50'])) $stock += 50;

  try {
    $u = $pdo->prepare("UPDATE items SET stock=?, restock_at=? WHERE id=?");
    $u->execute([$stock, $restock_at, $id]);

    // refresh
    $stmt->execute([$id]);
    $item = $stmt->fetch();

    $msg = "Stock mis à jour ✅";
  } catch (Throwable $e) {
    $err = "Erreur : " . $e->getMessage();
  }
}

$title = "Admin - Gérer stock";
require_once __DIR__ . '/../includes/header.php';

$isOut = ((int)$item['stock'] <= 0);
?>

<header class="container hero">
  <h1>Gérer Stock</h1>
  <p><strong><?= htmlspecialchars($item['name']) ?></strong> — <?= htmlspecialchars($item['category'] ?? '—') ?></p>
</header>

<main class="container">
  <div class="panel" style="padding:16px;max-width:820px;margin:0 auto;">
    <?php if ($msg): ?>
      <div class="alert" style="margin-bottom:12px;"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if ($err): ?>
      <div class="alert" style="margin-bottom:12px;border:1px solid rgba(255,90,90,.35);background:rgba(255,90,90,.12);">
        <?= htmlspecialchars($err) ?>
      </div>
    <?php endif; ?>

    <div class="card" style="padding:14px;margin-bottom:14px;">
      <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;align-items:center;">
        <div>
          <div style="opacity:.85;">Statut</div>
          <div style="font-size:22px;font-weight:800;">
            <?= $isOut ? "RUPTURE" : "EN STOCK" ?>
          </div>
          <div style="opacity:.85;">Stock actuel : <strong><?= (int)$item['stock'] ?></strong></div>
        </div>

        <form method="post" style="display:flex;gap:10px;flex-wrap:wrap;margin:0;">
          <button class="btn ghost" name="add_10" value="1" type="submit">+10</button>
          <button class="btn ghost" name="add_50" value="1" type="submit">+50</button>
          <button class="btn" name="set_out" value="1" type="submit" style="background:linear-gradient(90deg,#ff5a5a,#ff2f7a);">
            Mettre en rupture
          </button>
        </form>
      </div>
    </div>

    <form method="post" style="display:grid;gap:12px;">
      <label style="display:grid;gap:6px;">
        Stock (valeur exacte)
        <input type="number" name="stock" min="0" value="<?= (int)$item['stock'] ?>">
      </label>

      <label style="display:grid;gap:6px;">
        Date de restock (optionnel)
        <input type="date" name="restock_at" value="<?= $item['restock_at'] ? htmlspecialchars($item['restock_at']) : '' ?>">
        <small style="opacity:.8;">Si stock = 0, la date s’affichera au catalogue.</small>
      </label>

      <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:6px;">
        <button class="btn" type="submit">Enregistrer</button>
        <a class="btn ghost" href="/-e-commerce-dynamique/admin/items.php" style="text-decoration:none;">← Retour</a>
      </div>
    </form>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
