<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$title = "Réinitialiser le mot de passe - K-Store";
require_once __DIR__ . '/../includes/header.php';

$token = trim($_GET['token'] ?? '');
$errors = [];
$ok = null;

if ($token === '') {
  $errors[] = "Token manquant.";
} else {
  $stmt = $pdo->prepare("SELECT id, reset_expires FROM users WHERE reset_token = ? LIMIT 1");
  $stmt->execute([$token]);
  $u = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$u) {
    $errors[] = "Lien invalide.";
  } else {
    $expires = strtotime($u['reset_expires'] ?? '1970-01-01');
    if ($expires < time()) {
      $errors[] = "Lien expiré. Refais une demande.";
    }
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
  $p1 = $_POST['password'] ?? '';
  $p2 = $_POST['password2'] ?? '';

  if (strlen($p1) < 6) $errors[] = "Mot de passe trop court (min 6).";
  if ($p1 !== $p2) $errors[] = "Les mots de passe ne correspondent pas.";

  if (empty($errors)) {
    $hash = password_hash($p1, PASSWORD_DEFAULT);
    $upd = $pdo->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
    $upd->execute([$hash, (int)$u['id']]);

    $ok = "Mot de passe modifié ✅ Tu peux te connecter.";
  }
}
?>

<header class="container hero">
  <h1>Réinitialiser le mot de passe 🔐</h1>
  <p>Choisis un nouveau mot de passe.</p>
</header>

<main class="container">
  <div class="panel" style="max-width:560px;margin:auto;">

    <?php if (!empty($errors)): ?>
      <div class="callout" style="border-color:rgba(255,91,91,.35);background:rgba(255,91,91,.12)">
        <strong>Erreur :</strong>
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($ok): ?>
      <div class="callout" style="border-color:rgba(72,255,164,.25);background:rgba(72,255,164,.10)">
        <?= htmlspecialchars($ok) ?>
        <div style="margin-top:10px;">
          <a class="btn" href="/-e-commerce-dynamique/public/login.php" style="text-decoration:none;">Connexion</a>
        </div>
      </div>
    <?php endif; ?>

    <?php if (empty($errors) && !$ok): ?>
    <form method="post" style="display:grid;gap:12px;margin-top:10px;">
      <label>
        Nouveau mot de passe
        <input name="password" type="password" required
               style="width:100%;padding:10px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.08);color:#fff">
      </label>

      <label>
        Confirmer
        <input name="password2" type="password" required
               style="width:100%;padding:10px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.08);color:#fff">
      </label>

      <button class="btn" type="submit">Valider</button>
    </form>
    <?php endif; ?>

  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
