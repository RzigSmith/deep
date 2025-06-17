<?php


require_once 'includes/db.php';

$token = $_GET['token'] ?? '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_GET['token'] ?? '';
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token=? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    if ($user) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?");
        $stmt->execute([$hash, $user['id']]);
        $message = "Mot de passe réinitialisé. <a href='login.php'>Connectez-vous</a>";
    } else {
        $message = "Lien invalide ou expiré.";
    }
}

if ($token && !$message) {
    // Vérifie le token avant d'afficher le formulaire
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token=? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    if ($stmt->fetch()) {
        ?>
        <h2>Réinitialiser le mot de passe</h2>
        <form method="post">
            <input type="password" name="password" placeholder="Nouveau mot de passe" required>
            <button type="submit">Réinitialiser</button>
        </form>
        <?php
    } else {
        echo "<p>Lien invalide ou expiré.</p>";
    }
}
if ($message) {
    echo "<p>$message</p>";
}
?>