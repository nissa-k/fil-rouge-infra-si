<?php

class AuthMiddleware
{
    public static function handle(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            http_response_code(401);

            echo json_encode([
                'success' => false,
                'message' => 'Non authentifié'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }
    }
}