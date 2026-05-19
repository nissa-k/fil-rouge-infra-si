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

// Initialisation des contrôleurs
$authController = new AuthController();
$ticketController = new TicketController();
$userController = new UserController();
$clientTicketController = new ClientTicketController();

/*auth */

if ($uri === '/api/login' && $method === 'POST') {
    $authController->login();
    exit;
}

if ($uri === '/api/logout' && $method === 'POST') {
    AuthMiddleware::handle();
    $authController->logout();
    exit;
}

if ($uri === '/api/me' && $method === 'GET') {
    AuthMiddleware::handle();
    $authController->me();
    exit;
}

if ($uri === '/api/change-password' && $method === 'POST') {
    AuthMiddleware::handle();
    $authController->changePassword();
    exit;
}

/*client*/

if ($uri === '/api/client/tickets' && $method === 'GET') {
    AuthMiddleware::handle();
    RoleMiddleware::handle('user');
    $clientTicketController->myTickets();
    exit;
}

if ($uri === '/api/client/tickets' && $method === 'POST') {
    AuthMiddleware::handle();
    RoleMiddleware::handle('user');
    $clientTicketController->create();
    exit;
}

/*ticket admin*/

if ($uri === '/api/admin/tickets' && $method === 'GET') {
    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');
    $ticketController->index();
    exit;
}

if (preg_match('#^/api/admin/tickets/(\d+)$#', $uri, $m) && $method === 'DELETE') {
    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');
    $ticketController->delete((int)$m[1]);
    exit;
}

/*user admin*/

if ($uri === '/api/admin/users' && $method === 'GET') {
    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');
    $userController->index();
    exit;
}

if (preg_match('#^/api/admin/users/(\d+)$#', $uri, $m) && $method === 'DELETE') {
    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');
    $userController->delete((int)$m[1]);
    exit;
}

/*creer user*/

if ($uri === '/api/admin/create-user' && $method === 'POST') {
    AuthMiddleware::handle();
    RoleMiddleware::handle('admin');
    $authController->createUser();
    exit;
}

/*erreur 404*/

http_response_code(404);
echo json_encode([
    'success' => false,
    'message' => 'Route non trouvée'
]);