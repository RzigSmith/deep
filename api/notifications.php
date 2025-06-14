<?php

require_once '../includes/db.php';
session_start();

$db = loginDatabase();
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if ($isAdmin) {
    $stmt = $db->prepare("SELECT * FROM notifications WHERE user_id IS NULL OR user_id = 0 ORDER BY created_at DESC LIMIT 10");
    $stmt->execute();
} else {
    $stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$_SESSION['user_id']]);
}
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));