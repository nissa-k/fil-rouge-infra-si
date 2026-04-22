<?php

require_once __DIR__ . '/../config/database.php';

class TicketService
{
    private PDO $pdo;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function getAll(): array
    {
        $sql = "
            SELECT
                t.id,
                t.user_id,
                t.title,
                t.description,
                t.priority,
                t.status,
                t.created_at,
                t.updated_at,
                u.full_name,
                u.email
            FROM tickets t
            INNER JOIN users u ON t.user_id = u.id
            ORDER BY t.created_at DESC
        ";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getByStatus(string $status): array
    {
        $allowedStatuses = ['en_cours', 'traitee', 'refusee'];

        if (!in_array($status, $allowedStatuses, true)) {
            return [];
        }

        $sql = "
            SELECT
                t.id,
                t.user_id,
                t.title,
                t.description,
                t.priority,
                t.status,
                t.created_at,
                t.updated_at,
                u.full_name,
                u.email
            FROM tickets t
            INNER JOIN users u ON t.user_id = u.id
            WHERE t.status = :status
            ORDER BY t.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'status' => $status
        ]);

        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $sql = "
            SELECT
                t.id,
                t.user_id,
                t.title,
                t.description,
                t.priority,
                t.status,
                t.created_at,
                t.updated_at,
                u.full_name,
                u.email
            FROM tickets t
            INNER JOIN users u ON t.user_id = u.id
            WHERE t.id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id
        ]);

        return $stmt->fetch();
    }

    public function getByUserId(int $userId): array
    {
        $sql = "
            SELECT
                id,
                user_id,
                title,
                description,
                priority,
                status,
                created_at,
                updated_at
            FROM tickets
            WHERE user_id = :user_id
            ORDER BY created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId
        ]);

        return $stmt->fetchAll();
    }

    public function create(int $userId, string $title, string $description, string $priority): int|false
    {
        $allowedPriorities = ['low', 'medium', 'high'];

        if (!in_array($priority, $allowedPriorities, true)) {
            return false;
        }

        $sql = "
            INSERT INTO tickets (user_id, title, description, priority, status, created_at)
            VALUES (:user_id, :title, :description, :priority, 'en_cours', NOW())
        ";

        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'priority' => $priority
        ]);

        if (!$success) {
            return false;
        }

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, string $title, string $description, string $priority, string $status): bool
    {
        $allowedPriorities = ['low', 'medium', 'high'];
        $allowedStatuses = ['en_cours', 'traitee', 'refusee'];

        if (!in_array($priority, $allowedPriorities, true)) {
            return false;
        }

        if (!in_array($status, $allowedStatuses, true)) {
            return false;
        }

        $sql = "
            UPDATE tickets
            SET
                title = :title,
                description = :description,
                priority = :priority,
                status = :status,
                updated_at = NOW()
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'status' => $status
        ]);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $allowedStatuses = ['en_cours', 'traitee', 'refusee'];

        if (!in_array($status, $allowedStatuses, true)) {
            return false;
        }

        $sql = "
            UPDATE tickets
            SET
                status = :status,
                updated_at = NOW()
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'status' => $status
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM tickets WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }
}