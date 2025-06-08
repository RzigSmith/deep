<?php
session_start(); // Démarre la session
 require_once  dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'db.php';
$db = loginDatabase(); // Connexion à la base de données

$data = [];

$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$data[] = ["title" => "Utilisateurs", "value" => $userCount];

$salesSum = $pdo->query("SELECT SUM(amount) FROM sales")->fetchColumn();
$data[] = ["title" => "Total ventes", "value" => $salesSum . " €"];

header('Content-Type: application/json');
echo json_encode($data);
