<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/admin_guard.php';

requireAdmin();

$title = "Admin - Utilisateurs";
require_once __DIR__ . '/../includes/header.php';

$error = null;

/* suppression user */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
  $uid = (int)$_POST['delete_user_id'];
  $me = (int)($_SESSION['user']['id'] ?? 0);

  if ($uid === $me) {
    $error = "Tu ne peux pas supprimer ton propre compte admin.";
  } else {
    try {
      $pdo->beginTransaction();

      // ✅ détacher les commandes de ce user (conserver historique)
      $pdo->prepare("UPDATE orders SET user_id = NULL WHERE user_id = ?")->execute([$uid]);

      // ✅ supprimer l'utilisateur
      $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$uid]);

      $pdo->commit();

      header("Location: /-e-commerce-dynamique/admin/users.php");
      exit;

    } catch (Exception $e) {
      $pdo->rollBack();
      $error = "Erreur suppression : " . $e->getMessage();
    }
  }
}

/* liste */
$users = $pdo->query("
  SELECT id, fullname, email, role, created_at
  FROM users
  ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<header class="container hero">
  <h1>Utilisateurs</h1>
  <p>Liste des comptes inscrits</p>
</header>

<main class="container">
  <?php if ($error): ?>
    <div class="alert" style="margin-bottom:12px;border:1px solid rgba(255,90,90,.35);background:rgba(255,90,90,.12);">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="panel" style="padding:16px;">
    <a class="btn ghost" href="/-e-commerce-dynamique/admin/items.php" style="text-decoration:none;margin-bottom:12px;display:inline-block;">
      ← Retour articles
    </a>

    <div style="overflow:auto;">
      <table style="width:100%;border-collapse:collapse;min-width:900px;">
        <thead>
          <tr style="text-align:left;border-bottom:1px solid rgba(255,255,255,.12);">
            <th style="padding:12px 8px;">#</th>
            <th style="padding:12px 8px;">Nom</th>
            <th style="padding:12px 8px;">Email</th>
            <th style="padding:12px 8px;">Rôle</th>
            <th style="padding:12px 8px;">Créé le</th>
            <th style="padding:12px 8px;"></th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($users as $u): ?>
            <tr style="border-bottom:1px solid rgba(255,255,255,.08);">
              <td style="padding:12px 8px;"><strong><?= (int)$u['id'] ?></strong></td>
              <td style="padding:12px 8px;"><?= htmlspecialchars($u['fullname'] ?? '—') ?></td>
              <td style="padding:12px 8px;"><?= htmlspecialchars($u['email'] ?? '—') ?></td>
              <td style="padding:12px 8px;"><?= htmlspecialchars($u['role'] ?? 'user') ?></td>
              <td style="padding:12px 8px;"><?= htmlspecialchars($u['created_at'] ?? '') ?></td>
              <td style="padding:12px 8px;text-align:right;">
                <form method="post" style="margin:0;display:inline;" onsubmit="return confirm('Supprimer cet utilisateur ? Ses commandes resteront, mais seront détachées.');">
                  <input type="hidden" name="delete_user_id" value="<?= (int)$u['id'] ?>">
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
