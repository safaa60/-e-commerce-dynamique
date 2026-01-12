<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$title = "Connexion - K-Store";
require_once __DIR__ . '/../includes/header.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
  if ($password === '') $errors[] = "Mot de passe requis.";

  if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT id, fullname, email, password_hash, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
      $errors[] = "Email ou mot de passe incorrect.";
    } else {
      $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'fullname' => $user['fullname'],
        'email' => $user['email'],
        'role' => $user['role']
      ];
      header("Location: /-e-commerce-dynamique/public/items.php");
      exit;
    }
  }
}

$oldEmail = htmlspecialchars($_POST['email'] ?? '');
?>

<header class="container hero">
  <h1>Connexion 🔐</h1>
  <p>Connecte-toi pour accéder à tes commandes.</p>
</header>

<main class="container">
  <div class="panel" style="max-width:560px;margin:auto;">
    <?php if (!empty($errors)): ?>
      <div class="callout" style="border-color:rgba(255,91,91,.35);background:rgba(255,91,91,.12)">
        <strong>Corrige :</strong>
        <ul>
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" style="display:grid;gap:12px;margin-top:10px;">
      <label>
        Email
        <input name="email" type="email" required value="<?= $oldEmail ?>"
               style="width:100%;padding:10px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.08);color:#fff">
      </label>

      <label>
        Mot de passe
        <input name="password" type="password" required
               style="width:100%;padding:10px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.08);color:#fff">
      </label>

      <button class="btn" type="submit">Se connecter</button>
      <a class="btn ghost" href="/-e-commerce-dynamique/public/register.php"
         style="text-decoration:none;text-align:center">Créer un compte</a>
    </form>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
