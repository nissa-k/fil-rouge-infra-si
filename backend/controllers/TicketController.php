<?php

require_once __DIR__ . '/../services/TicketService.php';
require_once __DIR__ . '/../helpers/audit.php';

class TicketController
{
    private TicketService $ticketService;

    public function __construct()
    {
        $this->ticketService = new TicketService();
    }

    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function getJsonInput(): array
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        return is_array($data) ? $data : [];
    }

    public function index(): void
    {
        $tickets = $this->ticketService->getAll();

        $this->jsonResponse([
            'success' => true,
            'tickets' => $tickets
        ]);
    }

    public function show(int $id): void
    {
        $ticket = $this->ticketService->getById($id);

        if (!$ticket) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Ticket introuvable.'
            ], 404);
        }

        $this->jsonResponse([
            'success' => true,
            'ticket' => $ticket
        ]);
    }

    public function update(int $id): void
    {
        $data = $this->getJsonInput();

        $title = trim($data['title'] ?? '');
        $description = trim($data['description'] ?? '');
        $priority = $data['priority'] ?? '';
        $status = $data['status'] ?? '';

        if ($title === '' || $description === '' || $priority === '' || $status === '') {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Tous les champs sont obligatoires.'
            ], 400);
        }

        $updated = $this->ticketService->update($id, $title, $description, $priority, $status);

        if (!$updated) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Échec de mise à jour du ticket.'
            ], 400);
        }

        $adminId = $_SESSION['user']['id'] ?? null;
        if ($adminId) {
            logAction((int)$adminId, 'update', 'ticket', $id);
        }

        $this->jsonResponse([
            'success' => true,
            'message' => 'Ticket mis à jour avec succès.'
        ]);
    }

    public function updateStatus(int $id): void
    {
        $data = $this->getJsonInput();
        $status = $data['status'] ?? '';

        if ($status === '') {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Statut obligatoire.'
            ], 400);
        }

        $updated = $this->ticketService->updateStatus($id, $status);

        if (!$updated) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Échec de mise à jour du statut.'
            ], 400);
        }

        $adminId = $_SESSION['user']['id'] ?? null;
        if ($adminId) {
            logAction((int)$adminId, 'update_status', 'ticket', $id);
        }

        $this->jsonResponse([
            'success' => true,
            'message' => 'Statut mis à jour.'
        ]);
    }

    public function delete(int $id): void
    {
        $deleted = $this->ticketService->delete($id);

        if (!$deleted) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Échec de suppression du ticket.'
            ], 400);
        }

        $adminId = $_SESSION['user']['id'] ?? null;
        if ($adminId) {
            logAction((int)$adminId, 'delete', 'ticket', $id);
        }

        $this->jsonResponse([
            'success' => true,
            'message' => 'Ticket supprimé avec succès.'
        ]);
    }
}