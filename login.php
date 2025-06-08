<?php
// Connexion à la base des données
require_once 'includes/config.php';

session_start();

$error = ''; // Variable pour stocker les messages d'erreur

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = loginDatabase();
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validation des champs
    if (empty($username) || empty($password)) {
        $error = "Tous les champs sont requis.";
    } else {
        try {
            // Recherche de l'utilisateur
            $stmt = $db->prepare("SELECT id, username, password, email, is_admin, created_at FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Démarrage de la session
                $_SESSION["loggedin"] = true;
                $_SESSION["user_id"] = $user['id'];
                $_SESSION["username"] = $user['username'];
                $_SESSION["email"] = $user['email'];
                $_SESSION["is_admin"] = $user['is_admin'];
                $_SESSION["created_at"] = $user['created_at'];
                $_SESSION["role"] = $user['is_admin'] ? "admin" : "client"; // <-- AJOUT

                // Redirection en fonction du rôle
                if ($user['is_admin']) {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $error = "Nom d'utilisateur ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $error = "Une erreur est survenue : " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
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
        <h1>Connexion</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Nom d'utilisateur :</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Se connecter</button>
        </form>
        <div class="admin-notice">
            Pas de compte ? <a href="register.php">Inscrivez-vous</a>
        </div>
    </div>
</body>
</html>