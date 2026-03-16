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
}