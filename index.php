<?php
require_once "includes/config.php";

// //Gestion de l'upload d'image 
// $image = '';
// if(isset($_FILES['imge']) && $_FILES['images']['error'] == 0 ) {
//     $uploadDir = 'admin/';
//     $image = $uploadDir . basename ($_FILES['image']['name']);
//     move_uploaded_file( $_FILES['image']['tmp_namr'], $image);
// }

// Database connection
try {
    $db = 
 $bdd = new PDO('mysql:host=localhost;dbname=ecommerce_db', 'root', '');
        [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ;
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
// Récupération des produits pour affiche
$products = $db->query("
    SELECT p.*, c.name AS catergory_name FROM products p LEFT JOIN products c ON p.description ORDER BY id LIMIT 0,4")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boutique en ligne - Accueil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container" id="navbar">
            <div class="navar-logo">
            <nav class="navbar">
                <div class="logo">Smith<span>Collection</span></div>
                <ul class="nav-links">
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="classes/Product.php">Boutique</a></li>
                    <li><a href="#">Nouveautés</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
                <div class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Découvrez notre nouvelle collection</h1>
                <p>Des produits exclusifs à des prix imbattables</p>
                <a href="#" class="btn">Voir la collection</a>
            </div>
        </div>
    </section>

    <!-- Produits -->
    <section class="container">
        <h2 class="section-title">Nos produits phares</h2>
        <d class="products">
            <!-- Produit 1 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="https://via.placeholder.com/300x300" alt="Produit 1">
                </div>
                <div class="product-info">
                    <h3 class="product-title">T-shirt élégant</h3>
                    <div class="product-price">29.99€</div>
                    <button class="add-to-cart" data-id="1" data-name="T-shirt élégant" data-price="29.99" data-image="https://via.placeholder.com/300x300">Ajouter au panier</button>
                </div>
            </div>
            
            <!-- Produit 2 -->
             <?php foreach ($products as $product): ?>
            <div class="product-card">
                
                <div class="product-image">
                    <img src="<?=htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" alt="Produit 2">
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                    <div class="product-price"><?=htmlspecialchars($product['price']) ?></div>
                    <button class="add-to-cart" data-id="<?=htmlspecialchars($product['id']) ?>" data-name="<?=htmlspecialchars($product['name']) ?>" data-price="<?=htmlspecialchars($product['price']) ?>" data-image="<?=htmlspecialchars($product['image']) ?>">Ajouter au panier</button>
                </div>
            </div>
             <?php endforeach; ?>
      
            <!-- Produit 3 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="https://via.placeholder.com/300x300" alt="Produit 3">
                </div>
                <div class="product-info">
                    <h3 class="product-title">Chaussures sport</h3>
                    <div class="product-price">79.99€</div>
                    <button class="add-to-cart" data-id="3" data-name="Chaussures sport" data-price="79.99" data-image="https://via.placeholder.com/300x300">Ajouter au panier</button>
                </div>
            </div>
            
            <!-- Produit 4 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="https://via.placeholder.com/300x300" alt="Produit 4">
                </div>
                <div class="product-info">
                    <h3 class="product-title">Veste en cuir</h3>
                    <div class="product-price">129.99€</div>
                    <button class="add-to-cart" data-id="4" data-name="Veste en cuir" data-price="129.99" data-image="https://via.placeholder.com/300x300">Ajouter au panier</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Panier -->
    <div class="cart-overlay">
        <div class="cart">
            <span class="close-cart"><i class="fas fa-times"></i></span>
            <h2>Votre Panier</h2>
            <div class="cart-content">
                <!-- Les articles du panier seront ajoutés ici dynamiquement -->
            </div>
            <div class="cart-total">
                <span>Total : </span>
                <span class="cart-total-amount">0€</span>
            </div>
            <button class="checkout-btn">Passer la commande</button>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Smith Collection</h3>
                    <p>Votre destination shopping préférée pour des produits de qualité à des prix abordables.</p>
                </div>
                <div class="footer-column">
                    <h3>Liens rapides</h3>
                    <ul>
                        <li><a href="#">Accueil</a></li>
                        <li><a href="classes/product.php">Produits</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Promotions</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Informations</h3>
                    <ul>
                        <li><a href="classses/about.php">À propos</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Politique de confidentialité</a></li>
                        <li><a href="#">Conditions générales</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact</h3>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> ;;;;;;;</li>
                        <li><i class="fas fa-phone"></i><a href="tel:+243895025138">+243895025138</li>
                        <li><i class="fas fa-envelope"></i> contact@smithcollection.com</li>
                    </ul>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 Smith Collection. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/cart.js">
      
    </script>
</body>
</html>