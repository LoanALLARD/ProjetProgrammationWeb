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

            /*if (empty($identifiant) || empty($email) || empty($password)) {
                $_SESSION['error'] = "Identifiant, email et mot de passe sont requis !";
                header("Location: /index.php?url=register/index");
                exit;
            }*/

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

            $checkStmt = $db->prepare("SELECT COUNT(*) FROM users WHERE IDENTIFIANT = ? OR EMAIL = ?");
            $checkStmt->bind_param("ss", $identifiant, $email);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $count = $checkResult->fetch_row()[0];

            if ($count > 0) {
                $_SESSION['error'] = "Cet identifiant ou cet email est déjà utilisé !";
                $checkStmt->close();
                header("Location: /index.php?url=register/index");
                exit;
            }
            $checkStmt->close();

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $inscription_date = date("Y-m-d H:i:s");

            $stmt = $db->prepare("INSERT INTO users (IDENTIFIANT, EMAIL, TELEPHONE, PASSWORD, INSCRIPTION_DATE) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $identifiant, $email, $telephone, $hash, $inscription_date);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Inscription réussie !";
                $_SESSION['user_id'] = $db->insert_id;
                $_SESSION['identifiant'] = $identifiant;
                header("Location: /index.php?url=register/index");
                exit;
            } else {
                $_SESSION['error'] = "Erreur lors de l'inscription : " . $stmt->error;
                header("Location: /index.php?url=register/index");
                exit;
            }

            $stmt->close();

        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur lors de l'inscription : " . $e->getMessage();
            header("Location: /index.php?url=register/index");
            exit;
        }
    }
}