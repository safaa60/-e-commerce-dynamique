<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$action = $_POST['action'] ?? '';
$key = (string)($_POST['key'] ?? '');         // ex: "12:5"
$id  = (int)($_POST['id'] ?? 0);
$sizeId = (isset($_POST['size_id']) && $_POST['size_id'] !== '') ? (int)$_POST['size_id'] : null;

initCart();

/* Si on ne reçoit pas key, on la reconstruit */
if ($key === '' && $id > 0) {
  $key = cartKey($id, $sizeId);
}

if ($key === '') {
  header("Location: cart.php");
  exit;
}

switch ($action) {
  case 'plus':
    // ajoute +1 sur la même ligne (item + size)
    addToCart($pdo, $id > 0 ? $id : (int)($_SESSION['cart'][$key]['item_id'] ?? 0), 1, (int)($_SESSION['cart'][$key]['size_id'] ?? $sizeId));
    break;

  case 'minus':
    $current = (int)($_SESSION['cart'][$key]['qty'] ?? 0);
    updateCartQty($pdo, $key, $current - 1);
    break;

  case 'remove':
    removeFromCart($key);
    break;
}

header("Location: cart.php");
exit;
