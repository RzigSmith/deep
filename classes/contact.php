<?php

require_once '../includes/db.php';
// Vérification de la session utilisateur
require_once '../welcome.php';

// Connexion à la base de données
try {
    $db = new PDO('mysql:host=localhost;dbname=ecommerce_db', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion: " . $e->getMessage());
}
// Traitement du formulaire (optionnel)
$success = '';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : $name;

    if (empty($name)) $errors[] = "Le nom est requis.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Un email valide est requis.";
    if (empty($message)) $errors[] = "Le message est requis.";

    if (!$errors) {
        $success = "Votre message a bien été envoyé. Merci !";
        $stmt = $db->prepare("INSERT INTO contacts (user_id, username, name, email, message, date_message) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $username, $name, $email, $message]);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Contact - SmithCollection</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/contacts.css">
    <link rel="stylesheet" href="cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/od.css">

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
    <div class="contact-container">
        <h1>Contactez-nous</h1>
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($errors): ?>
            <div class="error">
                <?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
            </div>
        <?php endif; ?>
        <form method="post" action="contact.php">
            <input type="text" name="name" placeholder="Votre nom" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            <input type="email" name="email" placeholder="Votre email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            <textarea name="message" rows="5" placeholder="Votre message" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            <button type="submit" name="send">Envoyer</button>
        </form>
    </div>
    
    <script src="../assets/js/ct.js"></script>
</body>

</html>