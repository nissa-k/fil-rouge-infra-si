<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/database.php';

class UserController
{
    // rôles autorisés à la création
    private const ROLES_AUTORISES = ['client', 'technicien', 'admin'];

    // liste des utilisateurs
    public function index(): void
    {
        try {

            $db = Database::getConnection();

            $stmt = $db->query("
                SELECT id, full_name, email, role
                FROM users
                ORDER BY id DESC
            ");

            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "users"   => $users
            ]);

        } catch (Exception $e) {

            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Erreur serveur",
                "error"   => $e->getMessage()
            ]);
        }
    }

    // créer un utilisateur (client, technicien ou admin)
    public function create(): void
    {
        try {

            $data = json_decode(file_get_contents("php://input"), true);

            $firstName = trim($data['first_name'] ?? '');
            $lastName  = trim($data['last_name']  ?? '');
            $email     = trim($data['email']      ?? '');
            $password  = trim($data['password']   ?? '');
            $role      = trim($data['role']        ?? 'client');

            // Champs obligatoires
            if (
                empty($firstName) ||
                empty($lastName)  ||
                empty($email)     ||
                empty($password)
            ) {
                http_response_code(422);
                echo json_encode([
                    "success" => false,
                    "message" => "Champs requis manquants"
                ]);
                return;
            }

            // Validation du rôle
            if (!in_array($role, self::ROLES_AUTORISES)) {
                http_response_code(422);
                echo json_encode([
                    "success" => false,
                    "message" => "Rôle invalide. Valeurs acceptées : client, technicien, admin"
                ]);
                return;
            }

            $db = Database::getConnection();

            // Email déjà utilisé ?
            $check = $db->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);

            if ($check->fetch()) {
                http_response_code(409);
                echo json_encode([
                    "success" => false,
                    "message" => "Email déjà utilisé"
                ]);
                return;
            }

            $fullName       = $firstName . ' ' . $lastName;
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $db->prepare("
                INSERT INTO users
                (
                    full_name,
                    email,
                    password_hash,
                    must_change_password,
                    role
                )
                VALUES (?, ?, ?, 1, ?)
            ");

            $stmt->execute([
                $fullName,
                $email,
                $hashedPassword,
                $role
            ]);

            echo json_encode([
                "success" => true,
                "message" => ucfirst($role) . " créé avec succès"
            ]);

        } catch (Exception $e) {

            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Erreur serveur",
                "error"   => $e->getMessage()
            ]);
        }
    }

    // supprimer un utilisateur
    public function delete(int $id): void
    {
        try {

            $db = Database::getConnection();

            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            echo json_encode([
                "success" => true,
                "message" => "Utilisateur supprimé"
            ]);

        } catch (Exception $e) {

            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Erreur serveur",
                "error"   => $e->getMessage()
            ]);
        }
    }
}
