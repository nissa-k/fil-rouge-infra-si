<?php

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('log_errors', '1');

set_exception_handler(function ($e) {
    require_once __DIR__ . '/helpers/flash.php';
    setFlash('error', 'Une erreur est survenue. Merci de réessayer.');
    header('Location: index.php?action=login');
    exit;
});

register_shutdown_function(function () {
    $error = error_get_last();

    if ($error !== null) {
        require_once __DIR__ . '/helpers/flash.php';
        setFlash('error', 'Une erreur technique est survenue.');
        header('Location: index.php?action=login');
        exit;
    }
});

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/helpers/flash.php';
require_once __DIR__ . '/helpers/audit.php';

$action = $_GET['action'] ?? 'login';

$authController = new AuthController();
switch ($action) {
    case 'login':
        $authController->showLogin();
        break;

    case 'do_login':
        $authController->login();
        break;

    case 'register':
        require_once __DIR__ . '/views/auth/register.php';
        break;

    case 'do_register':
        $authController->register();
        break;

    case 'logout':
        $authController->logout();
        break;

    case 'dashboard':
        require_once __DIR__ . '/views/admin/dashboard.php';
        break;

    case 'client_dashboard':
        require_once __DIR__ . '/views/client/dashboard.php';
        break;

    case 'tickets_en_cours':
        require_once __DIR__ . '/views/admin/tickets_en_cours.php';
        break;

    case 'tickets_traites':
        require_once __DIR__ . '/views/admin/tickets_traites.php';
        break;

    case 'tickets_refuses':
        require_once __DIR__ . '/views/admin/tickets_refuses.php';
        break;

    case 'users':
        require_once __DIR__ . '/views/admin/users.php';
        break;

    case 'traiter':
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        if ($_SESSION['role'] !== 'admin') {
        header("Location: index.php?action=client_dashboard");
        exit;
        }

        require_once __DIR__ . '/config/database.php';

        $db = new Database();
        $pdo = $db->getConnection();

        $id = $_GET['id'] ?? null;

    if ($id) {
        $query = "UPDATE tickets SET status = 'traitee', updated_at = NOW() WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $id]);

        logAction($_SESSION['user_id'], 'update_status', 'ticket', $id);
    }

    header("Location: index.php?action=tickets_en_cours");
    exit;
    case 'refuser':
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        if ($_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=client_dashboard");
            exit;
        }

        require_once __DIR__ . '/config/database.php';

        $db = new Database();
        $pdo = $db->getConnection();

        $id = $_GET['id'] ?? null;

        if ($id) {
            $query = "UPDATE tickets SET status = 'refusee', updated_at = NOW() WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $id]);

            logAction($_SESSION['user_id'], 'update_status', 'ticket', $id);
        }

        header("Location: index.php?action=tickets_en_cours");
        exit;

    case 'delete_ticket':
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        if ($_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=client_dashboard");
            exit;
        }

        require_once __DIR__ . '/config/database.php';

        $db = new Database();
        $pdo = $db->getConnection();

        $id = $_GET['id'] ?? null;

        if ($id) {
            $query = "DELETE FROM tickets WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $id]);
        }
        logAction($_SESSION['user_id'], 'delete', 'ticket', $id);

        header("Location: index.php?action=tickets_en_cours");
        exit;

    case 'delete_user':
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        if ($_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=client_dashboard");
            exit;
        }

        require_once __DIR__ . '/config/database.php';

        $db = new Database();
        $pdo = $db->getConnection();

        $id = $_GET['id'] ?? null;

        if ($id && $id != $_SESSION['user_id']) {
            $deleteRoles = "DELETE FROM user_roles WHERE user_id = :id";
            $stmtRoles = $pdo->prepare($deleteRoles);
            $stmtRoles->execute(['id' => $id]);

            $deleteUser = "DELETE FROM users WHERE id = :id";
            $stmtUser = $pdo->prepare($deleteUser);
            $stmtUser->execute(['id' => $id]);

            logAction($_SESSION['user_id'], 'delete', 'user', $id);
        }

        header("Location: index.php?action=users");
        exit;

    case 'create_ticket':
        require_once __DIR__ . '/views/client/create_ticket.php';
        break;

    case 'store_ticket':
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        if ($_SESSION['role'] !== 'user') {
            header("Location: index.php?action=dashboard");
            exit;
        }

        require_once __DIR__ . '/config/database.php';

        $db = new Database();
        $pdo = $db->getConnection();

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $priority = $_POST['priority'] ?? '';

        $allowedPriorities = ['low', 'medium', 'high'];

        if ($title === '' || $description === '' || $priority === '') {
            setFlash('error', 'Tous les champs sont obligatoires.');
            header("Location: index.php?action=create_ticket");
            exit;
        }

        if (mb_strlen($title) > 150) {
            setFlash('error', 'Le titre ne doit pas dépasser 150 caractères.');
            header("Location: index.php?action=create_ticket");
            exit;
        }

        if (mb_strlen($description) > 1000) {
            setFlash('error', 'La description ne doit pas dépasser 1000 caractères.');
            header("Location: index.php?action=create_ticket");
            exit;
        }

        if (!in_array($priority, $allowedPriorities, true)) {
            setFlash('error', 'Priorité invalide.');
            header("Location: index.php?action=create_ticket");
            exit;
        }

        $query = "INSERT INTO tickets (user_id, title, description, priority, status, created_at)
                  VALUES (:user_id, :title, :description, :priority, 'en_cours', NOW())";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'title' => $title,
            'description' => $description,
            'priority' => $priority
        ]);
        require_once __DIR__ . '/helpers/audit.php';

        $ticketId = $pdo->lastInsertId();

        logAction($_SESSION['user_id'], 'create', 'ticket', $ticketId);

        setFlash('success', 'Requête créée avec succès.');
        header("Location: index.php?action=my_tickets");
        exit;

    case 'my_tickets':
        require_once __DIR__ . '/views/client/my_tickets.php';
        break;

    case 'edit_ticket':
        require_once __DIR__ . '/views/admin/edit_ticket.php';
        break;

    case 'update_ticket':
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        if ($_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=client_dashboard");
            exit;
        }

        require_once __DIR__ . '/config/database.php';

        $db = new Database();
        $pdo = $db->getConnection();

        $id = $_POST['id'] ?? null;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $priority = $_POST['priority'] ?? '';
        $status = $_POST['status'] ?? '';

        $allowedPriorities = ['low', 'medium', 'high'];
        $allowedStatuses = ['en_cours', 'traitee', 'refusee'];

        if (!$id || $title === '' || $description === '' || $priority === '' || $status === '') {
            setFlash('error', 'Tous les champs sont obligatoires.');
            header("Location: index.php?action=edit_ticket&id=" . urlencode((string)$id));
            exit;
        }

        if (mb_strlen($title) > 150) {
            setFlash('error', 'Le titre ne doit pas dépasser 150 caractères.');
            header("Location: index.php?action=edit_ticket&id=" . urlencode((string)$id));
            exit;
        }

        if (mb_strlen($description) > 1000) {
            setFlash('error', 'La description ne doit pas dépasser 1000 caractères.');
            header("Location: index.php?action=edit_ticket&id=" . urlencode((string)$id));
            exit;
        }

        if (!in_array($priority, $allowedPriorities, true)) {
            setFlash('error', 'Priorité invalide.');
            header("Location: index.php?action=edit_ticket&id=" . urlencode((string)$id));
            exit;
        }

        if (!in_array($status, $allowedStatuses, true)) {
            setFlash('error', 'Statut invalide.');
            header("Location: index.php?action=edit_ticket&id=" . urlencode((string)$id));
            exit;
        }

        $query = "UPDATE tickets
                  SET title = :title,
                      description = :description,
                      priority = :priority,
                      status = :status,
                      updated_at = NOW()
                  WHERE id = :id";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'status' => $status,
            'id' => $id
        ]);

        setFlash('success', 'Requête mise à jour avec succès.');
        header("Location: index.php?action=tickets_en_cours");
        exit;

    default:
        setFlash('error', 'Page introuvable.');
        header("Location: index.php?action=login");
        exit;
}