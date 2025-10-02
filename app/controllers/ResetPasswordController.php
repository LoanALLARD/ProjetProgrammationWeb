<?php

namespace controllers;

class ResetPasswordController
{
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        // Check that a code has been generated
        if (!isset($_SESSION['reset_code'])) {
            $_SESSION['error_message'] = "Aucune demande de réinitialisation en cours.";
            header('Location: index.php?url=forgotten-password/index');
            exit;
        }

        $pageTitle = "Saisir le code de réinitialisation";

        // Retrieve messages
        $successMessage = $_SESSION['success_message'] ?? null;
        $errorMessage = $_SESSION['error_message'] ?? null;
        unset($_SESSION['success_message'], $_SESSION['error_message']);

        require __DIR__ . '/../views/resetPassword.php';
    }

    public function verificationCode() {
        // Check if the code is provided
        if (empty($_POST['enteredCode'])) {
            $_SESSION['error_message'] = "Veuillez saisir le code reçu par e-mail.";
            header('Location: index.php?url=reset-password/index');
            exit;
        }

        $enteredCode = trim($_POST['enteredCode']);
        $savedCode = $_SESSION['reset_code'] ?? null;

        // Check if the code has been generated
        if ($savedCode === null) {
            $_SESSION['error_message'] = "Aucun code généré. Veuillez recommencer la procédure.";
            header('Location: index.php?url=forgotten-password/index');
            exit;
        }

        // Check the expiry date of the code
        if (isset($_SESSION['reset_code_time'])) {
            $elapsed = time() - $_SESSION['reset_code_time'];
            if ($elapsed > 900) { // 900s = 15min
                unset($_SESSION['reset_code'], $_SESSION['reset_code_time']);
                $_SESSION['error_message'] = "Le code a expiré. Veuillez recommencer.";
                header('Location: index.php?url=forgotten-password/index');
                exit;
            }
        }

        // Check the code
        if ((int)$enteredCode === (int)$savedCode) {
            // Sucess message
            $_SESSION['code_verified'] = true;
            $_SESSION['success_message'] = "Code validé ! Veuillez saisir votre nouveau mot de passe.";
            header('Location: index.php?url=update-password/index');
            exit;
        } else {
            // Error message
            $_SESSION['error_message'] = "Code incorrect. Veuillez réessayer.";
            header('Location: index.php?url=reset-password/index');
            exit;
        }
    }
}