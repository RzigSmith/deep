<?php
require_once 'includes/config.php';
require_once 'welcome.php';
$db = loginDatabase();
$total = $_GET['order_items'] ?? '';

// Récupérer les commandes de l'utilisateur
$stmt = $db->prepare("
    SELECT 
        o.id AS order_id,
        o.order_date,
        o.total_amount,
        o.status,
        u.username,
        u.email,
        GROUP_CONCAT(CONCAT(p.name, ' (x', oi.quantity, ')') SEPARATOR ', ') AS products
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.order_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Commandes</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/order.css">
</head>

<body>

    <div class="container" id="navbar">
        <div class="navar-logo">
            <nav class="navbar">
                <div class="logo">Smith<span>Collection</span></div>
                <ul class="nav-links">

                    <?php if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
                        echo '<li><a href="login.php">Connexion</a></li>';
                    } elseif (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
                        echo '<li><a href="admin/dashboard.php">Dashboard</a></li>';
                    } else {
                        echo '<li><a href="profile.php">Profile</a></li>';
                    } ?>
                    <li><a href="classes/Product.php">Boutique</a></li>
                    <li><a href="#">Nouveautés</a></li>
                    <li><a href="classes/contact.php">Contact</a></li>
                </ul>
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">0</span>
                </div>

            </nav>
        </div>
        <div class="orders-container">
            <h1 class="section-title">Mes Commandes</h1>

            <?php
            if (!empty($orders)) : ?>
                <p>Aucune commande trouvé</p>
            <?php else : ?>

                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Produits</th>
                            <th>Montant Total</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['id']) ?></td>
                                <td><?= htmlspecialchars(date('d/m/Y', strtotime($order['order_date']))) ?></td>
                                <td><?= htmlspecialchars($order['products']) ?></td>
                                <td><?= htmlspecialchars(number_format($order['total_amount'], 2)) ?> $</td>
                                <td><?= htmlspecialchars($order['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif ?>

        </div>
</body>

</html>