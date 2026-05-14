<?php

session_start();

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/TicketController.php';
require_once __DIR__ . '/../controllers/ClientTicketController.php';

require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../middlewares/RoleMiddleware.php';

/* ================= INIT ================= */

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

/* ================= CLEAN URL ================= */

$uri = parse_url($uri, PHP_URL_PATH);

$basePath = '/fil-rouge-infra-si/backend/public';

if (str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}

if (str_starts_with($uri, '/index.php')) {
    $uri = substr($uri, strlen('/index.php'));
}

if ($uri === '') {
    $uri = '/';
}

/* ================= CONTROLLERS ================= */

$authController = new AuthController();
$userController = new UserController();
$ticketController = new TicketController();
$clientTicketController = new ClientTicketController();

/* ================= AUTH ================= */

// LOGIN
if ($uri === '/api/login' && $method === 'POST') {
    $authController->login();
    exit;
}

// LOGOUT
if ($uri === '/api/logout' && $method === 'POST') {
    AuthMiddleware::handle();
    $authController->logout();
    exit;
}

// ME
if ($uri === '/api/me' && $method === 'GET') {
    AuthMiddleware::handle();
    $authController->me();
    exit;
}

// CHANGE PASSWORD
if ($uri === '/api/change-password' && $method === 'POST') {
    AuthMiddleware::handle();
    $authController->changePassword();
    exit;
}

/* ================= ADMIN USERS ================= */

// GET USERS
if ($uri === '/api/admin/users' && $method === 'GET') {

    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');

    $userController->index();
    exit;
}

// CREATE USER
if ($uri === '/api/admin/users/create' && $method === 'POST') {

    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');

    $userController->create();
    exit;
}

// DELETE USER
if (
    preg_match('#^/api/admin/users/(\d+)$#', $uri, $matches)
    && $method === 'DELETE'
) {

    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');

    $userController->delete((int)$matches[1]);
    exit;
}

/* ================= CLIENT TICKETS ================= */

// GET MY TICKETS
if ($uri === '/api/client/tickets' && $method === 'GET') {

    AuthMiddleware::handle();
    RoleMiddleware::handle('user');

    $clientTicketController->myTickets();
    exit;
}

// CREATE TICKET
if ($uri === '/api/client/tickets' && $method === 'POST') {

    AuthMiddleware::handle();
    RoleMiddleware::handle('user');

    $clientTicketController->create();
    exit;
}

/* ================= ADMIN TICKETS ================= */

// GET ALL TICKETS
if ($uri === '/api/admin/tickets' && $method === 'GET') {

    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');

    $ticketController->index();
    exit;
}

// GET ONE TICKET
if (
    preg_match('#^/api/admin/tickets/(\d+)$#', $uri, $matches)
    && $method === 'GET'
) {

    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');

    $ticketController->show((int)$matches[1]);
    exit;
}

// UPDATE STATUS
if (
    preg_match('#^/api/admin/tickets/(\d+)/status$#', $uri, $matches)
    && $method === 'PUT'
) {

    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');

    $ticketController->updateStatus((int)$matches[1]);
    exit;
}

// DELETE TICKET
if (
    preg_match('#^/api/admin/tickets/(\d+)$#', $uri, $matches)
    && $method === 'DELETE'
) {

    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');

    $ticketController->delete((int)$matches[1]);
    exit;
}

/* ================= 404 ================= */

http_response_code(404);

echo json_encode([
    "success" => false,
    "message" => "Route non trouvée",
    "uri" => $uri,
    "method" => $method
]);

exit;