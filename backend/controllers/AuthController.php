<?php

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/audit.php';

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function getJsonInput(): array
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        return is_array($data) ? $data : [];
    }

    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $data = $this->getJsonInput();

        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Tous les champs sont obligatoires.'
            ], 400);
        }

        $user = $this->authService->login($email, $password);

        if (!$user) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Email ou mot de passe incorrect.'
            ], 401);
        }

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role_name']
        ];

        logAction((int) $user['id'], 'login', 'user', (int) $user['id']);

        $this->jsonResponse([
            'success' => true,
            'message' => 'Connexion réussie.',
            'user' => $_SESSION['user']
        ]);
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user']['id'] ?? null;

        if ($userId) {
            logAction((int) $userId, 'logout', 'user', (int) $userId);
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        $this->jsonResponse([
            'success' => true,
            'message' => 'Déconnexion réussie.'
        ]);
    }

    public function register(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $data = $this->getJsonInput();

        $full_name = trim($data['full_name'] ?? '');
        $email = trim($data['email'] ?? '');
        $passwordPlain = $data['password'] ?? '';

        if ($full_name === '' || $email === '' || $passwordPlain === '') {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Tous les champs sont obligatoires.'
            ], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Email invalide.'
            ], 400);
        }

        if (mb_strlen($full_name) > 100) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Le nom complet ne doit pas dépasser 100 caractères.'
            ], 400);
        }

        if (mb_strlen($passwordPlain) < 6) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Le mot de passe doit contenir au moins 6 caractères.'
            ], 400);
        }

        $db = new Database();
        $pdo = $db->getConnection();

        $checkQuery = 'SELECT id FROM users WHERE email = :email LIMIT 1';
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute([
            'email' => $email
        ]);

        if ($checkStmt->fetch()) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Cet email existe déjà.'
            ], 409);
        }

        $passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);

        $query = 'INSERT INTO users (email, password_hash, full_name, is_active, created_at)
                  VALUES (:email, :password_hash, :full_name, 1, NOW())';

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'email' => $email,
            'password_hash' => $passwordHash,
            'full_name' => $full_name
        ]);

        $userId = (int) $pdo->lastInsertId();

        $roleQuery = 'INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)';
        $roleStmt = $pdo->prepare($roleQuery);
        $roleStmt->execute([
            'user_id' => $userId,
            'role_id' => 2
        ]);

        logAction($userId, 'register', 'user', $userId);

        $this->jsonResponse([
            'success' => true,
            'message' => 'Compte créé avec succès. Vous pouvez vous connecter.'
        ], 201);
    }

    public function me(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Non authentifié.'
            ], 401);
        }

        $this->jsonResponse([
            'success' => true,
            'user' => $_SESSION['user']
        ]);
    }
}