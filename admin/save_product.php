<?php
session_start(); // Démarre la session
 require_once  dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'db.php';
$db = loginDatabase(); // Connexion à la base de données

// Ajout des produits 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $price = floatval($_POST['price']);
    // $categoryId = $_POST['category_id'] ? intval($_POST['category_id']) : null;

    // Upload de l'image
    $image = 'uploads/default.png'; // Image par défaut
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
        $imagePath = $uploadDir . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        $image = $imagePath;
    }

    // Insertion en base de données
    $stmt = $db->prepare("
        INSERT INTO products (name, description, price, image )
        VALUES (?, ?, ?, ? )
    ");
    $stmt->execute([$name, $description, $price, $image]);

    header("Location: admin.php?success=1");
    exit;
}