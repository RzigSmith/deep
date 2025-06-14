<?php
require_once '../includes/db.php';
require_once 'ProductManager.php';
require_once '../welcome.php';
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
    <link rel="stylesheet" href="../assets/css/sh.css">
    <link rel="stylesheet" href="../assets/css/od.css">
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="logo">Smith<span>Collection</span></div>
            <ul class="nav-links" id="navLinks">
                <li><a href="../index.php">Acceuil</a></li>
                <li><a href="/ghost/deep/classes/product.php">Boutique</a></li>

                <li><a href="/ghost/deep/classes/contact.php">Contact</a></li>
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <li><a href="/ghost/deep/logout.php">Déconnexion</a></li>
                    <li><a href="/ghost/deep/profile.php">Profil</a></li>
                <?php else: ?>
                    <li><a href="/ghost/deep/login.php">Connexion</a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
                    <li><a href="/ghost/deep/admin/orders.php">Commandes</a></li>
                <?php else: ?>

                <?php endif; ?>
            </ul>
            <div class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count"> 0</span>
            </div>
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
            <!-- <span class="close-cart"><i class="fas fa-times"></i></span> -->
            <h2>Votre Panier</h2>
            <div class="cart-content" style="display: flex;">
            </div>
            <div class="cart-total">
                <span>Total : </span>
                <span class="cart-total-amount">0$</span>
            </div>
            <a href="../api/order.php" class="btn-order" id="btnOrder" style="
    display:inline-block;
    background:#7d1a1a;
    color:#fff;
    padding:0.8rem 2rem;
    border-radius:6px;
    font-size:1.1rem;
    text-decoration:none;
    font-weight:500;
    transition:background 0.2s;
    margin-top:1rem;
">Passer la commande</a>

        </div>
    </div>


    <script src="../assets/js/ct.js"></script>
    <script src="../assets/js/od.js"></script>

</body>

</html>