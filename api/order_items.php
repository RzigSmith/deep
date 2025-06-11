<?php
require_once '../includes/db.php';
require_once '../welcome.php';
$db = loginDatabase();
$total = $_GET['order_items'] ?? '';

$errors = [];
$success = false;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>order_items</title>
</head>

<body>
    <form action="order_items.php" method="post" id="orderForm">
        <h2>Confirmer votre commande</h2>
        <label for="oder_id">Id command</label>
        <input type="text" name="order_id" value="<?= $_SESSION['order_id'] ?? '' ?>" readonly>
        <label for="product_id">Id du produit</label>
        <input type="text" name="product_id" value="<?= implode(', ', array_keys($_SESSION['cart'])) ?>" readonly>
        <label for="quantity">Quantité</label>
        <input type="text" name="quantity" value="<?= array_sum($_SESSION['cart']) ?>" readonly>
        <label for="price">Prix</label>
        <input type="text" name="price" value="<?= $total ?>" readonly>
    </form>
    <button type="submit" form="orderForm" href="../order.php?order<?= $total ?>">Confirmer la commande</button>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données du formulaire
        $order_id = $db->lastInsertId();
        $product_id = $_POST['product_id'] ?? '';
        $quantity = $_POST['quantity'] ?? '';
        $price = $_POST['price'] ?? '';
        

        // Vérifier si l'ID de commande est passé en POST
        if (isset($_POST['order_id']) && !empty($_POST['order_id'])) {
            $order_id = $_POST['order_id'];
        } elseif (isset($_SESSION['order_id'])) {
            $order_id = $_SESSION['order_id'];
        } else {
            // Si l'ID de commande n'est pas trouvé, on le crée
            $stmt = $db->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $total, 'en attente']);
            $success = true;
            $_SESSION['orders'] = [
                'id' => $db->lastInsertId(),
                'order_id' => $_SESSION['order_id'] ?? '',
                // 'total_amout' => $_SESSION['total_amount'] ?? $total,
                'quatity' => $_SESSION['quantity'] ?? array_sum($_SESSION['cart']),
                'product_id' => $_SESSION['product_id'] ?? implode(', ', array_keys($_SESSION['cart'])),
                'user_id' => $_SESSION['user_id'],
                'price' => $_SESSION['price'] ?? $total,
               
            ];
        }
        // Vérifier si l'ID de commande est défini
        if (isset($_POST['order_id'])) {
            $order_id = $_POST['order_id'];
        } else {
            echo "<p>Erreur : ID de commande manquant.</p>";
            exit;
        }
        //Enregistrer les articles de la commande
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            // Récupérer le prix du produit
            $stmt = $db->prepare('SELECT price, id FROM products WHERE id = ?');
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($product) {
                $price = $product['price'];
            } else {
                $errors[] = 'Produit introuvable pour l\'ID ' . $product_id;
                continue;
            }
            $product_id = $_POST['product_id'] ?? '';
            $quantity = $_POST['quantity'] ?? '';
            $price = $_POST['price'] ?? '';

            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                // Vérifier si le produit existe dans la base de données
                $stmt = $db->prepare("SELECT price FROM products WHERE id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($product) {
                    $price = $product['price'];
                } else {
                    echo "<p>Produit avec ID $product_id non trouvé.</p>";
                    continue;
                }
            }
            
            // Vérifier si la quantité est valide
            if (!is_numeric($quantity) || $quantity <= 0) {
                echo "<p>Quantité invalide pour le produit avec ID $product_id.</p>";
                continue;
            }

            // Valider les données
            if (empty($order_id) || empty($product_id) || empty($quantity) || empty($price)) {
                echo "<p>Veuillez remplir tous les champs.</p>";
            } else {
                // Enregistrer la commande dans la base de données
                $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$order_id, $product_id, $quantity, $price])) {
                    echo "<p>Commande confirmée avec succès !</p>";
                } else {
                    echo "<p>Erreur lors de la confirmation de la commande.</p>";
                }
                // Calculer le montant total de la commande
                $total_amount = $quantity * $price;
                
                // Mettre à jour le statut de la commande
                $stmt = $db->prepare("UPDATE orders SET status = 'En cours' WHERE id = ?");
                $stmt->execute([$order_id]);
                echo "<p>Statut de la commande mis à jour.</p>";
                // Enregistrer les informations de la commande dans la session
                $_SESSION['order'] = [
                    'id' => $order_id,
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'status' => 'En cours'
                ];


                // Vider le panier après la confirmation de la commande
                unset($_SESSION['cart']);
                // Rediriger vers la page de confirmation ou d'historique des commandes
                header('Location: ../' . 'order.php?orders=' . $total_amount);
            }
        }
    }
    ?>
</body>

</html>