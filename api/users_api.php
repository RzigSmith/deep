<?php
// filepath: e:\wamp64\www\ghost\deep\api\users_api.php
require_once '../includes/db.php';
require_once '../classes/UserManager.php';

header('Content-Type: application/json');

$userManager = new UserManager($db);

if (isset($_GET['id'])) {
    $user = $userManager->getUserById($_GET['id']);
    echo json_encode($user);
} else {
    $users = $userManager->getAllUsers();
    echo json_encode($users);
}