<?php
require_once '../includes/db.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
$db = loginDatabase();

// Récupérer toutes les commandes
$stmt = $db->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pour chaque commande, récupérer les produits commandés
$orderProducts = [];
if ($orders) {
    $orderIds = array_column($orders, 'id');
    $in = implode(',', array_fill(0, count($orderIds), '?'));
    $stmt = $db->prepare("
        SELECT oi.order_id, p.name 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id IN ($in)
    ");
    $stmt->execute($orderIds);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $orderProducts[$row['order_id']][] = $row['name'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $db->prepare("UPDATE orders SET status = 'confirmé' WHERE id = ?")->execute([$order_id]);
    // Notif client
    $stmt = $db->prepare("SELECT user_id FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $user_id = $stmt->fetchColumn();
    $db->prepare("INSERT INTO notifications (user_id, type, message) VALUES (?, 'confirmation', ?)")
        ->execute([$user_id, "Votre commande #$order_id a été confirmée par l'administrateur."]);
    header('Location: orders.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commandes clients</title>
    <link rel="stylesheet" href="../assets/css/od.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/notif.css">
</head>
<body>
<header>
    <nav class="navbar">
        <div class="logo">Smith<span>Collection</span></div>
        <ul class="nav-links" id="navLinks">
            <li><a href="dashboard.php">Statistic</a></li>
            <li><a href="messages.php">messages</a></li>
            
            <?php if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true): ?>
                <li><a href="../login.php">Connexion</a></li>
            <?php elseif (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
                <li><a href="../admin/orders.php">Commandes</a></li>
                <li><a href="logout.php">Deconnexion</a></li>
            <?php else: ?>
                <li><a href="../admin/admin_profile.php">Profil</a></li>
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
    <h2>Commandes clients</h2>
    <table class="orders-table">
        <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Date</th>
            <th>Montant</th>
            <th>Produits</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['username']) ?></td>
            <td><?= $order['order_date'] ?></td>
            <td><?= number_format($order['total_amount'],2) ?> $</td>
            <td>
                <?php
                if (!empty($orderProducts[$order['id']])) {
                    echo htmlspecialchars(implode(', ', $orderProducts[$order['id']]));
                } else {
                    echo '-';
                }
                ?>
            </td>
            <td><?= htmlspecialchars($order['status']) ?></td>
            <td>
                <?php if ($order['status'] !== 'confirmé'): ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <button type="submit">Confirmer</button>
                </form>
                <?php else: ?>
                Confirmée
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <script src="../assets/js/ct.js"></script>
    <script src="../assets/js/od.js"></script>
</body>
</html>


