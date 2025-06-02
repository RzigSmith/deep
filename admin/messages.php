<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=ecommerce_db", "root", "");
} catch (PDOException $e) {
    echo "erreur lors de la connection à la base des donées". $e->getMessage();
}

$reponse = $db->query("SELECT *FROM contacts ORDER BY date_message");
while ($donnees = $reponse->fetch()) {
    echo"".'<br>'. $donnees["name"] . '<br>' .$donnees['email'] .'<br>'. $donnees['message'];
}

$reponse->closeCursor();
?>