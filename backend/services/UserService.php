<?php

require_once __DIR__ . '/../config/database.php';

class UserService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // récupérer tous les utilisateurs

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT *
            FROM users
            ORDER BY id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // compter utilisateurs

    public function countAll(): int
    {
        $stmt = $this->db->query("
            SELECT COUNT(*) AS total
            FROM users
        ");

        $result =
            $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)$result['total'];
    }
}