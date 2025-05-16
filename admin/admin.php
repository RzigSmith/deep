<?php

require_once 'config.php'; // Fichier de configuration (connexion DB)


// Vérifier si l'utilisateur est admin
// if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] !== 1) {
//     header('Location: login_admin.php');
//     exit;
// }

// Function to check if the user is an admin
// function isAdmin()
// {
//     return isset($_SESSION['user']) && $_SESSION['user']['is_admin'] === 1;
// }

// if (!isAdmin()) {
//     header('Location: login_admin.php');
//     exit;
// }
//  Traitement du formulaire d'ajout
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price =    $_POST['price'];


    //  Gestion de l'upload d'image

    $uploadDir = realpath(__DIR__ . '/../uploads/') . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $image = $uploadDir . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $image);
    // Isertion en base de données
    $stmt = $db->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $image]);

    header("Location: admin/admin.php?success=1"); // Rédirection pour le rechargement du formulaire
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
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>
    <header class="admin-header">
        <h1>Tableau de bord - Administration</h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="admin_profile.php">Mon Profil</a>
            <a href="connecion.php" onclick="logout()">Déconnexion</a>
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
        </section>
        <section class="admin-section">
            <h2>Liste des produits</h2>

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
                    <a href="edit_product.php?id=<?= $product['id'] ?>">Modifier</a> |
                    <a href="delete_product.php?id=<?= $product['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


    </main>
    <script src="../assets/js/main.js"></script>

</body>

</html>