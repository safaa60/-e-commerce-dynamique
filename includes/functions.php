<?php

function initCart() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function addToCart($itemId, $qty = 1) {
    initCart();
    if (isset($_SESSION['cart'][$itemId])) {
        $_SESSION['cart'][$itemId] += $qty;
    } else {
        $_SESSION['cart'][$itemId] = $qty;
    }
}

function removeFromCart($itemId) {
    initCart();
    unset($_SESSION['cart'][$itemId]);
}

function updateCartQty($itemId, $qty) {
    initCart();
    if ($qty <= 0) {
        removeFromCart($itemId);
    } else {
        $_SESSION['cart'][$itemId] = $qty;
    }
}

function clearCart() {
    $_SESSION['cart'] = [];
}

function getCartItems($pdo) {
    initCart();
    if (empty($_SESSION['cart'])) return [];

    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("SELECT * FROM items WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $items = $stmt->fetchAll();

    foreach ($items as &$item) {
        $item['qty'] = $_SESSION['cart'][$item['id']];
        $item['total'] = $item['qty'] * $item['price'];
    }

    return $items;
}

function getCartTotal($items) {
    $total = 0;
    foreach ($items as $item) {
        $total += $item['total'];
    }
    return $total;
}
