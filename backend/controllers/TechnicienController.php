<?php

require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../middlewares/RoleMiddleware.php';
require_once __DIR__ . '/../services/TicketService.php';

class TechnicienController
{
    private TicketService $ticketService;

    public function __construct()
    {
        $this->ticketService = new TicketService();
    }

    private function checkAuth(): ?array
    {
        $user = AuthMiddleware::check();

        if (!$user) {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Non authentifié"]);
            return null;
        }

        if (!RoleMiddleware::check($user, 'technicien')) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Accès réservé aux techniciens"]);
            return null;
        }

        return $user;
    }

    // GET /api/technicien/tickets/en-cours
    public function getEnCours(): void
    {
        $user = $this->checkAuth();
        if (!$user) return;

        $tickets = $this->ticketService->getByTechAndStatus($user['id'], 'en_cours');

        echo json_encode(["success" => true, "tickets" => $tickets]);
    }

    // GET /api/technicien/tickets/traitees
    public function getTraitees(): void
    {
        $user = $this->checkAuth();
        if (!$user) return;

        $tickets = $this->ticketService->getByTechAndStatus($user['id'], 'traite');

        echo json_encode(["success" => true, "tickets" => $tickets]);
    }

    // GET /api/technicien/tickets/refusees
    public function getRefusees(): void
    {
        $user = $this->checkAuth();
        if (!$user) return;

        $tickets = $this->ticketService->getByTechAndStatus($user['id'], 'refuse');

        echo json_encode(["success" => true, "tickets" => $tickets]);
    }

    // PUT /api/technicien/tickets/{id}/traiter
    public function traiter(int $id): void
    {
        $user = $this->checkAuth();
        if (!$user) return;

        $body        = json_decode(file_get_contents('php://input'), true);
        $commentaire = trim($body['commentaire'] ?? '');

        $ticket = $this->ticketService->getById($id);

        if (!$ticket) {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Ticket introuvable"]);
            return;
        }

        if ((int)$ticket['technicien_id'] !== (int)$user['id']) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Ce ticket ne vous est pas assigné"]);
            return;
        }

        if ($ticket['status'] !== 'en_cours') {
            http_response_code(409);
            echo json_encode(["success" => false, "message" => "Ce ticket n'est plus en cours"]);
            return;
        }

        $ok = $this->ticketService->changerStatut($id, 'traite', $commentaire);

        if ($ok) {
            echo json_encode(["success" => true, "message" => "Ticket marqué comme traité"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour"]);
        }
    }

    // PUT /api/technicien/tickets/{id}/refuser
    public function refuser(int $id): void
    {
        $user = $this->checkAuth();
        if (!$user) return;

        $body  = json_decode(file_get_contents('php://input'), true);
        $motif = trim($body['motif'] ?? '');

        if (empty($motif)) {
            http_response_code(422);
            echo json_encode(["success" => false, "message" => "Le motif de refus est obligatoire"]);
            return;
        }

        $ticket = $this->ticketService->getById($id);

        if (!$ticket) {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Ticket introuvable"]);
            return;
        }

        if ((int)$ticket['technicien_id'] !== (int)$user['id']) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Ce ticket ne vous est pas assigné"]);
            return;
        }

        if ($ticket['status'] !== 'en_cours') {
            http_response_code(409);
            echo json_encode(["success" => false, "message" => "Ce ticket n'est plus en cours"]);
            return;
        }

        $ok = $this->ticketService->changerStatut($id, 'refuse', $motif);

        if ($ok) {
            echo json_encode(["success" => true, "message" => "Ticket refusé"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour"]);
        }
    }
}
