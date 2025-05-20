<?php

// 
session_start();
// 
try {
    $db = new PDO("mysql:host=localhost;dbname=ecommerce_db;charset=utf8", 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

$ids = array_keys($_SESSION['cart'] ?? []);
$products = [];
$total = 0;

if (!empty($ids)) {
    $ids_list = implode(',', array_map('intval', $ids));
    $products = $db->query("SELECT * FROM products WHERE id IN ($ids_list)")->fetchAll(PDO::FETCH_ASSOC);
}

// Gestion des actions +, -, supprimer
if (isset($_GET['action'], $_GET['id'])) {
    $id = intval($_GET['id']);
    if ($id > 0 && isset($_SESSION['cart'][$id])) {
        switch ($_GET['action']) {
            case 'increment':
                $_SESSION['cart'][$id]++;
                break;
            case 'decrement':
                $_SESSION['cart'][$id]--;
                if ($_SESSION['cart'][$id] <= 0) {
                    unset($_SESSION['cart'][$id]);
                }
                break;
            case 'remove':
                unset($_SESSION['cart'][$id]);
                break;
        }
    }
    // Rafraîchir la page pour éviter la répétition de l'action au rechargement
    header('Location: cart.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/panier.css">
    <title>Panier</title>
</head>

<body class="cart">
    
    <header class="cart-header">
        <nav>
            <ul>
                <li><a href="../index.php">Accueil</a></li>
                <li><a href="../classes/product.php">Boutique</a></li>
                <li><a href="cart.php" class="active">Panier</a></li>
            </ul>
        </nav>
    </header>

    <section>
        <?php if (empty($products)): ?>
            <p>Votre panier est vide</p>
        <?php else: ?>

            <?php foreach ($products as $product):
                $quantity = $_SESSION['cart'][$product['id']];
                $subtotal = $product['price'] * $quantity;
                $total += $subtotal;
            ?>

                <div class="cart-img">
                    <img src="../admin/<?= htmlspecialchars($product['image']) ?>" alt="" width="60">
                </div>
                <div class="cart-item">
                    <h3 class="cart-title"><?= htmlspecialchars($product['name']) ?></h3>
                    <p><?= number_format($product['price'], 2) ?> $</p>
                    <div class="cart-qty">
                        <a href="cart.php?action=decrement&id=<?= $product['id'] ?>" class="qty-btn">−</a>
                        <span class="qty-value"><?= $quantity ?></span>
                        <a href="cart.php?action=increment&id=<?= $product['id'] ?>" class="qty-btn">+</a>
                    </div>
                    <a href="cart.php?action=remove&id=<?= $product['id'] ?>" class="btn-delete">Supprimer</a>
                </div>
            <?php endforeach; ?>

            <div class="total">
                <strong>Quantité totale :</strong> <?= array_sum($_SESSION['cart']) ?><br>
                <strong>Prix total :</strong> <?= number_format($total, 2) ?> €
            </div>
            <div class="cart-actions" style="margin-top:2rem; text-align:right;">
                <a href="order.php" class="btn-order" >Passer la commande</a>
            </div>
        <?php endif; ?>
    </section>
</body>

</html>