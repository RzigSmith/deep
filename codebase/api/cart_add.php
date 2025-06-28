<?php
session_start();

try {
    $db = new PDO("mysql:host=localhost;dbname=ecommerce_db;charset=utf8", 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($id <= 0) die("ID produit invalide");

    // Vérifier que le produit existe
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) die("Ce produit n'existe pas");

    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++;
    } else {
        $_SESSION['cart'][$id] = 1;
    }

    header("Location: ../classes/product.php");
    exit;
} else {
    die("Aucun produit sélectionné");
}
