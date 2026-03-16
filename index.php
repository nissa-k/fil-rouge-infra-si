<?php

require_once __DIR__ . '/controllers/AuthController.php';

$action = $_GET['action'] ?? 'login';

$authController = new AuthController();

switch ($action) {
    case 'login':
        $authController->showLogin();
        break;

    case 'do_login':
        $authController->login();
        break;

    case 'dashboard':
        require_once __DIR__ . '/views/admin/dashboard.php';
        break;

    case 'tickets_en_cours':
        require_once __DIR__ . '/views/admin/tickets_en_cours.php';
        break;

    case 'traiter':
        require_once __DIR__ . '/config/database.php';

        $db = new Database();
        $pdo = $db->getConnection();

        $id = $_GET['id'] ?? null;

        if ($id) {
            $query = "UPDATE tickets SET status = 'traitee' WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $id]);
        }

        header("Location: index.php?action=tickets_en_cours");
        exit;

    case 'refuser':
        require_once __DIR__ . '/config/database.php';

        $db = new Database();
        $pdo = $db->getConnection();

        $id = $_GET['id'] ?? null;

        if ($id) {
            $query = "UPDATE tickets SET status = 'refusee' WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $id]);
        }

        header("Location: index.php?action=tickets_en_cours");
        exit;

    case 'logout':
        $authController->logout();
        break;

    default:
        echo "Page introuvable";
        break;
    case 'tickets_traites':
        require_once __DIR__ . '/views/admin/tickets_traites.php';
        break;

    case 'tickets_refuses':
        require_once __DIR__ . '/views/admin/tickets_refuses.php';
         break;

    case 'create_ticket':
        require_once __DIR__ . '/views/client/create_ticket.php';
        break;
    case 'store_ticket':

    session_start();

    require_once __DIR__ . '/config/database.php';

    $db = new Database();
    $pdo = $db->getConnection();

    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];

    $query = "INSERT INTO tickets (user_id, title, description, priority, status, created_at)
              VALUES (:user_id, :title, :description, :priority, 'en_cours', NOW())";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'title' => $title,
        'description' => $description,
        'priority' => $priority
    ]);

    header("Location: index.php?action=my_tickets");
    exit;
    
    case 'client_dashboard':
        require_once __DIR__ . '/views/client/dashboard.php';
        break;
}