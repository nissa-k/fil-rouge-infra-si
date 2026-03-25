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

    case 'traiter':
        require_once __DIR__ . '/config/database.php';

        $db = new Database();
        $pdo = $db->getConnection();

        $id = $_GET['id'] ?? null;

        if ($id) {
            $query = "UPDATE tickets SET status = 'traitee', updated_at = NOW() WHERE id = :id";
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
            $query = "UPDATE tickets SET status = 'refusee', updated_at = NOW() WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $id]);
        }

        header("Location: index.php?action=tickets_en_cours");
        exit;

    case 'create_ticket':
        require_once __DIR__ . '/views/client/create_ticket.php';
        break;

    case 'store_ticket':
        session_start();
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $db = new Database();
        $pdo = $db->getConnection();

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $priority = $_POST['priority'] ?? 'medium';

        if ($title === '' || $description === '') {
            echo "Titre ou description vide";
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

        header("Location: index.php?action=my_tickets");
        exit;

    case 'my_tickets':
        require_once __DIR__ . '/views/client/my_tickets.php';
        break;

    case 'edit_ticket':
        require_once __DIR__ . '/views/admin/edit_ticket.php';
        break;

    case 'update_ticket':
        require_once __DIR__ . '/config/database.php';

        $db = new Database();
        $pdo = $db->getConnection();

        $id = $_POST['id'] ?? null;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $priority = $_POST['priority'] ?? 'medium';
        $status = $_POST['status'] ?? 'en_cours';

        if ($id) {
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
        }

        header("Location: index.php?action=tickets_en_cours");
        exit;

    case 'logout':
        $authController->logout();
        break;

    default:
        echo "Page introuvable";
        break;
    case 'register':
        require_once __DIR__ . '/views/auth/register.php';
        break;

    case 'do_register':
        require_once __DIR__ . '/controllers/AuthController.php';
        $authController->register();
        break;
}