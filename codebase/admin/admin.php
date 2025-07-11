<?php

include_once "config.php";

//  Traitement du formulaire d'ajout
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];


    //  Gestion de l'upload d'image

    $uploadDir = realpath(__DIR__ . '/../uploads/') . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir,  permissions: true);
    }
    $image = $uploadDir . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $image);
    // Isertion en base de données
    $stmt = $db->prepare("INSERT INTO products name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $image]);

    header("Location: admin/admin.php?success=1");
    // Rédirection pour le rechargement du formulaire
    exit;
}

//Récuperation des produits pour affichage
$products = $db->query("
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN products c ON p.description")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer toutes les catégories
$description = $db->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smith Collection - Administration</title>
    <link rel="stylesheet" href="../assets/css/adm.css">
    <link rel="stylesheet" href="../assets/css/od.css">
    <link rel="stylesheet" href="../assets/css/notif.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    <main>
        <section class="admin-section">
            <h2>Ajouter un produit</h2>
            <!-- Formulaire d'ajout de produit -->
            <form id="addProductForm" method="post" action="save_product.php" enctype="multipart/form-data">
                <label for="productName">Nom du produit :</label>
                <input type="text" name="name" placeholder="Nom" id="productName" required><br>
                <label for="productPrice">Prix :</label>
                <input type="number" name="price" step="0.01" placeholder="Prix" id="productPrice" required><br>
                <label for="producliescription">Description :</label>
                <textarea id="producliescription" name="description" placeholder="Description" rows="3" required></textarea>
                <label for="productImage">Image :</label>
                <input type="file" id="productImage" name="image" accept="image/*" required><br>
                <select name="category_id">
                    <option value="">-- Choisir une catégorie --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="add_product">Ajouter</button>
            </form>
            <h2>Liste des produits</h2>
        </section>



        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Prix</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['id']) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['description']) ?></td>
                        <td><?= htmlspecialchars($product['price']) ?>$</td>
                        <td><img src="<?= htmlspecialchars($product['image']) ?>" width="100"></td>
                        <td>
                            <button type="submit" id="modify"> <a href="edit_product.php?id=<?= $product['id'] ?>">Modifier</a> </button> |
                            <button type="submit" id="remove"><a href="delete_product.php?id=<?= $product['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


    </main>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/od.js"></script>

</body>

</html>