<?php

require_once 'includes/config.php'; // Connexion à la base
require_once __DIR__ . '/vendor/autoload.php'; // Autoload PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = loginDatabase();
    $email = trim($_POST['email']);

    if (!empty($email)) {
        try {
            // Vérifier si l'email existe
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Générer un token de réinitialisation
                $token = bin2hex(random_bytes(32));
                $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));
                $stmt = $db->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
                $stmt->execute([$token, $expires, $user['id']]);

                // Préparer l'e-mail avec PHPMailer
                $resetLink = "http://localhost/ghost/deep/reset_password.php?token=$token";
                $subject = "Réinitialisation de votre mot de passe";
                $body = "Bonjour,\n\nCliquez sur ce lien pour réinitialiser votre mot de passe : $resetLink\n\nCe lien expirera dans 1 heure.";

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.example.com'; // À adapter
                    $mail->SMTPAuth = true;
                    $mail->Username = 'ton@email.com'; // À adapter
                    $mail->Password = 'motdepasse';    // À adapter
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('ton@email.com', 'Smith Collection');
                    $mail->addAddress($email);
                    $mail->Subject = $subject;
                    $mail->Body    = $body;

                    $mail->send();
                } catch (Exception $e) {
                    // Log l'erreur si besoin, mais ne pas l'afficher à l'utilisateur
                }
            }
            // Toujours afficher le même message, même si l'e-mail n'existe pas
            $message = "Si un compte existe avec cette adresse, un e-mail de réinitialisation a été envoyé.";
        } catch (PDOException $e) {
            $message = "Une erreur est survenue. Veuillez réessayer plus tard.";
        }
    } else {
        $message = "Veuillez entrer votre adresse e-mail.";
    }
}
?>
<h2>Mot de passe oublié</h2>
<form method="post" action="forgot_password.php">
    <input type="email" name="email" placeholder="Votre e-mail" required>
    <button type="submit">Envoyer le lien de réinitialisation</button>
</form>
<p><?= htmlspecialchars($message) ?></p>