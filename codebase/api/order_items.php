<?php
require_once '../includes/db.php';
require_once '../welcome.php';
$db = loginDatabase();

$total = $_GET['order_items'] ?? '';
$errors = [];
$success = false;

// Protéger l'accès
if (!isset($_SESSION['user_id'], $_SESSION['cart'], $_SESSION['order_id'])) {
    header('Location: ../login.php');
    exit;
}

// Traitement lors de la confirmation de commande
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_SESSION['order_id'];
    $cart = $_SESSION['cart'];

    foreach ($cart as $product_id => $quantity) {
        // Sécuriser les données
        $product_id = intval($product_id);
        $quantity = intval($quantity);

        // Récupérer le prix du produit
        $stmt = $db->prepare('SELECT price FROM products WHERE id = ?');
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $errors[] = "Produit introuvable pour l'ID $product_id.";
            continue;
        }
        $price = $product['price'];

        // Valider la quantité
        if ($quantity <= 0) {
            $errors[] = "Quantité invalide pour le produit ID $product_id.";
            continue;
        }

        // Enregistrer dans la base de données
        $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        if (!$stmt->execute([$order_id, $product_id, $quantity, $price])) {
            $errors[] = "Erreur lors de la confirmation de la commande pour le produit $product_id.";
        }
    }

    if (empty($errors)) {
        // Mettre à jour le statut de la commande
        $stmt = $db->prepare("UPDATE orders SET status = 'En cours' WHERE id = ?");
        $stmt->execute([$order_id]);

        // Vider le panier
        unset($_SESSION['cart']);

        $success = true;
        $_SESSION['order_id'] = $db->lastInsertId();

        // Après création de la commande
        $stmt = $db->prepare("INSERT INTO notifications (user_id, type, message) VALUES (?, 'confirmation', ?)");
        $stmt->execute([$client_id, "Votre commande a bien été enregistrée."]);

        header('Location: ../order.php?order_items=' . $total_amount . '&order_id=' . $order_id . urlencode($total));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>order_items</title>
    <link rel="stylesheet" href="../assets/css/od.css">
</head>

<body>
    <header>
    <nav class="navbar">
        <div class="logo">Smith<span>Collection</span></div>
        <ul class="nav-links" id="navLinks">
            <li><a href="/ghost/deep/classes/product.php">Boutique</a></li>
            <li><a href="#">Nouveautés</a></li>
            <li><a href="/ghost/deep/classes/contact.php">Contact</a></li>
            <?php if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true): ?>
                <li><a href="/ghost/deep/login.php">Connexion</a></li>
            <?php elseif (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
                <li><a href="/ghost/deep/admin/orders.php">Commandes</a></li>
            <?php else: ?>
                <li><a href="/ghost/deep/profile.php">Profil</a></li>
            <?php endif; ?>
        </ul>
        <div class="notify-bell" id="notifyBell">
            <i class="fas fa-bell"></i>
            <span class="notify-count" id="notifyCount"></span>
            <div class="notify-dropdown" id="notifyDropdown"></div>
        </div>
        <div class="burger" id="burgerMenu">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    </header>

    <form action="order_items.php?order_items=<?= htmlspecialchars($total) ?>" method="post" id="orderForm">
        <h2>Confirmer votre commande</h2>
        <label for="order_id">Id commande</label>
        <input type="text" name="order_id" value="<?= htmlspecialchars($_SESSION['order_id']) ?>" readonly>
        <label for="product_id">Ids des produits</label>
        <input type="text" name="product_id" value="<?= htmlspecialchars(implode(', ', array_keys($_SESSION['cart']))) ?>" readonly>
        <label for="quantity">Quantité totale</label>
        <input type="text" name="quantity" value="<?= htmlspecialchars(array_sum($_SESSION['cart'])) ?>" readonly>
        <label for="price">Prix total</label>
        <input type="text" name="price" value="<?= htmlspecialchars($total) ?>" readonly>
        <button type="submit">Confirmer la commande</button>
    </form>
    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif ($success): ?>
        <div class="success">Commande confirmée avec succès !</div>
    <?php endif; ?>
    
    <script src="../assets/js/ct.js"></script>
</body>

</html>