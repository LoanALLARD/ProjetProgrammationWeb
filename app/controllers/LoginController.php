<?php

namespace controllers;

require_once __DIR__ . '/../core/Database.php';
use core\Database;
class LoginController
{
    public function index() {
        $pageTitle = "Connexion";

        // Variables for display messages in the view
        $successMessage = $_SESSION['success_message'] ?? null;
        $errorMessage = $_SESSION['error_message'] ?? null;

        // Unset variables to free up space
        unset($_SESSION['success_message'], $_SESSION['error_message']);

        // Call view
        require __DIR__ . '/../views/login.php';
    }


    public function login() {
        // Try to login
        try {
            // Get database instance
            $db = Database::getInstance()->getConnection();

            // Retrieves identifiant & password from the form
            $identifiant = trim($_POST["identifiant"]);
            $password = $_POST["password"];

            // Prepare & execute the SQL query
            $query = $db->prepare('SELECT * FROM users where identifiant = :identifiant');
            $query->bindParam(":identifiant", $identifiant, \PDO::PARAM_STR);
            $query->execute();
            $user = $query->fetch(\PDO::FETCH_ASSOC);

            // Check the different details
            if ($user !== null && password_verify($password, $user['PASSWORD'])) {
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['identifiant'] = $user['IDENTIFIANT'];
                $_SESSION['success_message'] = "Connexion réussie !";
                header("Location: /index.php?url=home/index");
                exit;
            } else {
                $_SESSION['error_message'] = "Identifiant ou mot de passe incorrect !";
                header("Location: /index.php?url=login/index");
                exit;
            }
        // Exception if it doesn't work
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Erreur lors de la connexion : " . $e->getMessage();
            header("Location: /index.php?url=login/index");
            exit;
        }
    }

    public function logout() {
        try {
            unset($_SESSION['user_id']);
            unset($_SESSION['identifiant']);
            unset($_SESSION['success_message']);
            unset($_SESSION['error_message']);
            session_unset();
            session_destroy();

            header("Location: /index.php?url=home/index");
            exit();
        }
        catch (\Exception $e) {
            $_SESSION['error_message'] = "Erreur lors de la déconnexion : " . $e->getMessage();
        }
    }
}