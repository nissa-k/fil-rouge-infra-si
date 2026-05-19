<?php

require_once __DIR__ . '/../config/database.php';

class User {

    private $db;

    // Constructeur pour initialiser la connexion à la base de données
    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}