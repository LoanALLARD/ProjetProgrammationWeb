<?php

namespace controllers;

use http\Message\Body;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

class ForgottenpasswordController
{

    // Int to store reset code
    private int $code = 0;

    // Array to store SMTP's configuration
    private array $config;

    public function __construct() {
        $this->config = require __DIR__ . '/../config/config.php';
    }

    public function index() {
        $pageTitle = "Mot de passe oublié";
        require __DIR__ . '/../views/forgottenPassword.php';
    }

    // Getter $code
    public function getCode(): int {
        return $this->code;
    }

    // Setter $code
    public function setCode(int $code): void {
        $this->code = $code;
    }

    public function changePassword() {
        session_start();
        // Retreives the email adress entered
        $emailDestinataire = $_POST['email'];

        try {
            $mail = new PHPMailer(true);

            // Store SMTP configuration -> $mail
            $mail->isSMTP();
            $mail->Host       = $this->config['smtp_host'];
            $mail->Port       = $this->config['smtp_port'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->config['smtp_user'];
            $mail->Password   = $this->config['smtp_pass'];
            $mail->SMTPSecure = $this->config['smtp_secure'];

            // Email information (sender, website name)
            $mail->setFrom($this->config['smtp_user'], 'PDW');

            // Recipient
            $mail->addAddress($emailDestinataire);

            $this->generationCode();

            // Email content
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Réinitialisation de votre mot de passe';
            $mail->Body    = '<p>Bonjour, suite à votre demande sur notre site, veuillez retrouver ci-dessous votre code afin de réinitialiser votre mot de passe.</p>Code : ' . $this->getCode() . '<br><p>L\'équipe de PDW vous remercie.</p>';
            $mail->AltBody = 'Bonjour ! Ceci est la version texte du mail.';

            // Send email
            $mail->send();

            require __DIR__ . '/../views/resetPassword.php';
        } catch (Exception $e) {
            echo "Erreur lors de l’envoi du mail : {$mail->ErrorInfo}";
        }
    }

    public function generationCode() {
        $code = random_int(100000, 999999);
        $this->setCode($code);
        $_SESSION['reset_code'] = $code;
    }

    public function verificationCode() {
        session_start();
        $enteredCode = $_POST['enteredCode'] ?? null;

        if ($enteredCode === null) {
            echo "Aucun code saisi.";
            return;
        }

        $savedCode = $_SESSION['reset_code'] ?? null;

        if ($savedCode === null) {
            echo "Aucun code généré.";
            return;
        }
        if ((int)$enteredCode === (int)$savedCode) {
            echo "Code correct";
        } else {
            echo "Code incorrect";
        }
    }


}