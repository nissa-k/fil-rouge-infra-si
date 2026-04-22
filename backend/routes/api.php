<?php

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/TicketController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/ClientTicketController.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../middlewares/RoleMiddleware.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

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

$authController = new AuthController();
$ticketController = new TicketController();
$userController = new UserController();
$clientTicketController = new ClientTicketController();

/* AUTH */

if ($uri === '/api/login' && $method === 'POST') {
    $authController->login();
}

if ($uri === '/api/register' && $method === 'POST') {
    $authController->register();
}

if ($uri === '/api/logout' && $method === 'POST') {
    AuthMiddleware::handle();
    $authController->logout();
}

if ($uri === '/api/me' && $method === 'GET') {
    AuthMiddleware::handle();
    $authController->me();
}

/* CLIENT */

if ($uri === '/api/client/tickets' && $method === 'GET') {
    AuthMiddleware::handle();
    RoleMiddleware::handle('user');
    $clientTicketController->myTickets();
}

if ($uri === '/api/client/tickets' && $method === 'POST') {
    AuthMiddleware::handle();
    RoleMiddleware::handle('user');
    $clientTicketController->create();
}

/* ADMIN TICKETS */

if ($uri === '/api/admin/tickets' && $method === 'GET') {
    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');
    $ticketController->index();
}

if (preg_match('#^/api/admin/tickets/(\d+)$#', $uri, $matches) && $method === 'GET') {
    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');
    $ticketController->show((int) $matches[1]);
}

if (preg_match('#^/api/admin/tickets/(\d+)$#', $uri, $matches) && $method === 'PUT') {
    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');
    $ticketController->update((int) $matches[1]);
}

if (preg_match('#^/api/admin/tickets/(\d+)/status$#', $uri, $matches) && $method === 'PUT') {
    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');
    $ticketController->updateStatus((int) $matches[1]);
}

if (preg_match('#^/api/admin/tickets/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');
    $ticketController->delete((int) $matches[1]);
}

/* ADMIN USERS */

if ($uri === '/api/admin/users' && $method === 'GET') {
    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');
    $userController->index();
}

if (preg_match('#^/api/admin/users/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');
    $userController->delete((int) $matches[1]);
}

http_response_code(404);
echo json_encode([
    'success' => false,
    'message' => 'Route non trouvée.',
    'uri' => $uri,
    'method' => $method
], JSON_UNESCAPED_UNICODE);
exit;