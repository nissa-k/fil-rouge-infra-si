<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class MailService {

    public static function sendResetPasswordMail($to, $token) {

        $resetLink = "http://localhost/fil-rouge-infra-si/frontend/reset-password.html?token=" . $token;

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();

            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            // 🔥 TON EMAIL GMAIL
            $mail->Username = 'safouzemmar@gmail.com';

            // 🔥 MOT DE PASSE APPLICATION GOOGLE
            $mail->Password = 'evpkkongbkjvkjuz ';

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('safouzemmar@gmail.com', 'Fil Rouge');

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