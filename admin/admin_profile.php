<?php
require_once 'config.php';

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login_admin.php');
    exit;
}

// Récupérer les infos complètes de l'admin
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user']['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: logout_admin.php');
    exit;
}

// Traitement du formulaire de mise à jour
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';

    // Validation des données
    if (empty($username)) {
        $errors[] = "Le nom complet est requis.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Une adresse email valide est requise.";
    }

    if (!empty($new_password) && strlen($new_password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }

    // Si aucune erreur, mettre à jour les informations
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$username, $email, $_SESSION['user']['id']]);

            // Mettre à jour le mot de passe si un nouveau est défini
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $_SESSION['user']['id']]);
            }

            // Mettre à jour les informations de session
            $_SESSION['user']['username'] = $username;
            $_SESSION['user']['email'] = $email;

            $success = "Profil mis à jour avec succès.";
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
    <title>Profil Administrateur</title>
    <link rel="stylesheet" href="../assets/css/admin_profile.css">
    <style>
        .admin-nav {
            background-color: #212529;
            min-height: 100vh;
            color: white;
        }
        .admin-nav a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: block;
            padding: 0.75rem 1rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
        }
        .admin-nav a:hover, .admin-nav a.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .profile-header {
            background-color: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .admin-content {
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block admin-nav">
                <div class="position-sticky pt-3">
                    <h4 class="text-center mb-4">Administration</h4>
                    <ul class="nav flex-column">
                        <li><a href="admin_profile.php" class="active">Mon Profil</a></li>
                        <li><a href="dashboard.php">Tableau de bord</a></li>
                        <li><a href="manage_users.php">Gestion Utilisateurs</a></li>
                        <li><a href="admin.php">Gestion Produits</a></li>
                        <li><a href="admin_settings.php">Paramètres</a></li>
                        <li><a href="login_admin.php">Déconnexion</a></li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Mon Profil Administrateur</h1>
                </div>

                <div class="profile-header">
                    <div class="row">
                        <div class="col-md-2 text-center">
                            <img src="admin-avatar.png" alt="Avatar" class="rounded-circle" width="100">
                        </div>
                        <div class="col-md-10">
                            <h2><?= htmlspecialchars($user['username']) ?></h2>
                            <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
                            <p>Dernière connexion: <?= date('d/m/Y H:i', strtotime($user['last_login'] ?? 'now')) ?></p>
                        </div>
                    </div>
                </div>

                <div class="admin-content">
                    <h3>Informations du compte</h3>
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

                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="admin_profile.php">
                                <div class="mb-3 row">
                                    <label class="col-sm-3 col-form-label">Nom complet</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($user['username']) ?>">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-3 col-form-label">Email</label>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-3 col-form-label">Nouveau mot de passe</label>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control" name="new_password" placeholder="Laisser vide pour ne pas changer">
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>