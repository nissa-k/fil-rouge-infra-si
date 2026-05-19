<?php

require_once __DIR__ . '/../config/database.php';

class MessageService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getByTicket(int $ticketId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                ticket_messages.*,
                users.full_name
            FROM ticket_messages
            JOIN users
            ON users.id = ticket_messages.user_id
            WHERE ticket_id = ?
            ORDER BY created_at ASC
        ");

        $stmt->execute([$ticketId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(
        int $ticketId,
        int $userId,
        string $message
    ): bool {

        $stmt = $this->db->prepare("
            INSERT INTO ticket_messages
            (
                ticket_id,
                user_id,
                message
            )
            VALUES (?, ?, ?)
        ");

        return $stmt->execute([
            $ticketId,
            $userId,
            $message
        ]);
    }
}