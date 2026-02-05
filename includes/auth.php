<?php
function requireLogin() {
  if (!isset($_SESSION['user'])) {
    header("Location: /-e-commerce-dynamique/public/login.php");
    exit;
  }
}

function isAdmin(): bool {
  return isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'admin';
}

// Vérifie si un utilisateur est connecté via la session Sinon, redirige vers la page de connexion