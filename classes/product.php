<?php
require_once '../includes/config.php';

if (isset($_POST['add-to-cart'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price =    $_POST['price'];
}


  //Récuperation des produits pour affichage
$products = $db->query("
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN products c ON p.description")-> fetchAll(PDO::FETCH_ASSOC);

    // Récupérer toutes les catégories
$description = $db->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
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

                    <li><a href="#">Contact</a></li>
                    
                </ul>
                <div class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
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
                    <img src="../admin/<?=htmlspecialchars($product['image'])?>" alt="<?= htmlspecialchars($product['name']) ?>" alt="Produit 2">
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                    <div class="product-price"><?=htmlspecialchars($product['price']) ?>$</div>
                    <p class="description"><?=htmlspecialchars($product['description'])?></p>
                    <button class="add-to-cart" data-id="<?=htmlspecialchars($product['id']) ?>" data-name="<?=htmlspecialchars($product['name']) ?>" data-price="<?=htmlspecialchars($product['price']) ?>" data-image="<?=htmlspecialchars($product['image']) ?>">Ajouter au panier</button>
                </div>
            </div>
             <?php endforeach; ?>
        </div>
        
    </section>

    <div class="cart-overlay">
        <div class="cart">
            <span class="close-cart"><i class="fas fa-times"></i></span>
            <h2>Votre Panier</h2>
            <div class="cart-content">
                <!-- Les articles du panier seront ajoutés ici dynamiquement -->
            </div>
            <div class="cart-total">
                <span>Total : </span>
                <span class="cart-total-amount">0$</span>
            </div>
            <button class="checkout-btn">Passer la commande</button>
        </div>
    </div>
    
    <script src="../assets/js/cart.js">
        
    </script>

</body>

</html>