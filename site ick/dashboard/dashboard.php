<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../../admin/login_admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="sidebar">
    <h2>Mon Dashboard</h2>
    <ul>
        <li><a href="#">Accueil</a></li>
        <li><a href="#">Statistiques</a></li>
        <li><a href="logout.php">Déconnexion</a></li>
    </ul>
</div>

<div class="main">
    <div class="header">
        <h1>Bienvenue, <?= htmlspecialchars($_SESSION['user']) ?></h1>
    </div>

    <div class="content" id="data-container">
        <!-- Cartes dynamiques -->
    </div>

    <canvas id="graph" width="400" height="200"></canvas>
</div>

<script src="script.js"></script>
</body>
</html>
