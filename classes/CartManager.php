<?php
// require_once realpath(__DIR__ . 'Database.php'); // Ensure the correct path to Database.php
$db= new PDO("mysql:host=localhost;dbname=ecommerce_db", "root","");
class CartManager {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Retrieve the cart for a specific user
    public function getCart($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, p.name, p.price, p.image, p.stock 
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = :user_id
            ");
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Add a product to the cart
    public function addToCartBtns($userId, $productId, $quantity = 1) {
        try {
            // Check product stock
            $product = $this->getProduct($productId);
            if (!$product || $product['stock'] < $quantity) {
                throw new Exception('Stock insuffisant');
            }

            // Check if the item already exists in the cart
            $existingItem = $this->getCartItem($userId, $productId);

            if ($existingItem) {
                $newQuantity = $existingItem['quantity'] + $quantity;
                return $this->updateCartItem($userId, $productId, $newQuantity);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO cart (user_id, product_id, quantity)
                    VALUES (:user_id, :product_id, :quantity)
                ");
                return $stmt->execute([
                    ':user_id' => $userId,
                    ':product_id' => $productId,
                    ':quantity' => $quantity
                ]);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Get a specific product by ID
    private function getProduct($productId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->execute([':id' => $productId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    // Get a specific cart item for a user
    private function getCartItem($userId, $productId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM cart 
                WHERE user_id = :user_id AND product_id = :product_id
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    // Update the quantity of an existing cart item
    public function updateCartItem($userId, $productId, $quantity) {
        try {
            // Vérifier si l'article existe déjà dans le panier
            $stmt = $this->db->prepare("SELECT id FROM cart WHERE user_id = :user_id AND product_id = :product_id");
            $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
            $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cartItem) {
                // Si l'article n'existe pas, retourner une erreur
                throw new Exception('Article non trouvé dans le panier', 404);
            }

            // Mettre à jour la quantité de l'article
            $stmt = $this->db->prepare("
                UPDATE cart 
                SET quantity = :quantity 
                WHERE user_id = :user_id AND product_id = :product_id
            ");
            $stmt->execute([
                ':quantity' => $quantity,
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);

            return true; // Mise à jour réussie
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false; // En cas d'erreur
        }
    }

    // Remove an item from the cart
    public function removeFromCart($userId, $productId) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM cart 
                WHERE user_id = :user_id AND product_id = :product_id
            ");
            return $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Clear the entire cart for a user
    public function clearCart($userId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM cart WHERE user_id = :user_id");
            return $stmt->execute([':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}