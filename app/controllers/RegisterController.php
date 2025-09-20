<?php

namespace controllers;

class RegisterController
{

    public function index() {
        require __DIR__ . '/../views/register.php';
    }

    public function register() {
        $db = \Database::getInstance()->getConnection();

        $identifiant = $_POST['identifiant'] ?? '';
        $email = $_POST['email'] ?? '';
        $telephone = $_POST['telephone'] ?? '';
        $password = $_POST['password'] ?? '';

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $inscription_date = date("Y-m-d");

        $stmt = $db->prepare("INSERT INTO user (IDENTIFIANT, EMAIL, TELEPHONE, PASSWORD, INSCRIPTION_DATE) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $identifiant, $email, $telephone, $hash, $inscription_date);

        if ($stmt->execute()) {
            echo "Inscription réussie !";
        } else {
            echo "Erreur lors de l'inscription : " . $stmt->error;
        }
    }

}