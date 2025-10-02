<?php

namespace controllers;

use core\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

class ForgottenPasswordController
{
    private array $config;

    public function __construct() {
        // Start the session (only once at the beginning)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->config = require __DIR__ . '/../config/config.php';
    }

    public function index() {
        $pageTitle = "Mot de passe oublié";

        // Retrieve and delete error messages
        $errorMessage = $_SESSION['error_message'] ?? null;
        unset($_SESSION['error_message']);

        require __DIR__ . '/../views/forgottenPassword.php';
    }

    public function changePassword()
    {
        // Verify that the email address is provided
        if (empty($_POST['email'])) {
            $_SESSION['error_message'] = "Veuillez saisir une adresse e-mail.";
            header('Location: index.php?url=forgotten-password/index');
            exit;
        }

        $emailDestinataire = trim($_POST['email']);
        $db = Database::getInstance()->getConnection();

        // Check if the email exists in the DB
        $checkStmt = $db->prepare("SELECT COUNT(*) FROM users WHERE EMAIL = ?");
        $checkStmt->bind_param("s", $emailDestinataire);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $count = $checkResult->fetch_row()[0];
        $checkStmt->close();

        if ($count === 0) {
            $_SESSION['error_message'] = "L'adresse e-mail n'existe pas !";
            header('Location: index.php?url=forgotten-password/index');
            exit;
        }

        // Generate the code before sending the email
        $code = $this->generationCode();

        try {
            $mail = new PHPMailer(true);

            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = $this->config['smtp_host'];
            $mail->Port = $this->config['smtp_port'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['smtp_user'];
            $mail->Password = $this->config['smtp_pass'];
            $mail->SMTPSecure = $this->config['smtp_secure'];

            // Sender and recipient
            $mail->setFrom($this->config['smtp_user'], 'PDW');
            $mail->addAddress($emailDestinataire);

            // Email content
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Réinitialisation de votre mot de passe';
            $mail->Body = '<p>Bonjour,</p><p>Suite à votre demande sur notre site, veuillez retrouver ci-dessous votre code afin de réinitialiser votre mot de passe.</p><p><strong>Code : ' . $code . '</strong></p><p>L\'équipe de PDW vous remercie.</p>';
            $mail->AltBody = 'Bonjour ! Code de réinitialisation : ' . $code;

            // Send email
            $mail->send();

            // Save the email for the next step
            $_SESSION['reset_email'] = $emailDestinataire;
            $_SESSION['success_message'] = "Un code de réinitialisation a été envoyé à votre adresse e-mail.";

            // Redirect to the code entry page
            header('Location: index.php?url=reset-password/index');
            exit;

        } catch (Exception $e) {
            $_SESSION['error_message'] = "Erreur lors de l'envoi du mail. Veuillez réessayer.";
            header('Location: index.php?url=forgotten-password/index');
            exit;
        }
    }

    private function generationCode(): int {
        $code = random_int(100000, 999999);
        $_SESSION['reset_code'] = $code;
        $_SESSION['reset_code_time'] = time();
        return $code;
    }
}