<?php

namespace controllers;

require_once __DIR__ . '/../core/Database.php';
use core\Database;

class RegisterController
{
    public function index() {
        $pageTitle = "Inscription";
        require __DIR__ . '/../views/register.php';
    }

    public function register() {
        try {
            // Validation of received data
            $identifiant = trim($_POST['identifiant'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirmation = $_POST['passwordConfirmation'] ?? '';

            // Basic validation
            if (empty($identifiant) || empty($email) || empty($password)) {
                echo "Identifiant, email et mot de passe sont requis !";
                return;
            }

            // Check that the passwords match.
            if ($password !== $passwordConfirmation) {
                echo "Les mots de passe ne correspondent pas !";
                return;
            }

            // Email validation
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Format d'email invalide !";
                return;
            }

            // Password validation
            if (strlen($password) < 8) {
                echo "Le mot de passe doit contenir au moins 8 caractères !";
                return;
            }

            $db = Database::getInstance()->getConnection();

            // Check whether the username or email address already exists
            // $checkStmt = $db->prepare("SELECT COUNT(*) FROM users WHERE IDENTIFIANT = ? OR EMAIL = ?");
            // $checkStmt->bind_param("ss", $identifiant, $email);
            // $checkStmt->execute();
            // $checkResult = $checkStmt->get_result();
            // $count = $checkResult->fetch_row()[0];


            $checkQuery = $db->prepare('SELECT COUNT(*) FROM users WHERE IDENTIFIANT = :identifiant OR EMAIL = :email');
            $checkQuery->bindParam(":email", $emailDestinataire, \PDO::PARAM_STR);
            $checkQuery->bindParam(":identifiant", $identifiant, \PDO::PARAM_STR);
            $checkQuery->execute();
            $count = $checkQuery->fetchColumn(); // recover the first line of the query

            if ($count > 0) {
                echo "Cet identifiant ou cet email est déjà utilisé !";
                $checkQuery = null ;
                return;
            }
            $checkQuery = null;

            // Password hashing
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $inscription_date = date("Y-m-d H:i:s");

            // Inserting the new user into the database
            
            //$stmt = $db->prepare("INSERT INTO users (IDENTIFIANT, EMAIL, TELEPHONE, PASSWORD, INSCRIPTION_DATE) VALUES (?, ?, ?, ?, ?)");
            //$stmt->bind_param("sssss", $identifiant, $email, $telephone, $hash, $inscription_date);

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
                header("Location: /index.php?url=home/index");
            } else {
                echo "Erreur lors de l'inscription.";
            }

            $query = null;

        } catch (Exception $e) {
            echo "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}