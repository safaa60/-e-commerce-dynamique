<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$title = "Inscription - K-Store";
require_once __DIR__ . '/../includes/header.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fullname = trim($_POST['fullname'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $password2 = $_POST['password2'] ?? '';

  if ($fullname === '' || strlen($fullname) < 2) $errors[] = "Nom invalide.";
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
  if (strlen($password) < 6) $errors[] = "Mot de passe trop court (min 6).";
  if ($password !== $password2) $errors[] = "Les mots de passe ne correspondent pas.";

  if (empty($errors)) {
    // email déjà utilisé ?
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
      $errors[] = "Cet email est déjà utilisé.";
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);

      $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password_hash, role)
                             VALUES (?, ?, ?, 'user')");
      $stmt->execute([$fullname, $email, $hash]);

      // auto-login après inscription
      $_SESSION['user'] = [
        'id' => (int)$pdo->lastInsertId(),
        'fullname' => $fullname,
        'email' => $email,
        'role' => 'user'
      ];

      header("Location: /-e-commerce-dynamique/public/items.php");
      exit;
    }
  }
}

$oldFullname = htmlspecialchars($_POST['fullname'] ?? '');
$oldEmail = htmlspecialchars($_POST['email'] ?? '');
?>

<header class="container hero">
  <h1>Inscription ✨</h1>
  <p>Crée ton compte pour commander et suivre tes achats.</p>
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
        Nom complet
        <input name="fullname" required value="<?= $oldFullname ?>"
               style="width:100%;padding:10px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.08);color:#fff">
      </label>

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

      <label>
        Confirmer mot de passe
        <input name="password2" type="password" required
               style="width:100%;padding:10px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.08);color:#fff">
      </label>

      <button class="btn" type="submit">Créer mon compte</button>
      <a class="btn ghost" href="/-e-commerce-dynamique/public/login.php"
         style="text-decoration:none;text-align:center">Déjà un compte ? Connexion</a>
    </form>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
