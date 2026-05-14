<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/database.php';

class UserController {

    // 🔥 GET ALL USERS
    public function index() {
        try {
            $db = Database::getConnection();

            $stmt = $db->query("SELECT id, full_name, email, role FROM users ORDER BY id DESC");

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

    // 🔥 CREATE USER
    public function create() {

        $data = json_decode(file_get_contents("php://input"), true);

        $firstName = trim($data['first_name'] ?? '');
        $lastName = trim($data['last_name'] ?? '');
        $email = trim($data['email'] ?? '');
        $role = $data['role'] ?? 'user';

        if (!$firstName || !$lastName || !$email) {
            echo json_encode([
                "success" => false,
                "message" => "Champs requis manquants"
            ]);
            return;
        }

        $db = Database::getConnection();

        $fullName = $firstName . ' ' . $lastName;

        // 🔥 mot de passe par défaut
        $defaultPassword = "123456";

        $stmt = $db->prepare("
            INSERT INTO users (full_name, email, password_hash, must_change_password, role)
            VALUES (?, ?, ?, 1, ?)
        ");

        $stmt->execute([
            $fullName,
            $email,
            password_hash($defaultPassword, PASSWORD_DEFAULT),
            $role
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Utilisateur créé"
        ]);
    }

    // 🔥 DELETE USER
    public function delete($id) {
        $db = Database::getConnection();

        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode([
            "success" => true,
            "message" => "Utilisateur supprimé"
        ]);
    }
}