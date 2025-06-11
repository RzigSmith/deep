<?php
require_once '../includes/db.php';
require_once '../welcome.php';
$db = loginDatabase();
$total = $_GET['orders'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smith Collection</title>
</head>

<body>
    <div class="modal-overlay" id="orderModal"></div>
    <div class="modal">
        <div class="modal">
            <button class="close-btn" aria-label="fermer la fenetre" onclick="closeOrderModal()">&times;</button>
            <h2 id="mondaTitle">Confirmer votre commande</h2>

            <form action="order_items.php" method="post" id="orderForm">

                <div class="total">
                    <strong>Quantité totale :</strong> <?= array_sum($_SESSION['cart']) ?><br>
                    <strong>Prix total :</strong> <?= $total ?> $
                </div>
                <label for="name">Nom</label>
                <input type="name" name="userName" value="<?= $_SESSION['username'] ?>">
                <label for="email">email</label>
                <input type="email" name="email" value="<?= $_SESSION["email"] ?>">
                <label for="user_id">ID utilisateur</label>
                <input type="id" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                <label for="total_amount">Montant total</label>
                <input type="price" name="total_amount" value="<?= $total ?>">
                <label for="status">Statut de la commande</label>
                <select name="status" id="status">
                    <option value="en attente">En attente</option>
                    <option value="en cours">En cours</option>
                    <option value="livrée">Livrée</option>
                    <option value="annulée">Annulée</option>

                </select>
                <label for="customer_address">Adresse de livraison</label>
                <input type="text" name="customer_address" placeholder="Entrez votre adresse de livraison" required>
               

                <button type="submit" >Confirmer la commande</button>
              
            </form>

            <?php
            $error = [];
            $success = false;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Récupérer les données du formulaire
                $user_id = $_POST['user_id'] ?? '';
                $total_amount = $_POST['total_amount'] ?? '';
                $status = $_POST['status'] ?? '';
                $customer_address = $_POST['customer_address'] ?? '';
                // Vérifier si l'utilisateur est connecté
                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];
                } else {
                    $user_id = '';
                }
                // Vérifier si les données sont valides
                $error = false;
                if (empty($user_id) || empty($total_amount) || empty($status)) {
                    $error = true;
                }

                // Initialiser un tableau d'erreurs
                $errors = [];

                // Vérifier si l'utilisateur est connecté
                if (empty($user_id)) {
                    $errors[] = 'Utilisateur non connecté.';
                }

                // Vérifier si le montant total est valide
                if (!is_numeric($total_amount) || $total_amount <= 0) {
                    $errors[] = 'Montant total invalide.';
                }

                // Vérifier si le statut est valide
                $valid_statuses = ['en attente', 'en cours', 'livrée', 'annulée'];
                if (!in_array($status, $valid_statuses)) {
                    $errors[] = 'Statut de commande invalide.';
                }

                // Si aucune erreur, procéder à l'insertion dans la base de données
                if (!$error && empty($errors)) {

                    $stmt = $db->prepare('INSERT INTO orders(user_id, order_date, total_amount, status, customer_address) VALUES (?, NOW(), ?, ? , ?)');
                    $stmt->execute([$user_id, $total_amount, $status, $customer_address]);

                    $success = true;
                    $_SESSION['orders'] = [
                        'id' => $db->lastInsertId(),
                        'user_id' => $user_id,
                        'total_amout' => $total_amount,
                        'status' => $status,
                        'order_date' => date('Y-m-d H:i:s'),
                        'custome_address' => $customer_address
                    ];
                }
                // Vérifier si des erreurs sont présentes
                if (!empty($errors)) {
                    echo '<div class="error">';
                    echo '<ul>';
                    foreach ($errors as $error) {
                        echo '<li>' . htmlspecialchars($error) . '</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                } else {
                    echo '<div class="success">Commande confirmée avec succès !</div>';
                }
         
                header('Location:order_items.php?order_items=' . $total_amount);
             }
            ?>
        </div>
    </div>
</body>

</html>