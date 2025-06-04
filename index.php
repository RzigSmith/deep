<?php
require_once "includes/config.php";
try {
  $db = new PDO(
    'mysql:host=localhost;dbname=ecommerce_db',
    'root',
    '',
    [
      PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]
  );
} catch (PDOException $e) {
  die('La connexion à la base de données a échoué : ' . $e->getMessage());
}


// Récupération des produits pour affichage
$products = $db->query('SELECT * FROM products ORDER BY id LIMIT 0, 4')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SmithCollection</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/monindex.css" />
</head>

<body>
  <header>
    <div class="container" id="navbar">
      <div class="navar-logo">
        <nav class="navbar">
          <div class="logo">Smith<span>Collection</span></div>
          <ul class="nav-links">

            <?php if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
              echo '<li><a href="login.php">Connexion</a></li>';
            } else {
              echo '<li><a href="profile.php">Profile</a></li>';
            } ?>
            <li><a href="classes/Product.php">Boutique</a></li>
            <li><a href="#">Nouveautés</a></li>
            <li><a href="classes/contact.php">Contact</a></li>
          </ul>
          <div class="notification-icon">
            <i class="fas fa-bell"></i>
            <span class="notification-badge">0</span>
          </div>

        </nav>
      </div>
  </header>

  </nav>
  </header>
  <div class="grid-container">
    <section class="hero">
      <div class="text-container">
        <?php if (!empty($products)): ?>
          <h1>
            <div class="main-product-name">
              <?= htmlspecialchars($products[0]['name']) ?>
            </div>
          </h1>

          <p>
            Lorem ipsum, dolor sit amet consectetur adipisicing elit. Ut unde
            tenetur blanditiis quod. Vero dolorum corrupti et numquam.
          </p>
          <div class="container">
            <div class="hero-content">
              <h1>Découvrez notre collection</h1>
              <p>Des produits exclusifs à des prix imbattables</p>
            </div>
          </div>
          <a href="classes/product.php" class="button">Voir la collection</a>
      </div>
    </section>
    <section class="products">

      <div class="Produit-icons">
        <?php foreach ($products as $product): ?>
          <div class="icon<?= $product === $products[0] ? ' active' : '' ?>" data-shoe="<?= htmlspecialchars($product['id']) ?>" data-name="<?= htmlspecialchars($product['name']) ?>">
            <img src="<?= 'admin/' . htmlspecialchars($product['image']) ?>" width="60" />
          </div>
        <?php endforeach; ?>
      </div>
      <div class="image-container">
        <div class="main-image"></div>
        <img src="<?= 'admin/' . htmlspecialchars($products[0]['image']) ?>" alt="<?= htmlspecialchars($products[0]['name']) ?>" class="shoe-image" width="450px">
      </div>
      </div>
    <?php endif; ?>
    <div class="more-button">
      <span>More</span>
      <div class="arrow"></div>
    </div>
    </section>
  </div>
  <script src="assets/js/script.js"></script>
  </section>
  </div>

  </style>
</body>
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
          <li><a href="classes/about.php">À propos</a></li>
          <li><a href="#">Contact</a></li>
          <li><a href="#">Politique de confidentialité</a></li>
          <li><a href="#">Conditions générales</a></li>
        </ul>
      </div>
      <div class="footer-column">
        <h3>Contact</h3>
        <ul>
          <li><i class="fas fa-map-marker-alt"></i> ;;;;;;;</li>
          <li><i class="fas fa-phone"></i><a href="tel:+243895025138">+243895025138</a></li>
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

</html>