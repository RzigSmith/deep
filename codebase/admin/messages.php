<?php

include_once "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_user_id'], $_POST['reply_message'])) {
    $user_id = intval($_POST['reply_user_id']);
    $message = trim($_POST['reply_message']);
    if ($user_id && $message) {
        $stmt = $db->prepare("INSERT INTO notifications (user_id, type, message) VALUES (?, 'admin_reply', ?)");
        $stmt->execute([$user_id, $message]);
        echo "<script>alert('Réponse envoyée !');window.location.href=window.location.href;</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smith collection</title>
    <link rel="stylesheet" href="messages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/od.css">
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
        <div class="notify-bell" id="notifyBell">
            <i class="fas fa-bell"></i>
            <span class="notify-count" id="notifyCount"></span>
            <div class="notify-dropdown" id="notifyDropdown"></div>
        </div>
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
                            echo "<td>
                                <button class='btn-reply' data-userid='" . intval($donnees['user_id']) . "' data-username='" . htmlspecialchars($donnees['name']) . "'>Répondre</button>
                            </td>";
                            echo "</tr>";
                        }
                        $reponse->closeCursor();
                        ?>
                    </tbody>
                </table>
            </section>
        </div>

    </main>
    <div id="replyModal" class="modal" style="display:none;">
        <form id="replyForm" method="post">
            <h3>Répondre à <span id="replyUser"></span></h3>
            <input type="hidden" name="reply_user_id" id="replyUserId">
            <textarea name="reply_message" id="replyMessage" rows="4" placeholder="Votre réponse..." required></textarea>
            <button type="submit">Envoyer</button>
            <button type="button" id="closeReplyModal">Annuler</button>
        </form>
    </div>
    <script src="../assets/js/ct.js"></script>
    <script src="../assets/js/od.js"></script>
</body>

</html>