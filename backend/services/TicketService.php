<?php

require_once __DIR__ . '/../config/database.php';

class TicketService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    //creer un ticket
    
    public function create(
        int $userId,
        string $title,
        string $description,
        string $priority
    ): bool {

        $stmt = $this->db->prepare("
            INSERT INTO tickets
            (
                user_id,
                title,
                description,
                priority,
                status,
                created_at
            )
            VALUES (?, ?, ?, ?, 'en_cours', NOW())
        ");

        return $stmt->execute([
            $userId,
            $title,
            $description,
            $priority
        ]);
    }

    //tout les tickets

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT
                tickets.*,
                users.full_name,
                users.email
            FROM tickets
            JOIN users
            ON tickets.user_id = users.id
            ORDER BY tickets.id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //tickets d'un utilisateur

    public function getByUser($userId): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM tickets
            WHERE user_id = ?
            ORDER BY id DESC
        ");

        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //ticket par id

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM tickets
            WHERE id = ?
        ");

        $stmt->execute([$id]);

        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        return $ticket ?: null;
    }

    //mettre à jour un ticket (admin)

    public function update(
        int $id,
        string $title,
        string $description,
        string $priority,
        string $status
    ): bool {

        $stmt = $this->db->prepare("
            UPDATE tickets
            SET
                title = ?,
                description = ?,
                priority = ?,
                status = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $title,
            $description,
            $priority,
            $status,
            $id
        ]);
    }

    //mettre à jour le statut d'un ticket (admin)

    public function updateStatus(
        int $id,
        string $status
    ): bool {

        $stmt = $this->db->prepare("
            UPDATE tickets
            SET status = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $status,
            $id
        ]);
    }

    //supprimer un ticket (admin)

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM tickets
            WHERE id = ?
        ");

        return $stmt->execute([$id]);
    }
}