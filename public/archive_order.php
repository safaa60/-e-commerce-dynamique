<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$userId = (int)$_SESSION['user']['id'];
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($orderId <= 0) {
  header("Location: /-e-commerce-dynamique/public/my_orders.php");
  exit;
}

// On archive seulement si la commande appartient à l'utilisateur et est livrée
$stmt = $pdo->prepare("UPDATE orders
                       SET is_archived = 1
                       WHERE id = ? AND user_id = ? AND status = 'delivered'");
$stmt->execute([$orderId, $userId]);

header("Location: /-e-commerce-dynamique/public/my_orders.php");
exit;
