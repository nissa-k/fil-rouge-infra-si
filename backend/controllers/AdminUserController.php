<?php

require_once __DIR__ . '/../config/database.php';

class AdminUserController
{
    public function createUser()
    {
        header("Content-Type: application/json; charset=UTF-8");

        try {
            $data = json_decode(file_get_contents("php://input"), true);

            $fullName = $data['full_name'] ?? '';
            $email    = $data['email']     ?? '';
            $role     = $data['role']      ?? 'client';

            if (!$fullName || !$email) {
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
                INSERT INTO users (full_name, email, password_hash, role, must_change_password)
                VALUES (?, ?, ?, ?, 1)
            ");

            $stmt->execute([
                $fullName,
                $email,
                $defaultPassword,
                $role
            ]);

            echo json_encode([
                "success" => true,
                "message" => "Utilisateur créé avec succès"
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Erreur serveur : " . $e->getMessage()
            ]);
        }
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
