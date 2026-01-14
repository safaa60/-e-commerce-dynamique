<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$logged = isset($_SESSION['user']);
$isAdmin = $logged && (($_SESSION['user']['role'] ?? '') === 'admin');
$fullname = $logged ? ($_SESSION['user']['fullname'] ?? 'Profil') : null;

$current = $_SERVER['REQUEST_URI'] ?? '';

function navActive(string $path): string {
  global $current;
  return (strpos($current, $path) !== false) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title ?? 'K-Store') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/-e-commerce-dynamique/assets/css/style.css">
</head>
<body>

<nav class="topbar">
  <div class="container nav-inner">

    <a class="brand" href="/-e-commerce-dynamique/public/items.php">K-Store KR</a>

    <div class="nav-links">
      <a class="<?= navActive('/public/items.php') ?>" href="/-e-commerce-dynamique/public/items.php">Catalogue</a>
      <a class="<?= navActive('/public/explorer.php') ?>" href="/-e-commerce-dynamique/public/explorer.php">Explorer</a>
      <a class="<?= navActive('/public/about.php') ?>" href="/-e-commerce-dynamique/public/about.php">À propos</a>
      <a class="<?= navActive('/public/cart.php') ?>" href="/-e-commerce-dynamique/public/cart.php">Panier</a>

      <?php if ($logged): ?>
        <a class="<?= navActive('/public/my_orders.php') ?>" href="/-e-commerce-dynamique/public/my_orders.php">Mes commandes</a>
      <?php endif; ?>

      <?php if ($isAdmin): ?>
        <a class="<?= navActive('/public/admin_orders.php') ?>" href="/-e-commerce-dynamique/public/admin_orders.php">Admin commandes</a>
        <a class="<?= navActive('/admin/items.php') ?>" href="/-e-commerce-dynamique/admin/items.php">Admin stock</a>
      <?php endif; ?>

      <?php if ($logged): ?>
        <span class="chip"><?= htmlspecialchars($fullname) ?></span>
        <a class="btn small" href="/-e-commerce-dynamique/public/logout.php">Déconnexion</a>
      <?php else: ?>
        <a class="<?= navActive('/public/login.php') ?>" href="/-e-commerce-dynamique/public/login.php">Connexion</a>
        <a class="<?= navActive('/public/register.php') ?>" href="/-e-commerce-dynamique/public/register.php">Inscription</a>
      <?php endif; ?>
    </div>

  </div>
</nav>
