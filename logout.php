<?php
session_start(); // Démarre la session
unset($_SESSION["loggedin"]); // Supprime la variable de session "loggedin"
unset($_SESSION["user"]); // Supprime la variable de session "user"
unset($_SESSION["user_id"]); // Supprime la variable de session "user_id"
unset($_SESSION["username"]); // Supprime la variable de session "username"
unset($_SESSION["email"]); // Supprime la variable de session "email"
unset($_SESSION["is_admin"]); // Supprime la variable de session "is_admin"
unset($_SESSION["created_at"]); // Supprime la variable de session "created_at"
unset($_SESSION["role"]); // Supprime la variable de session "role"
unset($_SESSION["avatar"]); // Supprime la variable de session "avatar"
// Détruit la session
session_destroy(); // Détruit la session
header("Location: login.php"); // Redirige vers la page de connexion

exit;