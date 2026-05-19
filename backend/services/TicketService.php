<?php

require_once __DIR__ . '/../config/database.php';

class TicketService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // créer un ticket

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

    // tous les tickets

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

    // tickets d'un utilisateur

    public function getByUser(
        int $userId
    ): array {

        $stmt = $this->db->prepare("
            SELECT *
            FROM tickets
            WHERE user_id = ?
            ORDER BY id DESC
        ");

        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ticket par id

    public function getById(
        int $id
    ): ?array {

        $stmt = $this->db->prepare("
            SELECT *
            FROM tickets
            WHERE id = ?
        ");

        $stmt->execute([$id]);

        $ticket =
            $stmt->fetch(PDO::FETCH_ASSOC);

        return $ticket ?: null;
    }

    // mettre à jour un ticket (admin)

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
                status = ?,
                updated_at = NOW()
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

    // mettre à jour le statut d'un ticket

    public function updateStatus(
        int $id,
        string $status
    ): bool {

        $stmt = $this->db->prepare("
            UPDATE tickets
            SET
                status = ?,
                updated_at = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([
            $status,
            $id
        ]);
    }

    // supprimer un ticket

    public function delete(
        int $id
    ): bool {

        $stmt = $this->db->prepare("
            DELETE FROM tickets
            WHERE id = ?
        ");

        return $stmt->execute([$id]);
    }

    // récupérer tickets technicien par statut

    public function getByTechAndStatus(
        int $technicienId,
        string $status
    ): array {

        $stmt = $this->db->prepare("
            SELECT
                tickets.*,
                users.full_name AS client
            FROM tickets
            JOIN users
            ON users.id = tickets.user_id
            WHERE tickets.technicien_id = ?
            AND tickets.status = ?
            ORDER BY tickets.created_at DESC
        ");

        $stmt->execute([
            $technicienId,
            $status
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // traiter / refuser ticket

    public function changerStatut(
        int $id,
        string $status,
        string $commentaire
    ): bool {

        $stmt = $this->db->prepare("
            UPDATE tickets
            SET
                status = ?,
                commentaire = ?,
                updated_at = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([
            $status,
            $commentaire,
            $id
        ]);
    }

    // statistiques par statut

    public function countByStatus(
        string $status
    ): int {

        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM tickets
            WHERE status = ?
        ");

        $stmt->execute([$status]);

        $result =
            $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)$result['total'];
    }

    // total tickets

    public function countAll(): int
    {
        $stmt = $this->db->query("
            SELECT COUNT(*) AS total
            FROM tickets
        ");

        $result =
            $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)$result['total'];
    }
}