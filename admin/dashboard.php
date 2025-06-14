<?php
include_once "config.php";
// Statistiques globales
$userCount = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$productCount = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orderCount = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalSales = $db->query("SELECT IFNULL(SUM(total_amount),0) FROM orders")->fetchColumn();

// Statistiques mensuelles pour le graphique
$stats = $db->query("
    SELECT DATE_FORMAT(order_date, '%Y-%m') AS month, COUNT(*) AS orders, IFNULL(SUM(total_amount),0) AS sales
    FROM orders
    GROUP BY month
    ORDER BY month DESC
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

$months = [];
$orderData = [];
$salesData = [];
foreach (array_reverse($stats) as $row) {
    $months[] = $row['month'];
    $orderData[] = (int)$row['orders'];
    $salesData[] = (float)$row['sales'];
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="mydashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/od.css">
    <style>
    </style>
</head>

<body>
    <div class="sidebar">
        <h2>Mon Dashboard</h2>
        <ul>
            <li><a href="#" class="active">Accueil</a></li>
            <li><a href="manage_users.php">Gestion des utilisateurs</a></li>
            <li><a href="dashboard.php">Statistiques</a></li>
            <li><a href="admin_profile.php">Profil</a></li>
            <li><a href="admin.php">Gestion des articles</a></li>
            <li><a href="messages.php">Mes messages</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>

    </div>
    <div class="main">
        <div class="header">
            <h1>Bienvenue, <?= htmlspecialchars($_SESSION['user']['username']) ?> !</h1>
        </div>
        <div class="notify-bell" id="notifyBell" style="justify-content: end;">
            <i class="fas fa-bell"></i>
            <span class="notify-count" id="notifyCount"></span>
            <div class="notify-dropdown" id="notifyDropdown"></div>
        </div>
        <div class="stats-cards">
            <div class="card">
                <h3>Utilisateurs</h3>
                <div class="stat"><?= $userCount ?></div>
            </div>
            <div class="card">
                <h3>Produits</h3>
                <div class="stat"><?= $productCount ?></div>
            </div>
            <div class="card">
                <h3>Commandes</h3>
                <div class="stat"><?= $orderCount ?></div>
            </div>
            <div class="card">
                <h3>Ventes totales ($)</h3>
                <div class="stat"><?= number_format($totalSales, 2, ',', ' ') ?></div>
            </div>
        </div>
        <div class="chart-container">
            <h3 style="text-align:center;">Commandes & Ventes (6 derniers mois)</h3>
            <canvas id="ordersChart" height="100"></canvas>
        </div>
        <div class="content" id="data-container">
            <!-- Tu peux ajouter ici d'autres widgets dynamiques -->
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/od.js"></script>
    <script>
        // Données PHP vers JS
        const months = <?= json_encode($months) ?>;
        const orderData = <?= json_encode($orderData) ?>;
        const salesData = <?= json_encode($salesData) ?>;

        // Graphique commandes/ventes
        const ctx = document.getElementById('ordersChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                        label: 'Commandes',
                        data: orderData,
                        backgroundColor: 'rgba(52, 152, 219, 0.7)',
                        borderColor: 'rgba(41, 128, 185, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Ventes ($)',
                        data: salesData,
                        backgroundColor: 'rgba(46, 204, 113, 0.7)',
                        borderColor: 'rgba(39, 174, 96, 1)',
                        borderWidth: 1,
                        type: 'line',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Commandes'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        },
                        title: {
                            display: true,
                            text: 'Ventes ($)'
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>