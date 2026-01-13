<?php

function initCart(): void {
  if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // key => ['item_id'=>..,'size_id'=>..,'qty'=>..]
  }
}

/** clé unique : "12" ou "12:5" (5 = size_id) */
function cartKey(int $itemId, ?int $sizeId): string {
  return $sizeId ? ($itemId . ':' . $sizeId) : (string)$itemId;
}

/** stock dispo */
function getItemStock(PDO $pdo, int $itemId): int {
  $stmt = $pdo->prepare("SELECT stock FROM items WHERE id = ? AND is_active = 1");
  $stmt->execute([$itemId]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  return $row ? (int)$row['stock'] : 0;
}

/** le produit a-t-il des tailles ? */
function itemHasSizes(PDO $pdo, int $itemId): bool {
  $stmt = $pdo->prepare("SELECT 1 FROM item_sizes WHERE item_id = ? LIMIT 1");
  $stmt->execute([$itemId]);
  return (bool)$stmt->fetchColumn();
}

/** vérifier que sizeId appartient bien au produit */
function sizeIsValidForItem(PDO $pdo, int $itemId, int $sizeId): bool {
  $stmt = $pdo->prepare("SELECT 1 FROM item_sizes WHERE item_id = ? AND size_id = ? LIMIT 1");
  $stmt->execute([$itemId, $sizeId]);
  return (bool)$stmt->fetchColumn();
}

/**
 * Ajout panier (avec taille optionnelle)
 * Retourne un message ou null si OK
 */
function addToCart(PDO $pdo, int $itemId, int $qty = 1, ?int $sizeId = null): ?string {
  initCart();
  $qty = max(1, (int)$qty);

  $stock = getItemStock($pdo, $itemId);
  if ($stock <= 0) return "Produit épuisé.";

  // si le produit a des tailles, taille obligatoire
  if (itemHasSizes($pdo, $itemId)) {
    if (!$sizeId) return "Veuillez choisir une taille / pointure.";
    if (!sizeIsValidForItem($pdo, $itemId, $sizeId)) return "Taille / pointure invalide.";
  } else {
    // pas de tailles -> on ignore size_id
    $sizeId = null;
  }

  $key = cartKey($itemId, $sizeId);
  $current = isset($_SESSION['cart'][$key]) ? (int)$_SESSION['cart'][$key]['qty'] : 0;
  $newQty = $current + $qty;

  if ($newQty > $stock) {
    $newQty = $stock;
    $_SESSION['cart'][$key] = ['item_id'=>$itemId,'size_id'=>$sizeId,'qty'=>$newQty];
    return "Stock limité : quantité ajustée à $stock.";
  }

  $_SESSION['cart'][$key] = ['item_id'=>$itemId,'size_id'=>$sizeId,'qty'=>$newQty];
  return null;
}

/** supprimer une ligne panier (clé) */
function removeFromCart(string $key): void {
  initCart();
  unset($_SESSION['cart'][$key]);
}

/** vider */
function clearCart(): void {
  $_SESSION['cart'] = [];
}

/**
 * Update quantité par KEY (ex: "12:5")
 * Retourne message ou null
 */
function updateCartQty(PDO $pdo, string $key, int $qty): ?string {
  initCart();
  if (!isset($_SESSION['cart'][$key])) return null;

  $qty = (int)$qty;
  if ($qty <= 0) {
    unset($_SESSION['cart'][$key]);
    return null;
  }

  $itemId = (int)$_SESSION['cart'][$key]['item_id'];
  $stock = getItemStock($pdo, $itemId);

  if ($stock <= 0) {
    unset($_SESSION['cart'][$key]);
    return "Produit épuisé : retiré du panier.";
  }

  if ($qty > $stock) {
    $_SESSION['cart'][$key]['qty'] = $stock;
    return "Stock limité : quantité ajustée à $stock.";
  }

  $_SESSION['cart'][$key]['qty'] = $qty;
  return null;
}

/**
 * Récupère les items du panier + code taille
 */
function getCartItems(PDO $pdo): array {
  initCart();
  if (empty($_SESSION['cart'])) return [];

  $lines = [];

  foreach ($_SESSION['cart'] as $key => $line) {
    $itemId = (int)$line['item_id'];
    $sizeId = !empty($line['size_id']) ? (int)$line['size_id'] : null;
    $qty    = (int)$line['qty'];

    $stmt = $pdo->prepare("
      SELECT i.id, i.name, i.price, i.stock, i.image,
             c.name AS category
      FROM items i
      LEFT JOIN categories c ON c.id = i.category_id
      WHERE i.id = ? AND i.is_active = 1
      LIMIT 1
    ");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$item) continue;

    $sizeCode = null;
    if ($sizeId) {
      $s = $pdo->prepare("SELECT code FROM sizes WHERE id = ? LIMIT 1");
      $s->execute([$sizeId]);
      $sizeCode = $s->fetchColumn() ?: null;
    }

    $item['cart_key'] = $key;
    $item['size_id']  = $sizeId;
    $item['size_code']= $sizeCode;
    $item['qty']      = $qty;
    $item['total']    = $qty * (float)$item['price'];
    $item['image']    = $item['image'] ?: 'placeholder.jpg';

    $lines[] = $item;
  }

  return $lines;
}

function getCartTotal(array $items): float {
  $total = 0;
  foreach ($items as $item) $total += (float)$item['total'];
  return $total;
}
