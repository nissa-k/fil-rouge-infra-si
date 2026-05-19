<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/Database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class AuthController {

    // login + 2FA

    public function login() {

        $data = json_decode(file_get_contents("php://input"), true);

        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (!$email || !$password) {

            echo json_encode([
                "success" => false,
                "message" => "Champs requis manquants"
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
                "message" => "Email ou mot de passe incorrect"
            ]);

            return;
        }

        // modification du mot de passe à la première connexion

        if ((int)$user['must_change_password'] === 1) {

            $_SESSION['user'] = [
                "id" => $user['id'],
                "name" => $user['full_name'],
                "email" => $user['email'],
                "role" => $user['role']
            ];

            echo json_encode([
                "success" => true,
                "must_change_password" => true
            ]);

            return;
        }

        //code de double authentification

        $code = rand(100000, 999999);

        $db = Database::getConnection();

        $stmt = $db->prepare("
            UPDATE users
            SET
                two_factor_code = ?,
                two_factor_expires = DATE_ADD(NOW(), INTERVAL 10 MINUTE)
            WHERE id = ?
        ");

        $stmt->execute([
            $code,
            $user['id']
        ]);

        //envoi du mail

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();

            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;

            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['MAIL_PORT'];

            $mail->setFrom(
                $_ENV['MAIL_FROM'],
                'Fil Rouge'
            );

            $mail->addAddress($user['email']);

            $mail->isHTML(true);

            $mail->Subject = 'Code de connexion';

            $mail->Body = "
                <h1>Double authentification</h1>

                <p>Votre code de connexion :</p>

                <h2>$code</h2>

                <p>Ce code expire dans 10 minutes.</p>
            ";

            $mail->send();

        } catch (Exception $e) {

            echo json_encode([
                "success" => false,
                "message" => $mail->ErrorInfo
            ]);

            return;
        }

        echo json_encode([
            "success" => true,
            "requires_2fa" => true,
            "email" => $user['email']
        ]);
    }

    // vérification du code de double authentification
    public function verify2FA() {

        $data = json_decode(file_get_contents("php://input"), true);

        $email = trim($data['email'] ?? '');
        $code = trim($data['code'] ?? '');

        if (!$email || !$code) {

            echo json_encode([
                "success" => false,
                "message" => "Code requis"
            ]);

            return;
        }

        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT *
            FROM users
            WHERE
                email = ?
                AND two_factor_code = ?
                AND two_factor_expires > NOW()
        ");

        $stmt->execute([
            $email,
            $code
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {

            echo json_encode([
                "success" => false,
                "message" => "Code invalide ou expiré"
            ]);

            return;
        }

        // clear 2FA code
        $clear = $db->prepare("
            UPDATE users
            SET
                two_factor_code = NULL,
                two_factor_expires = NULL
            WHERE id = ?
        ");

        $clear->execute([
            $user['id']
        ]);

        // SESSION
        $_SESSION['user'] = [
            "id" => $user['id'],
            "name" => $user['full_name'],
            "email" => $user['email'],
            "role" => $user['role']
        ];

        echo json_encode([
            "success" => true,
            "user" => $_SESSION['user']
        ]);
    }

    // logout
    public function logout() {

        session_destroy();

        echo json_encode([
            "success" => true
        ]);
    }

    // verifie si l'utilisateur est connecté et retourne ses infos
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

    // changement de mot de passe
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
            UPDATE users
            SET
                password_hash = ?,
                must_change_password = 0
            WHERE id = ?
        ");

        $stmt->execute([
            password_hash($newPassword, PASSWORD_DEFAULT),
            $_SESSION['user']['id']
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Mot de passe changé"
        ]);
    }

    // mot de passe oublié
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

        $stmt = $db->prepare("
            UPDATE users
            SET reset_token = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $token,
            $user['id']
        ]);

        $resetLink =
            "http://localhost/fil-rouge-infra-si/frontend/reset-password.html?token="
            . $token;

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();

            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;

            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['MAIL_PORT'];

            $mail->setFrom(
                $_ENV['MAIL_FROM'],
                'Fil Rouge'
            );

            $mail->addAddress($email);

            $mail->isHTML(true);

            $mail->Subject = 'Réinitialisation mot de passe';

            $mail->Body = "
                <h2>Réinitialisation mot de passe</h2>

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

    // réinitialisation du mot de passe
    public function resetPassword() {

        $data = json_decode(file_get_contents("php://input"), true);

        $token = $data['token'] ?? '';
        $password = $data['password'] ?? '';

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

        $stmt = $db->prepare("
            UPDATE users
            SET
                password_hash = ?,
                reset_token = NULL
            WHERE id = ?
        ");

        $stmt->execute([
            password_hash($password, PASSWORD_DEFAULT),
            $user['id']
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Mot de passe modifié"
        ]);
    }
}