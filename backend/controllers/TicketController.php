<?php

require_once __DIR__ . '/../services/TicketService.php';

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

        echo json_encode($data);

        exit;
    }

    private function getJsonInput(): array
    {
        $raw = file_get_contents('php://input');

        $data = json_decode($raw, true);

        return is_array($data) ? $data : [];
    }

    //creer un ticket

    public function create(): void
    {
        $data = $this->getJsonInput();

        $title = trim($data['title'] ?? '');
        $description = trim($data['description'] ?? '');
        $priority = trim($data['priority'] ?? 'medium');

        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {

            $this->jsonResponse([
                'success' => false,
                'message' => 'Non connecté'
            ], 401);
        }

        if ($title === '' || $description === '') {

            $this->jsonResponse([
                'success' => false,
                'message' => 'Tous les champs sont obligatoires.'
            ], 400);
        }

        $created = $this->ticketService->create(
            $userId,
            $title,
            $description,
            $priority
        );

        if (!$created) {

            $this->jsonResponse([
                'success' => false,
                'message' => 'Erreur création ticket'
            ], 500);
        }

        $this->jsonResponse([
            'success' => true,
            'message' => 'Ticket créé'
        ]);
    }

    //tickets de l'utilisateur connecté

    public function myTickets(): void
    {
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {

            $this->jsonResponse([
                'success' => false,
                'message' => 'Non connecté'
            ], 401);
        }

        $tickets = $this->ticketService->getByUser($userId);

        $this->jsonResponse([
            'success' => true,
            'tickets' => $tickets
        ]);
    }

    //tous les tickets (admin)

    public function index(): void
    {
        $tickets = $this->ticketService->getAll();

        $this->jsonResponse([
            'success' => true,
            'tickets' => $tickets
        ]);
    }

    //mettre à jour le statut d'un ticket (admin)

    public function updateStatus(int $id): void
{
    $data = $this->getJsonInput();

    $status = $data['status'] ?? '';

    $commentaire = $data['commentaire'] ?? '';

    $updated = $this->ticketService->changerStatut(
        $id,
        $status,
        $commentaire
    );

    $this->jsonResponse([
        'success' => $updated
    ]);
}

    public function update(int $id): void
    {
        $data = $this->getJsonInput();

        $updated = $this->ticketService->update(
            $id,
            $data['title'],
            $data['description'],
            $data['priority'],
            $data['status']
        );

        $this->jsonResponse([
            'success' => $updated
        ]);
    }

    public function delete(int $id): void
    {
        $deleted = $this->ticketService->delete($id);

        $this->jsonResponse([
            'success' => $deleted
        ]);
    }
}