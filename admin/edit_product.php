<?php
include_once "config.php";

// Vérifier si un ID de produit est passé dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$product_id = intval($_GET['id']);

// Récupérer les informations du produit
$stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: admin.php');
    exit;
}

// Mettre à jour le produit après soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    // $category_id = intval($_POST['category_id']);
    $image = $product['image']; // Garder l'image existante par défaut

    // Gestion de l'upload d'une nouvelle image
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = realpath(__DIR__ . '/../uploads/') . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $image = $uploadDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Mettre à jour les informations du produit dans la base de données
    $stmt = $db->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ?  WHERE id = ?");
    $stmt->execute([$name, $description, $price, $image,  $product_id]);

    header('Location: admin.php?success=1');
    exit;
}

// Récupérer les catégories pour le formulaire
$stmt = $db->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le produit</title>
    <link rel="stylesheet" href="../assets/css/edit.css">
</head>
<body>
    <header class="admin-header">
        <h1>Modifier le produit</h1>
        <nav>
            <a href="admin.php">Retour au tableau de bord</a>
        </nav>
    </header>
    <main>
        <section class="admin-section">
            <h2>Modifier les informations du produit</h2>
            <form id="editProductForm" method="post" enctype="multipart/form-data">
                <label for="productName">Nom du produit :</label>
                <input type="text" name="name" id="productName" value="<?= htmlspecialchars($product['name']) ?>" required><br>

                <label for="productPrice">Prix :</label>
                <input type="number" name="price" id="productPrice" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required><br>

                <label for="productDescription">Description :</label>
                <textarea id="productDescription" name="description" rows="3" required><?= htmlspecialchars($product['description']) ?></textarea><br>
                <p>Image actuelle :</p>
                <img src="<?= htmlspecialchars($product['image']) ?>" width="100"><br>

                <label for="productCategory">Catégorie :</label>
                <!-- <select name="category_id" id="productCategory" required>
                    <option value="">-- Choisir une catégorie --</option> -->
              

                <button type="submit">Mettre à jour</button>
            </form>
        </section>
    </main>
</body>
</html>