<?php

require_once __DIR__ . '/../config/database.php';

class User
{
    private PDO $pdo;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function findByEmail(string $email): array|false
    {
        $sql = "
            SELECT 
                u.id,
                u.email,
                u.password_hash,
                u.full_name,
                u.is_active,
                u.created_at,
                r.name AS role_name
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            WHERE u.email = :email
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'email' => $email
        ]);

        return $stmt->fetch();
    }

    public function findById(int $id): array|false
    {
        $sql = "
            SELECT 
                u.id,
                u.email,
                u.full_name,
                u.is_active,
                u.created_at,
                r.name AS role_name
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            WHERE u.id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id
        ]);

        return $stmt->fetch();
    }
}