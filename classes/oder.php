<?php
require_once '../includes/db.php';
require_once 'ProductManager.php';
$db = loginDatabase();
$productManager = new ProductManager($db);
$products = $productManager->getAllProducts();
$orderSuccess = false;
$orderError = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Basic validation and sanitation
    $customerName = trim($_POST['customer_name'] ?? '');
    $customerEmail = trim($_POST['customer_email'] ?? '');
    $customerAddress = trim($_POST['customer_address'] ?? '');
    $productIds = $_POST['product_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    if ($customerName === '' || $customerEmail === '' || $customerAddress === '') {
        $orderError = "Veuillez remplir tous les champs du formulaire.";
    } elseif (empty($productIds) || empty($quantities)) {
        $orderError = "Votre panier est vide.";
    } else {
        // Insert order into orders table
        try {
            $db->beginTransaction();
            $stmtOrder = $db->prepare("INSERT INTO orders (customer_name, customer_email, customer_address, order_date) VALUES (?, ?, ?, NOW())");
            $stmtOrder->execute([$customerName, $customerEmail, $customerAddress]);
            $orderId = $db->lastInsertId();
            $stmtItem = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            // Loop through products and quantities
            foreach ($productIds as $index => $prodId) {
                $quantity = intval($quantities[$index]);
                if ($quantity > 0) {
                    // Fetch product price to store at order time
                    $product = $productManager->getProductById($prodId);
                    if ($product) {
                        $stmtItem->execute([$orderId, $prodId, $quantity, $product['price']]);
                    }
                }
            }

            $db->commit();
            $orderSuccess = true;
        } catch (Exception $e) {
            $db->rollBack();
            $orderError = "Erreur lors de l'enregistrement de la commande. Veuillez réessayer.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Nos Produits</title>
    <link rel="stylesheet" href="../assets/css/shopping.css" />
    <style>
        /* Modal styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            max-width: 500px;
            width: 90%;
            font-family: 'Poppins', sans-serif;
            color: #1f2937;
        }

        .modal h2 {
            font-size: 1.75rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .modal label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            margin-top: 1rem;
        }

        .modal input[type="text"],
        .modal input[type="email"],
        .modal textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-family: inherit;
            color: #374151;
        }

        .modal textarea {
            resize: vertical;
            min-height: 80px;
        }

        .modal button {
            margin-top: 1.5rem;
            background-color: #111827;
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.25rem;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .modal .close-btn {
            background: transparent;
            color: #6b7280;
            font-weight: 600;
            font-size: 1.25rem;
            border: none;
            cursor: pointer;
            position: absolute;
            top: 1rem;
            right: 1rem;
        }

        /* Disabled background scroll when modal open */
        body.modal-open {
            overflow: hidden;
        }

        /* Error and success messages */
        .message {
            max-width: 500px;
            margin: 1rem auto;
            font-weight: 600;
            text-align: center;
            font-family: 'Poppins', sans-serif;
        }

        .message.error {
            color: #b91c1c;
        }

        .message.success {
            color: #15803d;
        }

        /* Product card updates for form button alignment */
        .product-card .btn-group {
            margin-top: 1rem;
        }

        .message {
            max-width: 500px;
            margin: 1rem auto;
            font-weight: 600;
            text-align: center;
            font-family: 'Poppins', sans-serif;
        }

        .message.error {
            color: #b91c1c;
        }

        .message.success {
            color: #15803d;
        }

        /* Product card updates for form button alignment */
        .product-card .btn-group {
            margin-top: 1rem;
        }

        .product-card .btn-order {
            background-color: #111827;
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .product-card .btn-order:hover {
            background-color: #374151;
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <div class="logo">Smith<span>Collection</span></div>
                <ul class="nav-links">
                    <li><a href="../index.php">Accueil</a></li>
                    <li><a href="product.php">Boutique</a></li>
                    <li><a href="../api/cart.php">Panier</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <section class="container section-title">
        <h2>Nos produits</h2>
    </section>
    <?php if ($orderError): ?>
        <p class="message error"><?= htmlspecialchars($orderError) ?></p>
    <?php elseif ($orderSuccess): ?>
        <p class="message success">Votre commande a été passée avec succès ! Merci.</p>
    <?php endif; ?>
    <section class="container products" id="product_list">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                    <div class="product-price"><?= number_format($product['price'], 2, ',', ' ') ?> €</div>
                    <p class="product-description"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    <div class="btn-group">
                        <button
                            class="btn-order"
                            data-product-id="<?= $product['id'] ?>"
                            data-product-name="<?= htmlspecialchars($product['name']) ?>"
                            data-product-price="<?= $product['price'] ?>"
                            onclick="openOrderModal(<?= $product['id'] ?>, '<?= htmlspecialchars(addslashes($product['name'])) ?>', <?= $product['price'] ?>)">Passer la commande</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
    <!-- Order Confirmation Modal -->
    <div class="modal-overlay" id="orderModal">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <button class="close-btn" aria-label="Fermer la fenêtre" onclick="closeOrderModal()">&times;</button>
            <h2 id="modalTitle">Confirmer votre commande</h2>
            <form method="POST" action="product.php" id="orderForm">
                <input type="hidden" name="place_order" value="1" />
                <input type="hidden" name="product_id[]" id="product_id_input" value="" />
                <input type="hidden" name="quantity[]" id="quantity_input" value="1" />
                <p>Produit sélectionné: <strong id="selectedProductName"></strong></p>
                <label for="customer_name">Nom complet</label>
                <input type="text" id="customer_name" name="customer_name" required />
                <label for="customer_email">Adresse e-mail</label>
                <input type="email" id="customer_email" name="customer_email" required />
                <label for="customer_address">Adresse de livraison</label>
                <textarea id="customer_address" name="customer_address" required></textarea>
                <button type="submit">Confirmer la commande</button>
            </form>
        </div>
    </div>
    <script>
        function openOrderModal(productId, productName, productPrice) {
            document.getElementById('product_id_input').value = productId;
            document.getElementById('quantity_input').value = 1; // default quantity 1
            document.getElementById('selectedProductName').textContent = productName;
            document.getElementById('orderModal').classList.add('active');
            document.body.classList.add('modal-open');
        }

        function closeOrderModal() {
            document.getElementById('orderModal').classList.remove('active');
            document.body.classList.remove('modal-open');
            // Clear form fields
            document.getElementById('orderForm').reset();
        } // Optional: Close modal on overlay click
        document.getElementById('orderModal').addEventListener('click', function(event) {
            if (event.target === this) closeOrderModal();
        });
    </script>
</body>

</html>