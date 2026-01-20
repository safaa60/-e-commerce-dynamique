<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$action = $_POST['action'] ?? '';        // add | plus | minus | remove
$id     = (int)($_POST['id'] ?? 0);      // item id
$qty    = max(1, (int)($_POST['qty'] ?? 1));

$sizeId = (isset($_POST['size_id']) && $_POST['size_id'] !== '') ? (int)$_POST['size_id'] : null;

$key = (string)($_POST['key'] ?? '');    // ex: "12:5"

initCart();

/* Si on ne reçoit pas key, on la reconstruit */
if ($key === '' && $id > 0) {
  $key = cartKey($id, $sizeId);
}

switch ($action) {

  case 'add': {
    if ($id <= 0) {
      header("Location: items.php");
      exit;
    }
    $msg = addToCart($pdo, $id, $qty, $sizeId);
    if ($msg !== null) {
      $_SESSION['flash_cart'] = $msg;
    }
    header("Location: cart.php");
    exit;
  }

  case 'plus': {
    // +1 sur la ligne existante
    if ($key === '' || !isset($_SESSION['cart'][$key])) {
      header("Location: cart.php"); exit;
    }
    $itemIdToAdd = (int)($_SESSION['cart'][$key]['item_id'] ?? 0);
    $sizeIdToAdd = $_SESSION['cart'][$key]['size_id'] ?? null;
    addToCart($pdo, $itemIdToAdd, 1, $sizeIdToAdd ? (int)$sizeIdToAdd : null);
    header("Location: cart.php"); exit;
  }

  case 'minus': {
    if ($key === '' || !isset($_SESSION['cart'][$key])) {
      header("Location: cart.php"); exit;
    }
    $current = (int)($_SESSION['cart'][$key]['qty'] ?? 0);
    updateCartQty($pdo, $key, $current - 1);
    header("Location: cart.php"); exit;
  }

  case 'remove': {
    if ($key !== '') removeFromCart($key);
    header("Location: cart.php"); exit;
  }

  default:
    header("Location: cart.php");
    exit;
}
