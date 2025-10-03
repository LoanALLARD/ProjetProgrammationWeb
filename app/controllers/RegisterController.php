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
        require __DIR__ . '/../views/register.php';
    }

    public function register() {
        try {
            $identifiant = trim($_POST['identifiant'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirmation = $_POST['passwordConfirmation'] ?? '';

            if ($password !== $passwordConfirmation) {
                $_SESSION['error'] = "Les mots de passe ne correspondent pas !";
                header("Location: /index.php?url=register/index");
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Format d'email invalide !";
                header("Location: /index.php?url=register/index");
                exit;
            }

            if (strlen($password) < 8) {
                $_SESSION['error'] = "Le mot de passe doit contenir au moins 8 caractères !";
                header("Location: /index.php?url=register/index");
                exit;
            }

            $db = Database::getInstance()->getConnection();

            $checkQuery = $db->prepare('SELECT COUNT(*) FROM users WHERE IDENTIFIANT = :identifiant OR EMAIL = :email');
            $checkQuery->bindParam(":email", $emailDestinataire, \PDO::PARAM_STR);
            $checkQuery->bindParam(":identifiant", $identifiant, \PDO::PARAM_STR);
            $checkQuery->execute();
            $count = $checkQuery->fetchColumn();

            if ($count > 0) {
                echo "Cet identifiant ou cet email est déjà utilisé !";
                $checkQuery = null ;
                return;
            }
            $checkQuery = null;

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
                echo "Inscription réussie !";
                // Automatic user login
                session_start();
                $_SESSION['user_id'] = session_id();
                $_SESSION['identifiant'] = $identifiant;
                header("Location: /index.php?url=register/index");
            } else {
                echo "Erreur lors de l'inscription.";
            }

            $query = null;

        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur lors de l'inscription : " . $e->getMessage();
            header("Location: /index.php?url=register/index");
            exit;
        }
    }
}