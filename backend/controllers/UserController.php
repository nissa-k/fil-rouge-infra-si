<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/database.php';

class UserController {

    // liste des utilisateurs
    public function index() {

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
                "users" => $users
            ]);

        } catch (Exception $e) {

            http_response_code(500);

            echo json_encode([
                "success" => false,
                "message" => "Erreur serveur",
                "error" => $e->getMessage()
            ]);
        }
    }

    // créer un utilisateur
    public function create() {

        try {

            $data = json_decode(file_get_contents("php://input"), true);

            $firstName = trim($data['first_name'] ?? '');
            $lastName = trim($data['last_name'] ?? '');
            $email = trim($data['email'] ?? '');
            $password = trim($data['password'] ?? '');
            $role = trim($data['role'] ?? 'user');

            // Vérification champs
            if (
                empty($firstName) ||
                empty($lastName) ||
                empty($email) ||
                empty($password)
            ) {

                echo json_encode([
                    "success" => false,
                    "message" => "Champs requis manquants"
                ]);

                return;
            }

            $db = Database::getConnection();

            // Vérifie si email existe déjà
            $check = $db->prepare("
                SELECT id FROM users
                WHERE email = ?
            ");

            $check->execute([$email]);

            if ($check->fetch()) {

                echo json_encode([
                    "success" => false,
                    "message" => "Email déjà utilisé"
                ]);

                return;
            }

            $fullName = $firstName . ' ' . $lastName;

            // Hash du mot de passe
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
                "message" => "Utilisateur créé"
            ]);

        } catch (Exception $e) {

            http_response_code(500);

            echo json_encode([
                "success" => false,
                "message" => "Erreur serveur",
                "error" => $e->getMessage()
            ]);
        }
    }

    // supprimer un utilisateur
    public function delete($id) {

        try {

            $db = Database::getConnection();

            $stmt = $db->prepare("
                DELETE FROM users
                WHERE id = ?
            ");

            $stmt->execute([$id]);

            echo json_encode([
                "success" => true,
                "message" => "Utilisateur supprimé"
            ]);

        } catch (Exception $e) {
            
            // En cas d'erreur, on retourne une réponse 500 avec le message d'erreur
            http_response_code(500);

            echo json_encode([
                "success" => false,
                "message" => "Erreur serveur",
                "error" => $e->getMessage()
            ]);
        }
    }
}