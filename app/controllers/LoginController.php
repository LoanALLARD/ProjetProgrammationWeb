<?php

namespace controllers;

require_once __DIR__ . '/../core/Database.php';
use core\Database;
class LoginController
{
    public function index() {
        require __DIR__ . '/../views/login.php';
    }

    public function login() {
        try {
            // Validation des données reçues
            if (empty($_POST["identifiant"]) || empty($_POST["password"])) {
                echo "Identifiant et mot de passe requis !";
                return;
            }

            $db = Database::getInstance()->getConnection();

            $identifiant = trim($_POST["identifiant"]);
            $password = $_POST["password"];

            // Requête sécurisée avec paramètres liés
            $stmt = $db->prepare("SELECT * FROM users WHERE IDENTIFIANT = ?");
            $stmt->bind_param("s", $identifiant);
            $stmt->execute();

            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['PASSWORD'])) {
                // Connexion réussie - démarrer la session
                session_start();
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['identifiant'] = $user['IDENTIFIANT'];

                echo "Connexion réussie !";
                // Redirection possible ici
                // header('Location: /dashboard');
            } else {
                echo "Identifiant ou mot de passe incorrect !";
            }

            $stmt->close();

        } catch (Exception $e) {
            echo "Erreur lors de la connexion : " . $e->getMessage();
        }
    }
}