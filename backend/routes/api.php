<?php

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/TicketController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/ClientTicketController.php';
require_once __DIR__ . '/../controllers/AssetController.php';
require_once __DIR__ . '/../controllers/MessageController.php';
require_once __DIR__ . '/../controllers/AdminUserController.php';
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

/* controllers */

$authController = new AuthController();

$ticketController =
    new TicketController();

$userController =
    new UserController();

$clientTicketController =
    new ClientTicketController();

$assetController =
    new AssetController();

$messageController =
    new MessageController();

$adminUserController =
    new AdminUserController();

/* auth */

if ($uri === '/api/login' && $method === 'POST') {

    $authController->login();

    exit;
}

if ($uri === '/api/verify-2fa' && $method === 'POST') {

    $authController->verify2FA();

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

if ($uri === '/api/forgot-password' && $method === 'POST') {

    $authController->forgotPassword();

    exit;
}

if ($uri === '/api/reset-password' && $method === 'POST') {

    $authController->resetPassword();

    exit;
}

/* stats admin */

if ($uri === '/api/admin/stats' && $method === 'GET') {

    AuthMiddleware::handle();

    RoleMiddleware::handle('admin');

    $adminUserController->getStats();

    exit;
}

/* tickets client */

if ($uri === '/api/client/tickets' && $method === 'GET') {

    AuthMiddleware::handle();

    RoleMiddleware::handle('client');

    $clientTicketController->myTickets();

    exit;
}

if ($uri === '/api/client/tickets' && $method === 'POST') {

    AuthMiddleware::handle();

    RoleMiddleware::handle('client');

    $clientTicketController->create();

    exit;
}

/* tickets admin */

if ($uri === '/api/admin/tickets' && $method === 'GET') {

    AuthMiddleware::handle();

    RoleMiddleware::handle('admin');

    $ticketController->index();

    exit;
}

/* modifier statut ticket */

if (
    preg_match(
        '#^/api/admin/tickets/(\d+)/status$#',
        $uri,
        $m
    )
    && $method === 'PUT'
) {

    AuthMiddleware::handle();

    $ticketController->updateStatus(
        (int)$m[1]
    );

    exit;
}

/* modifier ticket */

if (
    preg_match(
        '#^/api/admin/tickets/(\d+)$#',
        $uri,
        $m
    )
    && $method === 'PUT'
) {

    AuthMiddleware::handle();

    RoleMiddleware::handle('admin');

    $ticketController->update(
        (int)$m[1]
    );

    exit;
}

/* supprimer ticket */

if (
    preg_match(
        '#^/api/admin/tickets/(\d+)$#',
        $uri,
        $m
    )
    && $method === 'DELETE'
) {

    AuthMiddleware::handle();

    RoleMiddleware::handle('admin');

    $ticketController->delete(
        (int)$m[1]
    );

    exit;
}

/* messages tickets */

if (
    preg_match(
        '#^/api/tickets/(\d+)/messages$#',
        $uri,
        $m
    )
    && $method === 'GET'
) {

    AuthMiddleware::handle();

    $messageController->index(
        (int)$m[1]
    );

    exit;
}

if (
    preg_match(
        '#^/api/tickets/(\d+)/messages$#',
        $uri,
        $m
    )
    && $method === 'POST'
) {

    AuthMiddleware::handle();

    $messageController->create(
        (int)$m[1]
    );

    exit;
}

/* users admin */

if ($uri === '/api/admin/users' && $method === 'GET') {

    AuthMiddleware::handle();

    RoleMiddleware::handle('admin');

    $userController->index();

    exit;
}

if ($uri === '/api/admin/users' && $method === 'POST') {

    AuthMiddleware::handle();

    RoleMiddleware::handle('admin');

    $authController->createUser();

    exit;
}

if (
    preg_match(
        '#^/api/admin/users/(\d+)$#',
        $uri,
        $m
    )
    && $method === 'DELETE'
) {

    AuthMiddleware::handle();

    RoleMiddleware::handle('admin');

    $userController->delete(
        (int)$m[1]
    );

    exit;
}

/* assets */

if ($uri === '/api/assets' && $method === 'GET') {

    AuthMiddleware::handle();

    RoleMiddleware::handle('admin');

    $assetController->index();

    exit;
}

if ($uri === '/api/assets' && $method === 'POST') {

    AuthMiddleware::handle();

    RoleMiddleware::handle('admin');

    $assetController->store();

    exit;
}

if (
    preg_match(
        '#^/api/assets/(\d+)$#',
        $uri,
        $m
    )
    && $method === 'GET'
) {

    AuthMiddleware::handle();

    RoleMiddleware::handle('admin');

    $assetController->show(
        (int)$m[1]
    );

    exit;
}

if (
    preg_match(
        '#^/api/assets/(\d+)$#',
        $uri,
        $m
    )
    && $method === 'PUT'
) {

    AuthMiddleware::handle();

    RoleMiddleware::handle('admin');

    $assetController->update(
        (int)$m[1]
    );

    exit;
}

if (
    preg_match(
        '#^/api/assets/(\d+)$#',
        $uri,
        $m
    )
    && $method === 'DELETE'
) {

    AuthMiddleware::handle();

    RoleMiddleware::handle('admin');

    $assetController->delete(
        (int)$m[1]
    );

    exit;
}

/* 404 */

http_response_code(404);

echo json_encode([
    'success' => false,
    'message' => 'Route non trouvée'
]);
