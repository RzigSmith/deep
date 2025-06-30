<?php

session_start();
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'db.php';
$db = loginDatabase(); // Connexion à la base de données	
$essaie = $db->prepare("SELECT * FROM users WHERE id = ?");
$essaie->execute([$_SESSION['user']['id']]);


//Verifivation si l'utilisateur est administrateur 
if (!isset($_SESSION['user']) && $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['error_message'] = "Accès refusé. Vous devez être administrateur pour accéder à cette page.";
    header('Location: ../login.php');
    exit;
}


$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
