<?php

class RoleMiddleware
{
    public static function handle(string $role): void
    {
        // Assure que l'utilisateur est authentifié
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifie que l'utilisateur a le rôle requis
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== $role) {

            http_response_code(403);

            echo json_encode([
                'success' => false,
                'message' => 'Accès interdit'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }
    }
}