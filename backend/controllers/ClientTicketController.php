<?php

require_once __DIR__ . '/../services/TicketService.php';
require_once __DIR__ . '/../helpers/audit.php';

class ClientTicketController
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

    public function myTickets(): void
    {
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Non authentifié.'
            ], 401);
        }

        $tickets = $this->ticketService->getByUserId((int) $userId);

        $this->jsonResponse([
            'success' => true,
            'tickets' => $tickets
        ]);
    }

    public function create(): void
    {
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Non authentifié.'
            ], 401);
        }

        $data = $this->getJsonInput();

        $title = trim($data['title'] ?? '');
        $description = trim($data['description'] ?? '');
        $priority = $data['priority'] ?? '';

        if ($title === '' || $description === '' || $priority === '') {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Tous les champs sont obligatoires.'
            ], 400);
        }

        if (mb_strlen($title) > 150) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Le titre ne doit pas dépasser 150 caractères.'
            ], 400);
        }

        if (mb_strlen($description) > 1000) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'La description ne doit pas dépasser 1000 caractères.'
            ], 400);
        }

        $allowedPriorities = ['low', 'medium', 'high'];

        if (!in_array($priority, $allowedPriorities, true)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Priorité invalide.'
            ], 400);
        }

        $ticketId = $this->ticketService->create((int) $userId, $title, $description, $priority);

        if (!$ticketId) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la création du ticket.'
            ], 500);
        }

        logAction((int) $userId, 'create', 'ticket', (int) $ticketId);

        $this->jsonResponse([
            'success' => true,
            'message' => 'Ticket créé avec succès.'
        ], 201);
    }
}