<?php

require_once __DIR__ . '/../config/database.php';

class AdminUserController
{
    public function createUser()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $firstName = $data['first_name'] ?? '';
        $lastName  = $data['last_name']  ?? '';
        $email     = $data['email']      ?? '';
        $role      = $data['role']       ?? 'client';

        if (!$firstName || !$lastName || !$email) {
            echo json_encode([
                "success" => false,
                "message" => "Champs requis manquants"
            ]);
            return;
        }

        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            echo json_encode([
                "success" => false,
                "message" => "Email déjà utilisé"
            ]);
            return;
        }

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

    public function getStats()
    {
        $pdo = Database::getConnection();

        $total = $pdo
            ->query("SELECT COUNT(*) FROM tickets")
            ->fetchColumn();

        $en_cours = $pdo
            ->query("SELECT COUNT(*) FROM tickets WHERE status = 'en_cours'")
            ->fetchColumn();

        $traitee = $pdo
            ->query("SELECT COUNT(*) FROM tickets WHERE status = 'traitee'")
            ->fetchColumn();

        $refusee = $pdo
            ->query("SELECT COUNT(*) FROM tickets WHERE status = 'refusee'")
            ->fetchColumn();

        $users = $pdo
            ->query("SELECT COUNT(*) FROM users")
            ->fetchColumn();

        $assets = $pdo
            ->query("SELECT COUNT(*) FROM assets")
            ->fetchColumn();

        echo json_encode([
            "success" => true,
            "stats"   => [
                "total_tickets" => (int) $total,
                "en_cours"      => (int) $en_cours,
                "traitee"       => (int) $traitee,
                "refusee"       => (int) $refusee,
                "users"         => (int) $users,
                "assets"        => (int) $assets,
            ]
        ]);
    }
}
