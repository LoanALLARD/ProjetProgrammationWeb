<?php

namespace controllers;

require_once __DIR__ . '/../core/Database.php';
use core\Database;

class RegisterController
{
    public function index() {
        require __DIR__ . '/../views/register.php';
    }

    public function register() {
        try {
            // Validation des données reçues
            $identifiant = trim($_POST['identifiant'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirmation = $_POST['passwordConfirmation'] ?? '';

            // Vérifications de base
            if (empty($identifiant) || empty($email) || empty($password)) {
                echo "Identifiant, email et mot de passe sont requis !";
                return;
            }

            // Vérifier que les mots de passe correspondent
            if ($password !== $passwordConfirmation) {
                echo "Les mots de passe ne correspondent pas !";
                return;
            }

            // Validation de l'email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Format d'email invalide !";
                return;
            }

            // Validation du mot de passe (exemple : minimum 8 caractères)
            if (strlen($password) < 8) {
                echo "Le mot de passe doit contenir au moins 8 caractères !";
                return;
            }

            $db = Database::getInstance()->getConnection();

            // Vérifier si l'identifiant ou l'email existe déjà
            $checkStmt = $db->prepare("SELECT COUNT(*) FROM users WHERE IDENTIFIANT = ? OR EMAIL = ?");
            $checkStmt->bind_param("ss", $identifiant, $email);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $count = $checkResult->fetch_row()[0];

            if ($count > 0) {
                echo "Cet identifiant ou cet email est déjà utilisé !";
                $checkStmt->close();
                return;
            }
            $checkStmt->close();

            // Hachage du mot de passe
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $inscription_date = date("Y-m-d H:i:s");

            // Insertion du nouvel utilisateur (attention au nom de table : "users" pas "user")
            $stmt = $db->prepare("INSERT INTO users (IDENTIFIANT, EMAIL, TELEPHONE, PASSWORD, INSCRIPTION_DATE) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $identifiant, $email, $telephone, $hash, $inscription_date);

            if ($stmt->execute()) {
                echo "Inscription réussie !";
                // Optionnel : connecter automatiquement l'utilisateur
                // session_start();
                // $_SESSION['user_id'] = $db->insert_id;
                // $_SESSION['identifiant'] = $identifiant;
            } else {
                echo "Erreur lors de l'inscription : " . $stmt->error;
            }

            $stmt->close();

        } catch (Exception $e) {
            echo "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}