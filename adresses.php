<?php
require_once 'includes/config.php';
require_once 'welcome.php';

$db = loginDatabase();

// Récupérer les adresses de l'utilisateur
$stmt = $db->prepare("SELECT * FROM addresses WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire d'ajout d'adresse
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $country = trim($_POST['country'] ?? '');

    // Validation des données
    if (empty($address)) {
        $errors[] = "L'adresse est requise.";
    }
    if (empty($city)) {
        $errors[] = "La ville est requise.";
    }
    if (empty($postal_code)) {
        $errors[] = "Le code postal est requis.";
    }
    if (empty($country)) {
        $errors[] = "Le pays est requis.";
    }

    // Si aucune erreur, ajouter l'adresse
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO addresses (user_id, address, city, postal_code, country) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $address, $city, $postal_code, $country]);
            $success = "Adresse ajoutée avec succès.";
        } catch (PDOException $e) {
            $errors[] = "Une erreur est survenue lors de l'ajout de l'adresse : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Adresses</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/od.css">
    <link rel="stylesheet" href="assets/css/adresse.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">Smith<span>Collection</span></div>
            <ul class="nav-links" id="navLinks">
                <li><a href="./classes/product.php">Boutique</a></li>
                
                <li><a href="./classes/contact.php">Contact</a></li>
                <?php if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true): ?>
                    <li><a href="./login.php">Connexion</a></li>
                <?php elseif (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
                    <li><a href="./admin/orders.php">Commandes</a></li>
                <?php else: ?>
                    <li><a href="./profile.php">Profil</a></li>
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

    <div class="addresses-container">
        <h1 class="section-title">Mes Adresses</h1>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h2 class="section-subtitle">Ajouter une nouvelle adresse</h2>
        <form method="POST" action="adresses.php" class="address-form">
            <div class="form-group">
                <label for="address">Adresse :</label>
                <input type="text" id="address" name="address" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="city">Ville :</label>
                <input type="text" id="city" name="city" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="postal_code">Code Postal :</label>
                <input type="text" id="postal_code" name="postal_code" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="country">Pays :</label>
                <input type="text" id="country" name="country" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter l'adresse</button>
        </form>

        <h2 class="section-subtitle">Mes Adresses</h2>
        <?php if (empty($addresses)): ?>
            <p class="no-addresses">Vous n'avez pas encore ajouté d'adresse.</p>
        <?php else: ?>
            <ul class="address-list">
                <?php foreach ($addresses as $address): ?>
                    <li class="address-item">
                        <p><?= htmlspecialchars($address['address']) ?>, <?= htmlspecialchars($address['city']) ?>, <?= htmlspecialchars($address['postal_code']) ?>, <?= htmlspecialchars($address['country']) ?></p>
                        <a href="delete_address.php?id=<?= $address['id'] ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette adresse ?')">Supprimer</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>