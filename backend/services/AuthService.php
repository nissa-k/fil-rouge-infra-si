<?php

require_once __DIR__ . '/../models/User.php';

class AuthService
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login(string $email, string $password): array|false
    {
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return false;
        }

        // Vérifie si l'utilisateur est actif
        if ((int)$user['is_active'] !== 1) {
            return false;
        }

        // Vérifie le mot de passe
        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        return $user;
    }
}