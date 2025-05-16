<?php
require_once 'config.php';

// Vérifier si l'accès est autorisé (à protéger par IP ou autre méthode)
$allowed = $_SERVER['REMOTE_ADDR'] === '127.0.0.1'; // Exemple: seulement depuis localhost

// if (!$allowed) {
//     die("Accès non autorisé");
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'] ?? 'admin'; // Par défaut, le rôle est admin


    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        die("Tous les champs sont requis");
    }

    if ($password !== $confirm_password) {
        die("Les mots de passe ne correspondent pas");
    }

    if (strlen($password) < 8) {
        die("Le mot de passe doit faire au moins 8 caractères");
    }

    // Vérifier si l'utilisateur existe déjà
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->fetch()) {
        die("Ce nom d'utilisateur ou email est déjà utilisé");
    }

    // Créer le compte admin
    if (empty($errors)) {
        try {
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    $stmt = $db->prepare("INSERT INTO users (username, email, password, role, is_admin) VALUES (?, ?, ?, ?, 1)");
    $stmt->execute([$username, $email, $hashed_password, $role]);

}   catch(PDOException $e) {
    $errors[] = "Erreur lors de l'inscription: " . $e->getMessage();
}
}
    echo "Compte admin créé avec succès!";
     
    header('Location: dashboard.php');
    exit;
} 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Création compte admin</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px; }
        input { display: block; margin-bottom: 10px; padding: 8px; width: 100%; }
        button { padding: 10px 15px; background: #0066cc; color: white; border: none; }
    </style>
</head>
<body>
    <h1>Créer un compte administrateur</h1>
    <form method="POST">
        <input type="text" name="username" placeholder="Nom d'administrateur" required>
        <input type="email" name="email" placeholder="Email admin" required>
        <input type="password" name="password" placeholder="Mot de passe (+8 caractères)" required minlength="8">
        <input type="password" name="confirm_password" placeholder="Confirmez le mot de passe" required minlength="8">
        <input type="hidden" name="role" value="admin">
        <button type="submit">Créer le compte admin</button>
    </form>
    <p>Déjà un compte ? <a href="admin/login_admin.php">Connectez-vous</a></p>

</body>
</html>