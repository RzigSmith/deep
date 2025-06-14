<?php
include_once "config.php";

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
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" href="../assets/css/adu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/od.css">
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="logo">Smith<span>Collection</span></div>
            <ul class="nav-links" id="navLinks">
                <li><a href="/ghost/deep/classes/product.php">Boutique</a></li>
                <li><a href="#">Nouveautés</a></li>
                <li><a href="/ghost/deep/classes/contact.php">Contact</a></li>
                <?php if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true): ?>
                    <li><a href="/ghost/deep/login.php">Connexion</a></li>
                <?php elseif (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
                    <li><a href="/ghost/deep/admin/orders.php">Commandes</a></li>
                <?php else: ?>
                    <li><a href="/ghost/deep/profile.php">Profil</a></li>
                <?php endif; ?>
            </ul>
            <div class="notify-bell" id="notifyBell">
                <i class="fas fa-bell"></i>
                <span class="notify-count" id="notifyCount"></span>
                <div class="notify-dropdown" id="notifyDropdown"></div>
            </div>
            <div class="burger" id="burgerMenu">
                <span></span>
                <span></span>
                <span></span>
            </div>
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
                                <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
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
    <script src="../assets/js/od.js"></script>
</body>

</html></footer>