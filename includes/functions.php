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

/** prendre une taille "par défaut" (la 1ère dispo) */
function getDefaultSizeIdForItem(PDO $pdo, int $itemId): ?int {
  $stmt = $pdo->prepare("
    SELECT size_id
    FROM item_sizes
    WHERE item_id = ?
    ORDER BY size_id ASC
    LIMIT 1
  ");
  $stmt->execute([$itemId]);
  $sizeId = $stmt->fetchColumn();
  return $sizeId ? (int)$sizeId : null;
}

function getSizeCode(PDO $pdo, int $sizeId): ?string {
  $stmt = $pdo->prepare("SELECT code FROM sizes WHERE id = ? LIMIT 1");
  $stmt->execute([$sizeId]);
  $code = $stmt->fetchColumn();
  return $code ? (string)$code : null;
}

/**
 * Ajout panier (avec taille optionnelle)
 * ✅ IMPORTANT : si le produit a des tailles et que sizeId n'est pas fourni,
 * on choisit automatiquement la 1ère taille dispo.
 * Retourne un message ou null si OK.
 */
function addToCart(PDO $pdo, int $itemId, int $qty = 1, ?int $sizeId = null): ?string {
  initCart();
  $qty = max(1, (int)$qty);

  $stock = getItemStock($pdo, $itemId);
  if ($stock <= 0) return "Produit épuisé.";

  $infoMsg = null;

  if (itemHasSizes($pdo, $itemId)) {
    // ✅ auto-size si rien fourni
    if (!$sizeId) {
      $sizeId = getDefaultSizeIdForItem($pdo, $itemId);
      if (!$sizeId) return "Tailles indisponibles pour ce produit.";
      $code = getSizeCode($pdo, $sizeId);
      // tu peux mettre $infoMsg = null; si tu ne veux aucun message
      $infoMsg = $code ? "Taille sélectionnée automatiquement : $code." : "Taille sélectionnée automatiquement.";
    } else {
      if (!sizeIsValidForItem($pdo, $itemId, $sizeId)) return "Taille / pointure invalide.";
    }
  } else {
    // pas de tailles -> on ignore
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

  return $infoMsg; // peut être null
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
/**
 * Liste des tailles disponibles pour un produit
 * Retour: [ ['id'=>1,'code'=>'S'], ... ]
 */
function getItemSizes(PDO $pdo, int $itemId): array {
  $stmt = $pdo->prepare("
    SELECT s.id, s.code
    FROM item_sizes isz
    JOIN sizes s ON s.id = isz.size_id
    WHERE isz.item_id = ?
    ORDER BY s.code
  ");
  $stmt->execute([$itemId]);
  return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

/**
 * Change la taille d'une ligne du panier (key -> newKey)
 * - conserve la quantité
 * - fusionne si la newKey existe déjà
 * - respecte le stock
 */
function changeCartSize(PDO $pdo, string $key, ?int $newSizeId): ?string {
  initCart();
  if (!isset($_SESSION['cart'][$key])) return null;

  $itemId = (int)$_SESSION['cart'][$key]['item_id'];
  $qty    = (int)$_SESSION['cart'][$key]['qty'];

  // Si produit n'a pas de tailles, on ignore
  if (!itemHasSizes($pdo, $itemId)) {
    return null;
  }

  // Taille obligatoire et valide
  if (!$newSizeId) return "Veuillez choisir une taille.";
  if (!sizeIsValidForItem($pdo, $itemId, $newSizeId)) return "Taille invalide.";

  $newKey = cartKey($itemId, $newSizeId);

  // Si on ne change pas vraiment
  if ($newKey === $key) return null;

  // Stock dispo
  $stock = getItemStock($pdo, $itemId);
  if ($stock <= 0) {
    unset($_SESSION['cart'][$key]);
    return "Produit épuisé : retiré du panier.";
  }

  // Fusion si la ligne existe déjà
  $existingQty = isset($_SESSION['cart'][$newKey]) ? (int)$_SESSION['cart'][$newKey]['qty'] : 0;
  $mergedQty = $existingQty + $qty;

  if ($mergedQty > $stock) {
    $mergedQty = $stock;
    // on retire l'ancienne ligne quoi qu'il arrive
    unset($_SESSION['cart'][$key]);
    $_SESSION['cart'][$newKey] = ['item_id'=>$itemId,'size_id'=>$newSizeId,'qty'=>$mergedQty];
    return "Stock limité : quantité ajustée à $stock.";
  }

  // appliquer
  unset($_SESSION['cart'][$key]);
  $_SESSION['cart'][$newKey] = ['item_id'=>$itemId,'size_id'=>$newSizeId,'qty'=>$mergedQty];

  return null;
}
