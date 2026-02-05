<?php

function isLoggedIn(): bool {
  return isset($_SESSION['user']) && is_array($_SESSION['user']);
}
// Retourne true si la session contient un utilisateur connecté

function isAdmin(): bool {
  return isLoggedIn() && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

function requireAdmin(): void {
  if (!isAdmin()) {
    header("Location: /-e-commerce-dynamique/public/login.php");
    exit;
  }
}

//Bloquer l’accès si pas admin