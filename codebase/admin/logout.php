<?php
session_start(); // Démarre la session
unset($_SESSION["loggedin"]); // Supprime la variable de session "loggedin"
header("Location: ../login.php"); // Redirige vers la page de connexion

exit;