<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=ecommerce_db", "root", "");
} catch (PDOException $e) {
    echo "erreur lors de la connection à la base des donées". $e->getMessage();
}

// $reponse = $db->query("SELECT *FROM contacts ORDER BY date_message");
// while ($donnees = $reponse->fetch()) {
//     echo"".'<br>'. $donnees["name"] . '<br>' .$donnees['email'] .'<br>'. $donnees['message'];
// }

// $reponse->closeCursor();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smith collection</title>
    <link rel="stylesheet" href="messages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="sidebar">
        <h2>Mon Dashboard</h2>
        <ul>
            <li><a href="#">Accueil</a></li>
            <li><a href="users.php">Utilisateurs</a></li>
            <li><a href="dashboard.php">Statistiques</a></li>
            <li><a href="admin_profile.php"></a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </div>
    <main class="container">
        <div class="main">
            <section class="user-list">
                <h2>Messages des utilisateurs</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $reponse = $db->query("SELECT * FROM contacts ORDER BY date_message DESC");
                        while ($donnees = $reponse->fetch()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($donnees["name"]) . "</td>";
                            echo "<td>" . htmlspecialchars($donnees['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($donnees['message']) . "</td>";
                            echo "</tr>";
                        }
                        $reponse->closeCursor();
                        ?>
                    </tbody>
                </table>
            </section>
        </div>


</body>
</html>