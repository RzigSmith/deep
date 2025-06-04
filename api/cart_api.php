<?php
// filepath: e:\wamp64\www\ghost\deep\api\cart_api.php
session_start();
require_once '../classes/CartManager.php';

header('Content-Type: application/json');

$cartManager = new CartManager();

$action = $_POST['action'] ?? $_GET['action'] ?? 'get';

switch ($action) {
    case 'add':
        $id = intval($_POST['id'] ?? 0);
        $qty = intval($_POST['qty'] ?? 1);
        $cart = $cartManager->addToCart($id, $qty);
        break;
    case 'update':
        $id = intval($_POST['id'] ?? 0);
        $qty = intval($_POST['qty'] ?? 1);
        $cart = $cartManager->updateCart($id, $qty);
        break;
    case 'remove':
        $id = intval($_POST['id'] ?? 0);
        $cart = $cartManager->removeFromCart($id);
        break;
    case 'get':
    default:
        $cart = $cartManager->getCart();
        break;
}
echo json_encode(['cart' => $cart]);