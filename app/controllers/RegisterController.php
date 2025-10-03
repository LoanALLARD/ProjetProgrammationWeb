<?php

namespace controllers;

require_once __DIR__ . '/../core/Database.php';
use core\Database;

class RegisterController
{

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        $pageTitle = "Inscription";

        $successMessage = $_SESSION['success_message'] ?? null;
        $errorMessage = $_SESSION['error_message'] ?? null;

        unset($_SESSION['success_message'], $_SESSION['error_message']);

        require __DIR__ . '/../views/register.php';
    }

    public function register() {
        // Try to register
        try {
            $identifiant = trim($_POST['identifiant'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirmation = $_POST['passwordConfirmation'] ?? '';

            // Check if the password are the same
            if ($password !== $passwordConfirmation) {
                $_SESSION['error_message'] = "Les mots de passe ne correspondent pas !";
                header("Location: /index.php?url=register/index");
                exit;
            }

            // Check the email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error_message'] = "Format d'email invalide !";
                header("Location: /index.php?url=register/index");
                exit;
            }

            // Check the password complexity
            if (strlen($password) < 8) {
                $_SESSION['error_message'] = "Le mot de passe doit contenir au moins 8 caractères !";
                header("Location: /index.php?url=register/index");
                exit;
            }

            // Get database instance
            $db = Database::getInstance()->getConnection();

            // Preparation of the SQL query
            $checkQuery = $db->prepare('SELECT COUNT(*) FROM users WHERE IDENTIFIANT = :identifiant OR EMAIL = :email');
            $checkQuery->bindParam(":email", $emailDestinataire, \PDO::PARAM_STR);
            $checkQuery->bindParam(":identifiant", $identifiant, \PDO::PARAM_STR);
            $checkQuery->execute();
            $count = $checkQuery->fetchColumn();

            // If > 0, there is already a registered user with this email
            if ($count > 0) {
                echo "Cet identifiant ou cet email est déjà utilisé !";
                $checkQuery = null ;
                return;
            }

            // Clear the variable
            $checkQuery = null;

            // Password hash & get actual date
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $inscription_date = date("Y-m-d H:i:s");

            // Inserting the new user into the database
            $query = $db->prepare("INSERT INTO users (IDENTIFIANT, EMAIL, TELEPHONE, PASSWORD, INSCRIPTION_DATE) VALUES (:identifiant, :email, :telephone, :password, :inscription_date)");
            $query->bindParam(":identifiant", $identifiant, \PDO::PARAM_STR);
            $query->bindParam(":email", $email, \PDO::PARAM_STR);
            $query->bindParam(":telephone", $telephone, \PDO::PARAM_STR);
            $query->bindParam(":password", $hash, \PDO::PARAM_STR);
            $query->bindParam(":inscription_date", $inscription_date, \PDO::PARAM_STR);

            if ($query->execute()) {
                // Automatic user login
                session_start();
                $_SESSION['user_id'] = session_id();
                $_SESSION['identifiant'] = $identifiant;
                header("Location: /index.php?url=register/index");
            } else {
                echo "Erreur lors de l'inscription.";
            }

            // Clear the variable
            $query = null;

        // Exception if it doesn't work
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Erreur lors de l'inscription : " . $e->getMessage();
            header("Location: /index.php?url=register/index");
            exit;
        }
    }
}