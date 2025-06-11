<?php

require_once '../includes/db.php';
require_once '../welcome.php';
$db = loginDatabase();

$total = $_GET['orders'] ?? '';
$errors = [];
$success = false;

// Protéger l'accès : vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// POST : traitement de la commande
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et valider les données
    $user_id = $_SESSION['user_id'];
    $total_amount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;
    $status = $_POST['status'] ?? '';
    $customer_address = trim($_POST['customer_address'] ?? '');

    $valid_statuses = ['en attente', 'en cours', 'livrée', 'annulée'];

    if ($total_amount <= 0) $errors[] = 'Montant total invalide.';
    if (!in_array($status, $valid_statuses)) $errors[] = 'Statut de commande invalide.';
    if (empty($customer_address)) $errors[] = 'Adresse de livraison requise.';

    if (empty($errors)) {
        $stmt = $db->prepare('INSERT INTO orders(user_id, order_date, total_amount, status, customer_address) VALUES (?, NOW(), ?, ?, ?)');
        $stmt->execute([$user_id, $total_amount, $status, $customer_address]);

        $success = true;
        $_SESSION['order_id'] = $db->lastInsertId();

        header('Location: order_items.php?order_items=' . $total_amount);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smith Collection</title>
</head>
<body>
    <div class="modal-overlay" id="orderModal"></div>
    <div class="modal">
        <button class="close-btn" aria-label="fermer la fenetre" onclick="closeOrderModal()">&times;</button>
        <h2 id="mondaTitle">Confirmer votre commande</h2>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif ($success): ?>
            <div class="success">Commande confirmée avec succès !</div>
        <?php endif; ?>

        <form action="order.php" method="post" id="orderForm">
            <div class="total">
                <strong>Quantité totale :</strong> <?= array_sum($_SESSION['cart'] ?? []) ?><br>
                <strong>Prix total :</strong> <?= htmlspecialchars($total) ?> $
            </div>
            <label for="name">Nom</label>
            <input type="text" name="userName" value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>" readonly>
            <label for="email">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_SESSION["email"] ?? '') ?>" readonly>
            <label for="user_id">ID utilisateur</label>
            <input type="text" name="user_id" value="<?= htmlspecialchars($_SESSION['user_id'] ?? '') ?>" readonly>
            <label for="total_amount">Montant total</label>
            <input type="text" name="total_amount" value="<?= htmlspecialchars($total) ?>" readonly>
            <label for="status">Statut de la commande</label>
            <select name="status" id="status" required>
                <option value="en attente">En attente</option>
                <option value="en cours">En cours</option>
                <option value="livrée">Livrée</option>
                <option value="annulée">Annulée</option>
            </select>
            <label for="customer_address">Adresse de livraison</label>
            <input type="text" name="customer_address" placeholder="Entrez votre adresse de livraison" required>
            <button type="submit">Confirmer la commande</button>
        </form>
    </div>
</body>
</html>