<?php
include_once "config.php";

// Vérifier si un ID de produit est passé dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Aucun produit spécifié.";
    header('Location: admin.php');
    exit;
}

$product_id = intval($_GET['id']);

// Récupérer les informations du produit pour supprimer l'image associée
$stmt = $db->prepare("SELECT image FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $_SESSION['error_message'] = "Produit introuvable.";
    header('Location: admin.php');
    exit;
}

// Supprimer l'image associée au produit
if (!empty($product['image']) && file_exists($product['image'])) {
    unlink($product['image']);
}

// Supprimer le produit de la base de données
$stmt = $db->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$product_id]);

// Rediriger avec un message de succès
$_SESSION['success_message'] = "Produit supprimé avec succès.";
header('Location: admin.php');
exit;