<?php

require_once '../includes/config.php';
// Traitement du formulaire (optionnel)
$success = '';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name)) $errors[] = "Le nom est requis.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Un email valide est requis.";
    if (empty($message)) $errors[] = "Le message est requis.";

    if (!$errors) {
        // Ici tu pourrais envoyer un email ou enregistrer le message en base
        $success = "Votre message a bien été envoyé. Merci !";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Contact - SmithCollection</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/contact.css">

</head>

<body>
    <header class="header">
        <nav class="navbar">
            <div id="logo">
                <h2 class="logo">Smith<span>Collection</span></h2>
            </div>
            <ul class= "nav-links">
                <li class="link"><a href="../index.php">Acceuil</a></li>
                <li class="link"><a href="product.php">Boutique</a></li>
                <li class="link"><a href="#"></a></li>
                <li class="link"><a href="#"></a></li>
            </ul>
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
            <button type="submit">Envoyer</button>
        </form>
    </div>
</body>

</html>