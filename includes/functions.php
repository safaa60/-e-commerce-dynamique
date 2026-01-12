<?php

function initCart() {
  if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }
}

/**
 * Retourne stock dispo pour un item
 */
function getItemStock(PDO $pdo, int $itemId): int {
  $stmt = $pdo->prepare("SELECT stock FROM items WHERE id = ? AND is_active = 1");
  $stmt->execute([$itemId]);
  $row = $stmt->fetch();
  return $row ? (int)$row['stock'] : 0;
}

/**
 * Ajoute au panier sans dépasser le stock
 * Retourne un message d'erreur (string) ou null si OK
 */
function addToCart(PDO $pdo, int $itemId, int $qty = 1): ?string {
  initCart();
  $qty = max(1, $qty);

  $stock = getItemStock($pdo, $itemId);
  if ($stock <= 0) {
    return "Produit épuisé.";
  }

  $current = isset($_SESSION['cart'][$itemId]) ? (int)$_SESSION['cart'][$itemId] : 0;
  $newQty = $current + $qty;

  if ($newQty > $stock) {
    // on bloque à stock max
    $_SESSION['cart'][$itemId] = $stock;
    return "Stock limité : quantité ajustée à $stock.";
  }

  $_SESSION['cart'][$itemId] = $newQty;
  return null;
}

function removeFromCart($itemId) {
  initCart();
  unset($_SESSION['cart'][$itemId]);
}

/**
 * Met à jour quantité sans dépasser stock
 * Retourne message erreur ou null
 */
function updateCartQty(PDO $pdo, int $itemId, int $qty): ?string {
  initCart();
  $qty = (int)$qty;

  if ($qty <= 0) {
    removeFromCart($itemId);
    return null;
  }

  $stock = getItemStock($pdo, $itemId);
  if ($stock <= 0) {
    removeFromCart($itemId);
    return "Produit épuisé : retiré du panier.";
  }

  if ($qty > $stock) {
    $_SESSION['cart'][$itemId] = $stock;
    return "Stock limité : quantité ajustée à $stock.";
  }

  $_SESSION['cart'][$itemId] = $qty;
  return null;
}

function clearCart() {
  $_SESSION['cart'] = [];
}

function getCartItems(PDO $pdo) {
  initCart();
  if (empty($_SESSION['cart'])) return [];

  $ids = array_keys($_SESSION['cart']);
  $placeholders = implode(',', array_fill(0, count($ids), '?'));

  $stmt = $pdo->prepare("SELECT * FROM items WHERE id IN ($placeholders)");
  $stmt->execute($ids);
  $items = $stmt->fetchAll();

  foreach ($items as &$item) {
    $qty = (int)($_SESSION['cart'][$item['id']] ?? 0);
    $item['qty'] = $qty;
    $item['total'] = $qty * (float)$item['price'];
  }

  return $items;
}

function getCartTotal($items) {
  $total = 0;
  foreach ($items as $item) {
    $total += (float)$item['total'];
  }
  return $total;
}
