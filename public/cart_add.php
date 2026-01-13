<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = (int)($_POST['id'] ?? 0);
$sizeId = (isset($_POST['size_id']) && $_POST['size_id'] !== '') ? (int)$_POST['size_id'] : null;

if ($id <= 0) {
  header("Location: items.php");
  exit;
}

addToCart($pdo, $id, 1, $sizeId);

header("Location: cart.php");
exit;

