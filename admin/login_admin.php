<?php
// login_admin.php - Page de connexion pour les administrateurs

require_once 'config.php'; // Fichier de configuration (connexion DB)


// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Tous les champs sont requis";
    } else {
        // Recherche de l'utilisateur avec son rôle
        $stmt = $db->prepare("
            SELECT id, username, password, role, is_admin 
            FROM users 
            WHERE username = ? 
            LIMIT 1
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérification du mot de passe et du rôle
        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_admin'] === 1) {
                // Connexion réussie
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role'], // Récupération du rôle
                    'is_admin' => true
                ];

                // Régénération de l'ID de session pour prévenir le fixation
                session_regenerate_id(true);

                // Redirection vers le tableau de bord
                header('Location: dashboard.php');
                exit;
            } else {
                $error = "Accès réservé aux administrateurs.";
            }
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 0.75rem;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 1rem;
        }
        button:hover {
            background-color: #1a252f;
        }
        .error {
            color: #e74c3c;
            margin-bottom: 1rem;
            text-align: center;
        }
        .admin-notice {
            text-align: center;
            margin-top: 1rem;
            color: #7f8c8d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Espace Administrateur</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                
                <input type="hidden" id="is_admin" name="is_admin" value="1">
                <input type="hidden" id="role" name="role" value="admin">
            </div>
            
            <button type="submit">Se connecter</button>
        </form>
        
        <div class="admin-notice">
            Cette page est réservée au personnel autorisé.
        </div>
        <p>Pas de compte ? <a href="create-admin.php">Inscrivez-vous</a></p>
    </div>
</body>
</html>