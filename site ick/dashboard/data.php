<?php
require 'db.php';

$data = [];

$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$data[] = ["title" => "Utilisateurs", "value" => $userCount];

$salesSum = $pdo->query("SELECT SUM(amount) FROM sales")->fetchColumn();
$data[] = ["title" => "Total ventes", "value" => $salesSum . " €"];

header('Content-Type: application/json');
echo json_encode($data);
