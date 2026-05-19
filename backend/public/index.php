<?php

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/TicketController.php';
require_once __DIR__ . '/../controllers/TechnicienController.php';
require_once __DIR__ . '/../controllers/AssetController.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

header("Content-Type: application/json; charset=UTF-8");

/*controllers*/

$authController = new AuthController();
$userController = new UserController();
$ticketController = new TicketController();
$technicienController = new TechnicienController();
$assetController = new AssetController();


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$method = $_SERVER['REQUEST_METHOD'];

$uri = str_replace(
    '/fil-rouge-infra-si/backend/public/index.php',
    '',
    $uri
);

/*auth*/

if ($uri === '/api/login' && $method === 'POST') {

    $authController->login();

    exit;
}

elseif ($uri === '/api/verify-2fa' && $method === 'POST') {

    $authController->verify2FA();

    exit;
}

elseif ($uri === '/api/logout' && $method === 'POST') {

    $authController->logout();

    exit;
}

elseif ($uri === '/api/me' && $method === 'GET') {

    $authController->me();

    exit;
}

elseif ($uri === '/api/change-password' && $method === 'POST') {

    $authController->changePassword();

    exit;
}

elseif ($uri === '/api/forgot-password' && $method === 'POST') {

    $authController->forgotPassword();

    exit;
}

elseif ($uri === '/api/reset-password' && $method === 'POST') {

    $authController->resetPassword();

    exit;
}

/*client*/

elseif ($uri === '/api/client/tickets' && $method === 'POST') {

    $ticketController->create();

    exit;
}

elseif ($uri === '/api/client/tickets' && $method === 'GET') {

    $ticketController->myTickets();

    exit;
}

/* admin users */

elseif ($uri === '/api/admin/users' && $method === 'GET') {

    $userController->index();

    exit;
}

elseif ($uri === '/api/admin/users/create' && $method === 'POST') {

    $userController->create();

    exit;
}

elseif (
    preg_match('#^/api/admin/users/(\\d+)$#', $uri, $matches)
    && $method === 'DELETE'
) {

    $userController->delete((int)$matches[1]);

    exit;
}

/* tickets admin */

elseif ($uri === '/api/admin/tickets' && $method === 'GET') {

    $ticketController->index();

    exit;
}

elseif (
    preg_match('#^/api/admin/tickets/(\\d+)/status$#', $uri, $matches)
    && $method === 'PUT'
) {

    $ticketController->updateStatus((int)$matches[1]);

    exit;
}

elseif (
    preg_match('#^/api/admin/tickets/(\\d+)$#', $uri, $matches)
    && $method === 'PUT'
) {

    $ticketController->update((int)$matches[1]);

    exit;
}

elseif (
    preg_match('#^/api/admin/tickets/(\\d+)$#', $uri, $matches)
    && $method === 'DELETE'
) {

    $ticketController->delete((int)$matches[1]);

    exit;
}

/*technicien*/

elseif ($uri === '/api/technicien/tickets/en-cours' && $method === 'GET') {

    $technicienController->getEnCours();

    exit;
}

elseif ($uri === '/api/technicien/tickets/traitees' && $method === 'GET') {

    $technicienController->getTraitees();

    exit;
}

elseif ($uri === '/api/technicien/tickets/refusees' && $method === 'GET') {

    $technicienController->getRefusees();

    exit;
}

elseif (
    preg_match('#^/api/technicien/tickets/(\\d+)/traiter$#', $uri, $matches)
    && $method === 'PUT'
) {

    $technicienController->traiter((int)$matches[1]);

    exit;
}

elseif (
    preg_match('#^/api/technicien/tickets/(\\d+)/refuser$#', $uri, $matches)
    && $method === 'PUT'
) {

    $technicienController->refuser((int)$matches[1]);

    exit;
}

/*parc informatique*/

elseif ($uri === '/api/assets' && $method === 'GET') {

    $assetController->index();

    exit;
}

elseif ($uri === '/api/assets' && $method === 'POST') {

    $assetController->store();

    exit;
}

elseif (
    preg_match('#^/api/assets/(\\d+)$#', $uri, $matches)
    && $method === 'GET'
) {

    $assetController->show((int)$matches[1]);

    exit;
}

elseif (
    preg_match('#^/api/assets/(\\d+)$#', $uri, $matches)
    && $method === 'PUT'
) {

    $assetController->update((int)$matches[1]);

    exit;
}

elseif (
    preg_match('#^/api/assets/(\\d+)$#', $uri, $matches)
    && $method === 'DELETE'
) {

    $assetController->delete((int)$matches[1]);

    exit;
}

/*404*/

http_response_code(404);

echo json_encode([

    "success" => false,

    "message" => "Route non trouvée",

    "uri" => $uri,

    "method" => $method
]);