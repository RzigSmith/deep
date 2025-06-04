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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smith collection</title>
    <link rel="stylesheet" href="users.css">
</head>

<body>
    <div class="sidebar">
        <h2>Mon Dashboard</h2>
        <ul>
            <li><a href="#">Accueil</a></li>
            <li><a href="users.php">Utilisateurs</a></li>
            <li><a href="dashboard.php">Statistiques</a></li>
            <li><a href="admin_profile.php"></a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </div>
    <main class="container">
        <div class="main">
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
    <script src="../assets/js/dashboard.js"></script>
</body>

</html>