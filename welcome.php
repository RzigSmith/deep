<?php
session_start();

// Vérifier si l'utilisateur est connecté ET est client
if(
    !isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ||
    !isset($_SESSION["role"]) || $_SESSION["role"] !== "client"
)
?>