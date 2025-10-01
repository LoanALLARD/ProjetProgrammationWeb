<?php

namespace controllers;

require_once __DIR__ . '/../core/Database.php';
use core\Database;
class LoginController
{
    public function index() {
        $pageTitle = "Connexion";
        require __DIR__ . '/../views/login.php';
    }

    public function login() {
        session_start();
        try {
            // get instance od the database
            $db = Database::getInstance()->getConnection();
            $identifiant = trim($_POST["identifiant"]);
            $password = $_POST["password"];

            // Retrieves information from the database
            $stmt = $db->prepare("SELECT * FROM users WHERE IDENTIFIANT = ?");
            $stmt->bind_param("s", $identifiant);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            // Check the validity of the information
            if ($user =! null && password_verify($password, $user['PASSWORD'])) {
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['identifiant'] = $user['IDENTIFIANT'];
                $_SESSION['success'] = "Connexion réussie !";
                header("Location: /index.php?url=home/index");
                exit;
            } else {
                $_SESSION['error'] = "Identifiant ou mot de passe incorrect !";
                header("Location: /index.php?url=login/index");
                exit;
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erreur lors de la connexion : " . $e->getMessage();
            header("Location: /index.php?url=login/index");
            exit;
        }
    }
}