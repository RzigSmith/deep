<?php

class UserManager {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllUsers() {
        return $this->db->query("SELECT id, username, email,avatar, role, created_at FROM users")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT id, username, email,avatar, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}