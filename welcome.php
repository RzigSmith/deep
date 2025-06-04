<?php
session_start();

// Vérifier si l'utilisateur est connecté, sinon rediriger vers la page de connexion
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bienvenue</title>
    <style>
        body{ font: 14px sans-serif; text-align: center; }
        .wrapper{ width: 600px; padding: 20px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>Bienvenue</h1>
        <?php 
        // Afficher le message de bienvenue
        if(isset($_SESSION["welcome_message"])){
            echo '<div class="alert alert-success">' . $_SESSION["welcome_message"] . ' ' . $_SESSION['role'] . '</div>';
            unset($_SESSION["welcome_message"]);
        }
        
        // Redirection en fonction du rôle
        if($_SESSION["role"] === "admin"){
            echo '<p>Vous êtes connecté en tant qu\'administrateur.</p>';
            echo '<p><a href="admin/dashboard.php">Accéder au tableau de bord administrateur</a></p>';
        } else{
            echo '<p>Vous êtes connecté en tant que client.</p>';
            echo '<p><a href="index.php">Accéder à l\'accueil</a></p>';
        }
        ?>
        <p>
            <a href="logout.php">Déconnexion</a>
        </p>
    </div>
</body>
</html>