<?php
// filepath: e:\wamp64\www\ghost\deep\classes\ProductManager.php
class ProductManager
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllProducts()
    {
        return $this->db->query("
         SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN products c ON p.description")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

  
}
