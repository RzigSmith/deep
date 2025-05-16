<?php
require_once '../includes/config.php';


// Vérifier si la requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $product_id = $data['product_id'];
    $name = $data['name'];
    $price = $data['price'];
    $image = $data['image'];
    $quantity = $data['quantity'];

    // Vérifier si le produit existe déjà dans le panier pour cet utilisateur
    $stmt = $db->prepare("SELECT id FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user']['id'], $product_id]);
    $existingCartItem = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingCartItem) {
        // Mettre à jour la quantité si le produit existe déjà
        $stmt = $db->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$quantity, $_SESSION['user']['id'], $product_id]);
    } else {
        // Insérer un nouveau produit dans le panier
        $stmt = $db->prepare("INSERT INTO cart (user_id, product_id, quantity, price, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user']['id'], $product_id, $quantity, $price, $image]);
    }

    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Requête invalide']);
exit;