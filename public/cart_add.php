<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = (int)($_POST['id'] ?? 0);
$qty = max(1, (int)($_POST['qty'] ?? 1));
$sizeId = (isset($_POST['size_id']) && $_POST['size_id'] !== '') ? (int)$_POST['size_id'] : null;

if ($id <= 0) {
  header("Location: items.php");
  exit;
}

initCart();

$msg = addToCart($pdo, $id, $qty, $sizeId);
if ($msg !== null) {
  $_SESSION['flash_cart'] = $msg;
}

header("Location: cart.php");
exit;
