<?php

namespace controllers;

class LoginController
{

    public function index() {
        require __DIR__ . '/../views/login.php';
    }

    public function login() {
        $db = \Database::getInstance()->getConnection();

        $identifiant = $_POST["identifiant"];
        $password = $_POST["password"];

        $stmt = $db->prepare("SELECT * FROM users WHERE IDENTIFIANT = $identifiant AND PASSWORD = $password");
        $stmt->execute();
        echo $stmt.db2_bind_param($identifiant);

//        if ($stmt->execute()) {
//            if ($identifiant == .... && password_verify($password, ....)) {
//                echo "Connexion réussie !";
//            } else {
//                echo "Identifiant ou mot de passe incorrect !";
//            }
//        } else {
//            echo "Erreur lors de la conenxion : " . $stmt->error;
//        }
    }

}