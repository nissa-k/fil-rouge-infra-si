<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/Database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class AuthController {

    // =========================
    // LOGIN
    // =========================
    public function login() {

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

        $_SESSION['user'] = [
            "id" => $user['id'],
            "name" => $user['full_name'],
            "email" => $user['email'],
            "role" => $user['role']
        ];

        echo json_encode([
            "success" => true,
            "user" => $_SESSION['user'],
            "force_password_change" =>
                (bool)$user['must_change_password']
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
                "success" => false
            ]);

            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        $newPassword = trim($data['password'] ?? '');

        if (strlen($newPassword) < 6) {

            echo json_encode([
                "success" => false,
                "message" => "Mot de passe trop court"
            ]);

            return;
        }

        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT password_hash
            FROM users
            WHERE id = ?
        ");

        $stmt->execute([
            $_SESSION['user']['id']
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (
            password_verify(
                $newPassword,
                $user['password_hash']
            )
        ) {

            echo json_encode([
                "success" => false,
                "message" => "Le nouveau mot de passe doit être différent"
            ]);

            return;
        }

        $update = $db->prepare("
            UPDATE users
            SET
                password_hash = ?,
                must_change_password = 0
            WHERE id = ?
        ");

        $update->execute([
            password_hash($newPassword, PASSWORD_DEFAULT),
            $_SESSION['user']['id']
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Mot de passe changé"
        ]);
    }

    // =========================
    // FORGOT PASSWORD
    // =========================
    public function forgotPassword() {

        $data = json_decode(file_get_contents("php://input"), true);

        $email = trim($data['email'] ?? '');

        if (!$email) {

            echo json_encode([
                "success" => false,
                "message" => "Email requis"
            ]);

            return;
        }

        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT *
            FROM users
            WHERE email = ?
        ");

        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {

            echo json_encode([
                "success" => false,
                "message" => "Utilisateur introuvable"
            ]);

            return;
        }

        $token = bin2hex(random_bytes(32));

        $save = $db->prepare("
            UPDATE users
            SET reset_token = ?
            WHERE id = ?
        ");

        $save->execute([
            $token,
            $user['id']
        ]);

        $resetLink =
            "http://localhost/fil-rouge-infra-si/frontend/reset-password.html?token="
            . $token;

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            // 🔥 TON GMAIL
            $mail->Username = 'safouzemmar@gmail.com';

            // 🔥 TON APP PASSWORD
            $mail->Password = 'evpkkongbkjvkjuz ';

            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom(
                'TONMAIL@gmail.com',
                'Fil Rouge'
            );

            $mail->addAddress($email);

            $mail->isHTML(true);

            $mail->Subject = 'Réinitialisation mot de passe';

            $mail->Body = "
                <h2>Réinitialisation</h2>

                <p>
                    Cliquez ici :
                </p>

                <a href='$resetLink'>
                    Réinitialiser mon mot de passe
                </a>
            ";

            $mail->send();

            echo json_encode([
                "success" => true,
                "message" => "Email envoyé"
            ]);

        } catch (Exception $e) {

            echo json_encode([
                "success" => false,
                "message" => $mail->ErrorInfo
            ]);
        }
    }

    // =========================
    // RESET PASSWORD
    // =========================
    public function resetPassword() {

        $data = json_decode(file_get_contents("php://input"), true);

        $token = $data['token'] ?? '';
        $password = $data['password'] ?? '';

        if (!$token || !$password) {

            echo json_encode([
                "success" => false
            ]);

            return;
        }

        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT *
            FROM users
            WHERE reset_token = ?
        ");

        $stmt->execute([$token]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {

            echo json_encode([
                "success" => false,
                "message" => "Token invalide"
            ]);

            return;
        }

        $update = $db->prepare("
            UPDATE users
            SET
                password_hash = ?,
                reset_token = NULL
            WHERE id = ?
        ");

        $update->execute([
            password_hash($password, PASSWORD_DEFAULT),
            $user['id']
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Mot de passe réinitialisé"
        ]);
    }
}