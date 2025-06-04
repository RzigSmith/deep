<?php
// filepath: e:\wamp64\www\ghost\deep\api\products_api.php
require_once '../includes/db.php';
require_once '../classes/ProductManager.php';

header('Content-Type: application/json');

$productManager = new ProductManager($db);

if (isset($_GET['id'])) {
    $product = $productManager->getProductById($_GET['id']);
    echo json_encode($product);
} else {
    $products = $productManager->getAllProducts();
    echo json_encode($products);
}