<?php
require_once '../includes/db.php';
require_once 'ProductManager.php';
$db = loginDatabase();

$productManager = new ProductManager($db);
$products = $productManager->getAllProducts();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Produits</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <div class="logo">Smith<span>Collection</span></div>
                <ul class="nav-links">
                    <li><a href="../index.php">Accueil</a></li>
                    <li><a href="product.php">Boutique</a></li>
                    <div class="btn-group">
                        <li> <a href="../api/cart.php">Panier</a></li>
                    </div>
                    <li><a href="contact.php">Contact</a></li>

                </ul>
                <div class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count"> 0</span>
                </div>
            </nav>
        </div>
    </header>

    <section class="container">
        <h2 class="section-title">Nos produits </h2>
        <div class="products" id="product_list">
            <?php foreach ($products as $product): ?>

                <div class="product-card">

                    <div class="product-image">
                        <img src="../admin/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" alt="Produit 2">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                        <div class="product-price"><?= htmlspecialchars($product['price']) ?>$</div>
                        <p class="description"><?= htmlspecialchars($product['description']) ?></p>
                        <div class="btn-group">
                            <a href="../api/cart_add.php?id=<?= $product['id'] ?>">
                                <button type="button"
                                    class="add-to-cart add-to-cart-js"
                                    data-id="<?= htmlspecialchars($product['id']) ?>"
                                    data-name="<?= htmlspecialchars($product['name']) ?>"
                                    data-price="<?= htmlspecialchars($product['price']) ?>"
                                    data-image="<?= htmlspecialchars($product['image']) ?>">
                                    Ajouter au panier
                                </button></a>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>

    </section>

    <div class="cart-overlay">
        <div class="cart">
            <span class="close-cart"><i class="fas fa-times"></i></span>
            <h2>Votre Panier</h2>
            <div class="cart-content">
                <!-- Les articles du panier seront ajoutés ici dynamiquement -->

                <!-- <script>
                    cartItem.innerHTML =

                        cartContent.appendChild(cartItem);
                </script> -->

            </div>
            <div class="cart-total">
                <span>Total : </span>
                <span class="cart-total-amount">0$</span>
            </div>
            <button class="checkout-btn">Passer la commande</button>
        </div>
    </div>

    <script src="../assets/js/cart.js"></script>

</body>

</html>