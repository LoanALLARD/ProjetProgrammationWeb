<?php

namespace controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

class ForgottenpasswordController
{

    private array $config;

    public function __construct() {
        $this->config = require __DIR__ . '/../config/config.php';
    }

    public function index() {
        $pageTitle = "Mot de passe oublié";
        require __DIR__ . '/../views/forgottenPassword.php';
    }

    public function changePassword() {
        $this->config = require __DIR__ . '/../config/config.php';
        $emailDestinataire = $_POST['email'];
        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host       = $this->config['smtp_host'];
            $mail->Port       = $this->config['smtp_port'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->config['smtp_user'];
            $mail->Password   = $this->config['smtp_pass'];
            $mail->SMTPSecure = $this->config['smtp_secure'];

            $mail->setFrom($this->config['smtp_user'], 'PDW');

            $code = random_bytes(6);

            // Recipient
            $mail->addAddress($emailDestinataire);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de votre mot de passe';
            $mail->Body    = '<p>Bonjour, suite à votre demande sur notre site, veuillez retrouver ci-dessous votre code afin de réinitialiser votre mot de passe.</p><br> Code : ' . $code . '<br><p>L\'équipe de PDW vous remercie.</p>';
            $mail->AltBody = 'Bonjour ! Ceci est la version texte du mail.';

            $mail->send();

            echo "Mail envoyé avec succès";
        } catch (Exception $e) {
            echo "Erreur lors de l’envoi du mail : {$mail->ErrorInfo}";
        }
    }

}