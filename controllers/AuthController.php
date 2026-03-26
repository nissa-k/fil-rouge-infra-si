<?php

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/flash.php';

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

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            setFlash('error', 'Tous les champs sont obligatoires.');
            header("Location: index.php?action=login");
            exit;
        }

        $user = $this->authService->login($email, $password);

        if (!$user) {
            setFlash('error', 'Email ou mot de passe incorrect.');
            header("Location: index.php?action=login");
            exit;
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
        $db = new Database();
        $pdo = $db->getConnection();

        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $passwordPlain = $_POST['password'] ?? '';

        if ($full_name === '' || $email === '' || $passwordPlain === '') {
            setFlash('error', 'Tous les champs sont obligatoires.');
            header("Location: index.php?action=register");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Email invalide.');
            header("Location: index.php?action=register");
            exit;
        }

        if (mb_strlen($full_name) > 100) {
            setFlash('error', 'Le nom complet ne doit pas dépasser 100 caractères.');
            header("Location: index.php?action=register");
            exit;
        }

        if (mb_strlen($passwordPlain) < 6) {
            setFlash('error', 'Le mot de passe doit contenir au moins 6 caractères.');
            header("Location: index.php?action=register");
            exit;
        }

        $checkQuery = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute(['email' => $email]);

        if ($checkStmt->fetch()) {
            setFlash('error', 'Cet email existe déjà.');
            header("Location: index.php?action=register");
            exit;
        }

        $passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (email, password_hash, full_name, is_active, created_at)
                  VALUES (:email, :password_hash, :full_name, 1, NOW())";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'email' => $email,
            'password_hash' => $passwordHash,
            'full_name' => $full_name
        ]);

        $userId = $pdo->lastInsertId();

        $roleQuery = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, 2)";
        $roleStmt = $pdo->prepare($roleQuery);
        $roleStmt->execute([
            'user_id' => $userId
        ]);

        setFlash('success', 'Compte créé avec succès. Vous pouvez vous connecter.');
        header("Location: index.php?action=login");
        exit;
    }
}