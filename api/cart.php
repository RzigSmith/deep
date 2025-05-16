<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../classes/CartManager.php';


try {
    // Vérifier le CSRF token
    // if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || $_SERVER['HTTP_X_CSRF_TOKEN'] !== ($_SESSION['csrf_token'] ?? '')) {
    //     throw new Exception('Token CSRF invalide', 403);
    // }

    $cartManager = new CartManager($db); // Passer la connexion PDO au CartManager
    $userId = $_SESSION['user_id'] ?? null;
// Vérifier si l'utilisateur est connecté, sinon rediriger vers la page de connexion
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// Traitement du formulaire de mise à jour
 
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
$errors = [];
$success = [];



    // Gérer les différentes méthodes HTTP
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET': // Récupérer les articles du panier
            $cartItems = $userId ? $cartManager->getCart($userId) : [];
            echo json_encode(['success' => true, 'data' => $cartItems]);
            break;

        case 'POST': // Ajouter un article au panier
            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['product_id']) || empty($data['quantity'])) {
                throw new Exception('Données invalides', 400);
            }

            $result = $cartManager->addToCart($userId, $data['product_id'], (int)$data['quantity']);
            echo json_encode(['success' => $result]);
            break;

        case 'PUT': // Mettre à jour la quantité d'un article dans le panier
            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['product_id']) || empty($data['quantity'])) {
                throw new Exception('Données invalides', 400);
            }

            $result = $cartManager->updateCartItem($userId, $data['product_id'], (int)$data['quantity']);
            echo json_encode(['success' => $result]);
            break;

        case 'DELETE': // Supprimer un article du panier
            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['product_id'])) {
                throw new Exception('Données invalides', 400);
            }

            $result = $cartManager->removeFromCart($userId, $data['product_id']);
            echo json_encode(['success' => $result]);
            break;

        default:
            throw new Exception('Méthode HTTP non autorisée', 405);
    }
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/cart.css">
</head>
<body>
    <div class="cart-container">
        <h1 class="section-title">Mon Panier</h1>

        <?php if (empty($cartItems)): ?>
            <p class="empty-cart">Votre panier est vide.</p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix</th>
                        <th>Quantité</th>
                        <th>Sous-total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-image">
                                <?= htmlspecialchars($item['name']) ?>
                            </td>
                            <td><?= number_format($item['price'], 2) ?> €</td>
                            <td>
                                <form method="POST" action="cart.php" class="update-form">
                                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="quantity-input">
                                    <button type="submit" name="update_cart" class="btn-update">Mettre à jour</button>
                                </form>
                            </td>
                            <td><?= number_format($item['price'] * $item['quantity'], 2) ?> €</td>
                            <td>
                                <form method="POST" action="cart.php" class="delete-form">
                                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                    <button type="submit" name="delete_item" class="btn-delete">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-total">
                <h3>Total : <?= number_format($total, 2) ?> €</h3>
                <a href="checkout.php" class="btn-checkout">Passer à la caisse</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>