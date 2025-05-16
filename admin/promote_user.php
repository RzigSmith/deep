<?php
require_once 'config.php';
session_start();

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['error_message'] = "Accès refusé. Vous devez être administrateur pour effectuer cette action.";
    header('Location: login_admin.php');
    exit;
}

// Vérifier si un ID d'utilisateur est passé dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Aucun utilisateur spécifié.";
    header('Location: dashboard.php');
    exit;
}

$user_id = intval($_GET['id']);

try {
    // Récupérer les informations de l'utilisateur
    $stmt = $db->prepare("SELECT id, is_admin FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['error_message'] = "Utilisateur introuvable.";
        header('Location: dashboard.php');
        exit;
    }

    // Inverser le rôle d'administrateur
    $new_role = $user['is_admin'] ? 0 : 1;
    $stmt = $db->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
    $stmt->execute([$new_role, $user_id]);

    // Définir un message de succès
    $_SESSION['success_message'] = $user['is_admin'] 
        ? "Le rôle d'administrateur a été retiré avec succès." 
        : "L'utilisateur a été promu administrateur avec succès.";

    header('Location: dashboard.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Une erreur est survenue : " . $e->getMessage();
    header('Location: dashboard.php');
    exit;
}