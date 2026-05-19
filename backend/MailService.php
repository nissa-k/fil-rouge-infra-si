<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class MailService {

    //envoi du mail de réinitialisation de mot de passe
    public static function sendResetPasswordMail($to, $token) {

        $resetLink = "http://localhost/fil-rouge-infra-si/frontend/reset-password.html?token=" . $token;

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();

            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;

            $mail->Username = $_ENV['MAIL_USERNAME'];

            $mail->Password = $_ENV['MAIL_PASSWORD'];

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['MAIL_PORT'];

            $mail->setFrom($_ENV['MAIL_FROM'], 'Fil Rouge');

            $mail->addAddress($to);

            $mail->isHTML(true);

            $mail->Subject = 'Réinitialisation mot de passe';

            $mail->Body = "
                <h2>Mot de passe oublié</h2>

                <p>Clique sur le lien :</p>

                <a href='$resetLink'>
                    Réinitialiser mon mot de passe
                </a>

                <p>Le lien expire dans 1 heure.</p>
            ";

            $mail->send();

            return true;

        } catch (Exception $e) {

            return false;
        }
    }
}