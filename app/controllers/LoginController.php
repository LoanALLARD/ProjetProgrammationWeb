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

            $query = $db->prepare('SELECT * FROM users where identifiant = :identifiant');
            $query->bindParam(":identifiant", $identifiant, \PDO::PARAM_STR);
            $query->execute();
            $user = $query->fetch(\PDO::FETCH_ASSOC);


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
                return;
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erreur lors de la connexion : " . $e->getMessage();
            header("Location: /index.php?url=login/index");
            exit;
        }
    }
}