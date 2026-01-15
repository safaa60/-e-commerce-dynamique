<?php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/admin_guard.php';

requireAdmin();

$title = "Admin - Articles";
require_once __DIR__ . '/../includes/header.php';

$error = null;

/* ✅ Suppression article */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $id = (int)$_POST['delete_id'];

  $st = $pdo->prepare("SELECT image FROM items WHERE id = ?");
  $st->execute([$id]);
  $img = $st->fetchColumn();

  $pdo->beginTransaction();
  try {
    $pdo->prepare("DELETE FROM item_sizes WHERE item_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM items WHERE id = ?")->execute([$id]);
    $pdo->commit();

    if ($img) {
      $path = __DIR__ . '/../public/uploads/' . basename($img);
      if (is_file($path)) @unlink($path);
    }

    header("Location: /-e-commerce-dynamique/admin/items.php");
    exit;
  } catch (Exception $e) {
    $pdo->rollBack();
    $error = "Erreur suppression : " . $e->getMessage();
  }
}

/* ✅ Liste articles + info "a des tailles ?" */
$stmt = $pdo->query("
  SELECT
    i.id, i.name, i.price, i.stock, i.is_active, i.image,
    c.name AS category,
    EXISTS(SELECT 1 FROM item_sizes isz WHERE isz.item_id = i.id) AS has_sizes
  FROM items i
  LEFT JOIN categories c ON c.id = i.category_id
  ORDER BY i.id DESC
");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<header class="container hero">
  <h1>Admin • Articles</h1>
  <p>Stock + Modifier article + (Tailles uniquement si nécessaire) + Supprimer</p>
</header>

<main class="container">

  <?php if ($error): ?>
    <div class="alert" style="margin-bottom:12px;border:1px solid rgba(255,90,90,.35);background:rgba(255,90,90,.12);">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="panel" style="padding:16px;">

    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
      <a class="btn" href="/-e-commerce-dynamique/admin/item_create.php" style="text-decoration:none;">
        + Ajouter un article
      </a>

      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a class="btn ghost" href="/-e-commerce-dynamique/admin/orders.php" style="text-decoration:none;">
          Commandes
        </a>
        <a class="btn ghost" href="/-e-commerce-dynamique/admin/users.php" style="text-decoration:none;">
          Utilisateurs
        </a>
      </div>
    </div>

    <div style="overflow:auto;">
      <table style="width:100%;border-collapse:collapse;min-width:1100px;">
        <thead>
          <tr style="text-align:left;border-bottom:1px solid rgba(255,255,255,.12);">
            <th style="padding:12px 8px;">#</th>
            <th style="padding:12px 8px;">Image</th>
            <th style="padding:12px 8px;">Nom</th>
            <th style="padding:12px 8px;">Catégorie</th>
            <th style="padding:12px 8px;">Prix</th>
            <th style="padding:12px 8px;">Stock</th>
            <th style="padding:12px 8px;">Actif</th>
            <th style="padding:12px 8px;text-align:right;">Actions</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($items as $it): ?>
            <?php
              $img = $it['image'] ?: 'placeholder.jpg';
              $u = "/-e-commerce-dynamique/public/uploads/" . rawurlencode($img);
              $a = "/-e-commerce-dynamique/assets/img/" . rawurlencode($img);
              $hasSizes = ((int)$it['has_sizes'] === 1);
            ?>

            <tr style="border-bottom:1px solid rgba(255,255,255,.08);">
              <td style="padding:12px 8px;"><strong><?= (int)$it['id'] ?></strong></td>

              <td style="padding:12px 8px;">
                <img src="<?= htmlspecialchars($u) ?>"
                     onerror="this.onerror=null;this.src='<?= htmlspecialchars($a) ?>';"
                     style="width:46px;height:46px;object-fit:cover;border-radius:12px;border:1px solid rgba(255,255,255,.14);">
              </td>

              <td style="padding:12px 8px;"><?= htmlspecialchars($it['name']) ?></td>
              <td style="padding:12px 8px;"><?= htmlspecialchars($it['category'] ?? '—') ?></td>
              <td style="padding:12px 8px;"><?= number_format((float)$it['price'], 2) ?> €</td>
              <td style="padding:12px 8px;"><strong><?= (int)$it['stock'] ?></strong></td>
              <td style="padding:12px 8px;"><?= ((int)$it['is_active'] === 1) ? '✅' : '❌' ?></td>

              <td style="padding:12px 8px;text-align:right;white-space:nowrap;">
                <a class="btn ghost"
                   href="/-e-commerce-dynamique/admin/item_edit.php?id=<?= (int)$it['id'] ?>"
                   style="text-decoration:none;">
                  Stock
                </a>

                <a class="btn ghost"
                   href="/-e-commerce-dynamique/admin/item_form.php?id=<?= (int)$it['id'] ?>"
                   style="text-decoration:none;">
                  Modifier
                </a>

                <!-- ✅ Tailles UNIQUEMENT si l'article a des tailles -->
                <?php if ($hasSizes): ?>
                  <a class="btn ghost"
                     href="/-e-commerce-dynamique/admin/item_sizes.php?id=<?= (int)$it['id'] ?>"
                     style="text-decoration:none;">
                    Tailles
                  </a>
                <?php endif; ?>

                <form method="post" style="display:inline;margin:0;" onsubmit="return confirm('Supprimer cet article ?');">
                  <input type="hidden" name="delete_id" value="<?= (int)$it['id'] ?>">
                  <button class="btn" type="submit">Supprimer</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>

      </table>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
