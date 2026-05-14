<?php

require_once __DIR__ . '/../config/database.php';

class AdminUserController
{
    public function createUser()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $firstName = $data['first_name'] ?? '';
        $lastName = $data['last_name'] ?? '';
        $email = $data['email'] ?? '';
        $role = $data['role'] ?? 'user';

        if (!$firstName || !$lastName || !$email) {
            echo json_encode([
                "success" => false,
                "message" => "Champs requis manquants"
            ]);
            return;
        }

        $pdo = Database::connect();

        // Vérifier email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            echo json_encode([
                "success" => false,
                "message" => "Email déjà utilisé"
            ]);
            return;
        }

        // mot de passe par défaut
        $defaultPassword = password_hash("123456", PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users (first_name, last_name, email, password_hash, role, must_change_password)
            VALUES (?, ?, ?, ?, ?, 1)
        ");

        $stmt->execute([
            $firstName,
            $lastName,
            $email,
            $defaultPassword,
            $role
        ]);

        echo json_encode([
            "success" => true
        ]);
    }
}