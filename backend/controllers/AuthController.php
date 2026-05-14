<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/Database.php';

class AuthController {

    // =========================
    // LOGIN
    // =========================
    public function login() {

        ini_set('display_errors', 0);

        $data = json_decode(file_get_contents("php://input"), true);

        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (!$email || !$password) {

            echo json_encode([
                "success" => false,
                "message" => "Champs requis manquants."
            ]);

            return;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (
            !$user ||
            !password_verify($password, $user['password_hash'])
        ) {

            echo json_encode([
                "success" => false,
                "message" => "Email ou mot de passe incorrect."
            ]);

            return;
        }

        // ROLE
        $role = $user['role'] ?? (
            $user['email'] === 'admin@test.com'
                ? 'admin'
                : 'user'
        );

        $_SESSION['user'] = [
            "id" => $user['id'],
            "name" => $user['full_name'],
            "email" => $user['email'],
            "role" => $role
        ];

        echo json_encode([
            "success" => true,
            "user" => $_SESSION['user'],
            "force_password_change" =>
                isset($user['must_change_password'])
                    ? (bool)$user['must_change_password']
                    : false
        ]);
    }

    // =========================
    // LOGOUT
    // =========================
    public function logout() {

        session_destroy();

        echo json_encode([
            "success" => true
        ]);
    }

    // =========================
    // ME
    // =========================
    public function me() {

        if (!isset($_SESSION['user'])) {

            http_response_code(401);

            echo json_encode([
                "success" => false
            ]);

            return;
        }

        echo json_encode([
            "success" => true,
            "user" => $_SESSION['user']
        ]);
    }

    // =========================
    // CHANGE PASSWORD
    // =========================
    public function changePassword() {

        if (!isset($_SESSION['user'])) {

            http_response_code(401);

            echo json_encode([
                "success" => false,
                "message" => "Non authentifié"
            ]);

            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        $newPassword = trim($data['password'] ?? '');

        // Vérifie vide
        if (empty($newPassword)) {

            echo json_encode([
                "success" => false,
                "message" => "Mot de passe requis"
            ]);

            return;
        }

        // Vérifie longueur
        if (strlen($newPassword) < 6) {

            echo json_encode([
                "success" => false,
                "message" => "Mot de passe trop court"
            ]);

            return;
        }

        $db = Database::getConnection();

        // 🔥 récupère ancien mdp
        $stmt = $db->prepare("
            SELECT password_hash
            FROM users
            WHERE id = ?
        ");

        $stmt->execute([
            $_SESSION['user']['id']
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {

            echo json_encode([
                "success" => false,
                "message" => "Utilisateur introuvable"
            ]);

            return;
        }

        // 🔥 interdit même mot de passe
        if (
            password_verify(
                $newPassword,
                $user['password_hash']
            )
        ) {

            echo json_encode([
                "success" => false,
                "message" => "Le nouveau mot de passe doit être différent de l'ancien"
            ]);

            return;
        }

        // Nouveau hash
        $hashedPassword = password_hash(
            $newPassword,
            PASSWORD_DEFAULT
        );

        // UPDATE
        $update = $db->prepare("
            UPDATE users
            SET
                password_hash = ?,
                must_change_password = 0
            WHERE id = ?
        ");

        $update->execute([
            $hashedPassword,
            $_SESSION['user']['id']
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Mot de passe changé"
        ]);
    }
}