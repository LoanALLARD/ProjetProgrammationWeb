<?php

namespace controllers;

use core\Database;

class UpdatePasswordController
{
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        // Vérifier que le code a été validé
        if (!isset($_SESSION['code_verified']) || $_SESSION['code_verified'] !== true) {
            $_SESSION['error_message'] = "Vous devez d'abord valider le code de réinitialisation.";
            header('Location: index.php?url=forgotten-password/index');
            exit;
        }

        $pageTitle = "Nouveau mot de passe";

        // Récupérer les messages
        $successMessage = $_SESSION['success_message'] ?? null;
        $errorMessage = $_SESSION['error_message'] ?? null;
        unset($_SESSION['success_message'], $_SESSION['error_message']);

        require __DIR__ . '/../views/updatePassword.php';
    }

    public function updatePassword() {
        // Vérifier que le code a été validé
        if (!isset($_SESSION['code_verified']) || $_SESSION['code_verified'] !== true) {
            $_SESSION['error_message'] = "Session expirée. Veuillez recommencer.";
            header('Location: index.php?url=forgotten-password/index');
            exit;
        }

        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['passwordConfirmation'] ?? '';

        // Validation du mot de passe
        if (empty($password) || empty($passwordConfirmation)) {
            $_SESSION['error_message'] = "Veuillez remplir tous les champs.";
            header('Location: index.php?url=update-password/index');
            exit;
        }

        if ($password !== $passwordConfirmation) {
            $_SESSION['error_message'] = "Les mots de passe ne correspondent pas !";
            header('Location: index.php?url=update-password/index');
            exit;
        }

        if (strlen($password) < 8) {
            $_SESSION['error_message'] = "Le mot de passe doit contenir au moins 8 caractères !";
            header('Location: index.php?url=update-password/index');
            exit;
        }

        // Vérifier qu'on a bien l'email
        if (!isset($_SESSION['reset_email'])) {
            $_SESSION['error_message'] = "Session expirée. Veuillez recommencer.";
            header('Location: index.php?url=forgotten-password/index');
            exit;
        }

        $email = $_SESSION['reset_email'];
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Mettre à jour le mot de passe dans la base de données
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE users SET PASSWORD = ? WHERE EMAIL = ?");
        $stmt->bind_param("ss", $hash, $email);

        if ($stmt->execute()) {
            // Nettoyer toutes les variables de session liées à la réinitialisation
            unset(
                $_SESSION['reset_code'],
                $_SESSION['reset_code_time'],
                $_SESSION['reset_email'],
                $_SESSION['code_verified']
            );

            $_SESSION['success_message'] = "Votre mot de passe a été réinitialisé avec succès ! Vous pouvez maintenant vous connecter.";
            header('Location: index.php?url=login/index');
            exit;
        } else {
            $_SESSION['error_message'] = "Erreur lors de la mise à jour du mot de passe. Veuillez réessayer.";
            header('Location: index.php?url=update-password/index');
            exit;
        }
    }
}