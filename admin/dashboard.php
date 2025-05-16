<?php
require_once 'config.php';


// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    // Définir un message d'erreur dans la session
    $_SESSION['error_message'] = "Accès refusé. Vous devez être administrateur pour accéder à cette page.";
    header('Location: login_admin.php');
    exit;
}
// Récupérer tous les utilisateurs
$users = $db->query("
    SELECT id, username, email, role, is_admin, created_at 
    FROM users
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord admin</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <header class="admin-header">
        <h1>Bienvenue, <?= htmlspecialchars($_SESSION['user']['username']) ?> !</h1>
        <nav>
            <a href="dashboard.php">Tableau de bord</a>
            <a href="admin_profile.php">Profil</a>
            <a href="logout.php">Déconnexion</a>
        </nav>
    </header>

    <main class="container">
        <section class="user-list">
            <h2>Liste des utilisateurs</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= $user['is_admin'] ? 'Admin' : 'Utilisateur' ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn-edit">Modifier</a>
                                <a href="promote_user.php?id=<?= $user['id'] ?>" class="btn-promote">
                                    <?= $user['is_admin'] ? 'Retirer admin' : 'Promouvoir admin' ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Smith Collection. Tous droits réservés.</p>
    </footer>
</body>
</html>