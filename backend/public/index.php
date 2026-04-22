<?php

// Activer les erreurs pour le développement
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Réponse JSON par défaut
header('Content-Type: application/json; charset=utf-8');

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gestion globale des exceptions
set_exception_handler(function ($e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur.',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);

    exit;
});

// Gestion des erreurs fatales
register_shutdown_function(function () {

    $error = error_get_last();

    if ($error !== null) {

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Erreur fatale.',
            'error' => $error['message']
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }
});

// Charger les routes API
require_once __DIR__ . '/../routes/api.php';