<?php
// require_once realpath(__DIR__ . 'Database.php'); // Ensure the correct path to Database.php
$db= new PDO("mysql:host=localhost;dbname=ecommerce_db", "root","");
class CartManager {
    public function __construct() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function getCart() {
        return $_SESSION['cart'];
    }

    public function addToCart($id, $qty = 1) {
        if (!isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] = 0;
        }
        $_SESSION['cart'][$id] += $qty;
        return $_SESSION['cart'];
    }

    public function updateCart($id, $qty) {
        if ($qty > 0) {
            $_SESSION['cart'][$id] = $qty;
        } else {
            unset($_SESSION['cart'][$id]);
        }
        return $_SESSION['cart'];
    }

    public function removeFromCart($id) {
        unset($_SESSION['cart'][$id]);
        return $_SESSION['cart'];
    }
}