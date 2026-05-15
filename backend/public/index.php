<?php

session_start();

header("Content-Type: application/json");

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/UserController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$uri = str_replace(
    '/fil-rouge-infra-si/backend/public/index.php',
    '',
    $uri
);

$method = $_SERVER['REQUEST_METHOD'];

$authController = new AuthController();
$userController = new UserController();


// =========================
// AUTH
// =========================

if ($uri === '/api/login' && $method === 'POST') {

    $authController->login();
    exit;
}

if ($uri === '/api/verify-2fa' && $method === 'POST') {

    $authController->verify2FA();
    exit;
}

if ($uri === '/api/logout' && $method === 'POST') {

    $authController->logout();
    exit;
}

if ($uri === '/api/me' && $method === 'GET') {

    $authController->me();
    exit;
}

if ($uri === '/api/change-password' && $method === 'POST') {

    $authController->changePassword();
    exit;
}

if ($uri === '/api/forgot-password' && $method === 'POST') {

    $authController->forgotPassword();
    exit;
}

if ($uri === '/api/reset-password' && $method === 'POST') {

    $authController->resetPassword();
    exit;
}


// =========================
// USERS
// =========================

if ($uri === '/api/users' && $method === 'GET') {

    $userController->index();
    exit;
}

if ($uri === '/api/users' && $method === 'POST') {

    $userController->create();
    exit;
}


// =========================
// 404
// =========================

http_response_code(404);

echo json_encode([
    "success" => false,
    "message" => "Route non trouvée"
]);