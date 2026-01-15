<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/admin_guard.php';

requireAdmin();

$itemId = (int)($_GET['id'] ?? 0);
if ($itemId <= 0) {
  header("Location: /-e-commerce-dynamique/admin/items.php");
  exit;
}

$itemStmt = $pdo->prepare("SELECT id, name FROM items WHERE id = ? LIMIT 1");
$itemStmt->execute([$itemId]);
$item = $itemStmt->fetch(PDO::FETCH_ASSOC);
if (!$item) {
  header("Location: /-e-commerce-dynamique/admin/items.php");
  exit;
}

/* toutes les tailles */
$sizes = $pdo->query("SELECT id, code FROM sizes ORDER BY code")->fetchAll(PDO::FETCH_ASSOC);

/* tailles déjà liées */
$linkedStmt = $pdo->prepare("SELECT size_id FROM item_sizes WHERE item_id = ?");
$linkedStmt->execute([$itemId]);
$linkedIds = array_map('intval', $linkedStmt->fetchAll(PDO::FETCH_COLUMN));

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $selected = $_POST['sizes'] ?? [];
  $selected = array_map('intval', (array)$selected);

  $pdo->beginTransaction();
  try {
    $pdo->prepare("DELETE FROM item_sizes WHERE item_id = ?")->execute([$itemId]);

    $ins = $pdo->prepare("INSERT INTO item_sizes (item_id, size_id) VALUES (?, ?)");
    foreach ($selected as $sid) {
      if ($sid > 0) $ins->execute([$itemId, $sid]);
    }

    $pdo->commit();
    header("Location: /-e-commerce-dynamique/admin/items.php");
    exit;
  } catch (Exception $e) {
    $pdo->rollBack();
    $error = $e->getMessage();
  }
}

$title = "Admin - Tailles";
require_once __DIR__ . '/../includes/header.php';
?>

<header class="container hero">
  <h1>Tailles</h1>
  <p>Article : <strong><?= htmlspecialchars($item['name']) ?></strong></p>
</header>

<main class="container">
  <div class="panel" style="padding:16px;max-width:900px;margin:0 auto;">
    <?php if ($error): ?>
      <div class="alert" style="margin-bottom:12px;border:1px solid rgba(255,90,90,.35);background:rgba(255,90,90,.12);">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="post" style="display:grid;gap:10px;">
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;">
        <?php foreach ($sizes as $s): ?>
          <label class="k-check" style="padding:10px;border:1px solid rgba(255,255,255,.12);border-radius:12px;">
            <input type="checkbox" name="sizes[]" value="<?= (int)$s['id'] ?>"
              <?= in_array((int)$s['id'], $linkedIds, true) ? 'checked' : '' ?>>
            <span><?= htmlspecialchars($s['code']) ?></span>
          </label>
        <?php endforeach; ?>
      </div>

      <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:10px;">
        <button class="btn" type="submit">Enregistrer</button>
        <a class="btn ghost" href="/-e-commerce-dynamique/admin/items.php" style="text-decora
