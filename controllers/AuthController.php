<?php

require_once __DIR__ . '/../services/AuthService.php';

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function showLogin()
    {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function login()
    {
        session_start();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            echo "Tous les champs sont obligatoires.";
            return;
        }

        $user = $this->authService->login($email, $password);

        if (!$user) {
            echo "Email ou mot de passe incorrect.";
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role_name'];

        if ($user['role_name'] === 'admin') {
            header("Location: index.php?action=dashboard");
            exit;
        }

        header("Location: index.php?action=client_dashboard");
        exit;
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        header("Location: index.php?action=login");
        exit;
    }
    public function register()
{
    require_once __DIR__ . '/../config/database.php';

    $db = new Database();
    $pdo = $db->getConnection();

    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $passwordPlain = $_POST['password'] ?? '';

    if ($full_name === '' || $email === '' || $passwordPlain === '') {
        echo "Tous les champs sont obligatoires.";
        return;
    }

    $checkQuery = "SELECT id FROM users WHERE email = :email LIMIT 1";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute(['email' => $email]);

    if ($checkStmt->fetch()) {
        echo "Cet email existe déjà. Choisis-en un autre.";
        return;
    }

    $password = password_hash($passwordPlain, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (email, password_hash, full_name, is_active, created_at)
              VALUES (:email, :password, :full_name, 1, NOW())";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'email' => $email,
        'password' => $password,
        'full_name' => $full_name
    ]);

    $userId = $pdo->lastInsertId();

    $roleQuery = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, 2)";
    $roleStmt = $pdo->prepare($roleQuery);
    $roleStmt->execute([
        'user_id' => $userId
    ]);

    header("Location: index.php?action=login");
    exit;
}
   
}