<?php

require_once '../includes/db.php';
session_start();
$db = loginDatabase();
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
if ($isAdmin) {
    $db->query("UPDATE notifications SET is_read = 1 WHERE user_id IS NULL OR user_id = 0");
} else {
    $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}