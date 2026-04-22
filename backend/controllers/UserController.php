<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/audit.php';

class UserController
{
    private PDO $pdo;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function index(): void
    {
        $sql = "
            SELECT 
                u.id,
                u.full_name,
                u.email,
                u.is_active,
                u.created_at,
                r.name AS role_name
            FROM users u
            LEFT JOIN user_roles ur ON ur.user_id = u.id
            LEFT JOIN roles r ON r.id = ur.role_id
            ORDER BY u.id DESC
        ";

        $stmt = $this->pdo->query($sql);
        $users = $stmt->fetchAll();

        $this->jsonResponse([
            'success' => true,
            'users' => $users
        ]);
    }

    public function delete(int $id): void
    {
        $currentUserId = $_SESSION['user']['id'] ?? null;

        if (!$currentUserId) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Non authentifié.'
            ], 401);
        }

        if ((int)$id === (int)$currentUserId) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.'
            ], 400);
        }

        $deleteRoles = "DELETE FROM user_roles WHERE user_id = :id";
        $stmtRoles = $this->pdo->prepare($deleteRoles);
        $stmtRoles->execute(['id' => $id]);

        $deleteUser = "DELETE FROM users WHERE id = :id";
        $stmtUser = $this->pdo->prepare($deleteUser);
        $stmtUser->execute(['id' => $id]);

        logAction((int)$currentUserId, 'delete', 'user', $id);

        $this->jsonResponse([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès.'
        ]);
    }
}