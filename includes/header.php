<?php if (!isset($title)) $title = "K-Store"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/-e-commerce-dynamique/assets/css/style.css">
</head>
<body>

<nav class="navbar">
  <div class="nav-container">
    <div class="logo">K-Store KR</div>
    <ul class="nav-links">
      <li><a href="/-e-commerce-dynamique/public/items.php">Catalogue</a></li>
      <li><a href="/-e-commerce-dynamique/public/about.php">Qui sommes-nous</a></li>
      <li><a href="/-e-commerce-dynamique/public/cart.php">Panier</a></li>
      <li><a href="/-e-commerce-dynamique/public/login.php">Connexion</a></li>
      <li><a href="/-e-commerce-dynamique/public/register.php">Inscription</a></li>
    </ul>
  </div>
</nav>
