<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function requireAdmin() {
  if (!isset($_SESSION['user'])) {
    header("Location: /-e-commerce-dynamique/public/login.php");
    exit;
  }
  if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo "Accès refusé (admin seulement).";
    exit;
  }
}
