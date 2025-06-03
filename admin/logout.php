<?php
session_start();
session_destroy(); // Supprime la session
header("Location: login_admin.php"); // Redirige vers la page de connexion
exit;