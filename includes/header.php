<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($title) ? htmlspecialchars($title) : 'K-Store' ?></title>
  <link rel="stylesheet" href="/-e-commerce-dynamique/assets/css/style.css">
</head>
<body>

<header class="topbar">
  <div class="topbar-inner container">
    <a class="brand" href="/-e-commerce-dynamique/public/items.php">
      <span class="brand-name">K-Store KR</span>
    </a>

    <nav class="nav">
      <a href="/-e-commerce-dynamique/public/items.php">Catalogue</a>
      <a href="/-e-commerce-dynamique/public/about.php">Qui sommes-nous</a>
      <a href="/-e-commerce-dynamique/public/cart.php">Panier</a>
      <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
      <a href="/-e-commerce-dynamique/admin/orders.php">Admin</a>
      <?php endif; ?>


      <?php if (isset($_SESSION['user'])): ?>
        <a href="/-e-commerce-dynamique/public/my_orders.php">Mes commandes</a>

        <div class="profile">
          <span class="avatar">👤</span>
          <span class="profile-name"><?= htmlspecialchars($_SESSION['user']['fullname'] ?? 'Mon compte') ?></span>
        </div>

        <a class="nav-cta" href="/-e-commerce-dynamique/public/logout.php">Déconnexion</a>
      <?php else: ?>
        <a href="/-e-commerce-dynamique/public/login.php">Connexion</a>
        <a class="nav-cta" href="/-e-commerce-dynamique/public/register.php">Inscription</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
