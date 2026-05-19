<?php

require_once __DIR__ . '/../services/MessageService.php';

class MessageController
{
    private MessageService $messageService;

    public function __construct()
    {
        $this->messageService =
            new MessageService();
    }

    private function jsonResponse(
        array $data,
        int $status = 200
    ): void {

        http_response_code($status);

        echo json_encode($data);

        exit;
    }

    private function getJsonInput(): array
    {
        return json_decode(
            file_get_contents("php://input"),
            true
        ) ?? [];
    }

    public function index(int $ticketId): void
    {
        $messages =
            $this->messageService
                ->getByTicket($ticketId);

        $this->jsonResponse([
            'success' => true,
            'messages' => $messages
        ]);
    }

    public function create(int $ticketId): void
    {
        $data = $this->getJsonInput();

        $message =
            trim($data['message'] ?? '');

        $userId =
            $_SESSION['user']['id'];

        if ($message === '') {

            $this->jsonResponse([
                'success' => false
            ], 400);
        }

        $created =
            $this->messageService
                ->create(
                    $ticketId,
                    $userId,
                    $message
                );

        $this->jsonResponse([
            'success' => $created
        ]);
    }
}