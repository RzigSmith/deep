<?php
require_once 'includes/config.php';

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// Traitement du formulaire de mise à jour
 
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
$errors = [];
$success = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation des données
    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Une adresse email valide est requise.";
    }

    if (!empty($new_password) && $new_password !== $confirm_password) {
        $errors[] = "Le nouveau mot de passe et sa confirmation ne correspondent pas.";
    }

    if (!empty($new_password) && strlen($new_password) < 6) {
        $errors[] = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
    }

    // Vérification du mot de passe actuel si un nouveau mot de passe est défini
    if (!empty($new_password) && !password_verify($current_password, $user['password'])) {
        $errors[] = "Le mot de passe actuel est incorrect.";
    }

    // Si aucune erreur, mettre à jour les informations
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$username, $email, $_SESSION['user_id']]);

            // Mettre à jour le mot de passe si un nouveau est défini
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $_SESSION['user_id']]);
            }

            // Mettre à jour les informations de session
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;

            $success = "Votre profil a été mis à jour avec succès.";
        } catch (PDOException $e) {
            $errors[] = "Une erreur est survenue lors de la mise à jour : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/profile.css">
</head>
<body>
    <div class="profile-container">
        <!-- Sidebar -->
        <aside class="profile-sidebar">
            <img src="images/2025_04_12_11_22_IMG_6669.JPG" alt="Avatar" class="profile-avatar">
            <h2 class="profile-name"><?= htmlspecialchars($_SESSION['username']) ?></h2>
            <p class="profile-email"><?= htmlspecialchars($_SESSION['email']) ?></p>
            
            <div class="profile-stats">
                <div class="stat-item">
                    <div class="stat-value">42</div>
                    <div class="stat-label">Commandes</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">5</div>
                    <div class="stat-label">Favoris</div>
                </div>
            </div>
            
            <ul class="profile-menu">
                <li><a href="#" class="active">Mon Profil</a></li>
                <li><a href="order.php">Mes Commandes</a></li>
                <li><a href="index.php">Acceder à l'Accueil</a></li>
                <li><a href="adresses.php">Mes Adresses</a></li>
                <li><a href="api/cart.php">mon panier</a></li>
                <li><a href="favoris.php">Mes Favoris</a></li>
                <li><a href="parametres.php">Paramètres</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <main class="profile-main">
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
            
            <h1 class="section-title">Informations Personnelles</h1>
            <p class="member-since">Membre depuis le <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
            
            <form method="POST" action="profile.php">
                <div class="form-group">
                    <label for="username" class="form-label">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Adresse Email</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                
                <h2 class="section-title" style="margin-top: 2.5rem;">Changer de mot de passe</h2>
                
                <div class="form-group">
                    <label for="current_password" class="form-label">Mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="new_password" class="form-label">Nouveau mot de passe</label>
                    <input type="password" id="new_password" name="new_password" class="form-control">
                    <small class="error-message">Laissez vide pour ne pas changer</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                </div>
                
                <button type="submit" class="btn btn-block">Mettre à jour le profil</button>
            </form>
        </main>
    </div>
</body>
</html>