<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$action = $_POST['action'] ?? '';
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
  header("Location: cart.php");
  exit;
}

initCart();

switch ($action) {
  case 'plus':
    addToCart($id, 1);
    break;

  case 'minus':
    $current = $_SESSION['cart'][$id] ?? 0;
    updateCartQty($id, (int)$current - 1);
    break;

  case 'remove':
    removeFromCart($id);
    break;

  default:
    // rien
    break;
}

header("Location: cart.php");
exit;
