<?php

namespace controllers;

class ForgottenpasswordController
{

    public function index() {
        $pageTitle = "Mot de passe oublié";
        require __DIR__ . '/../views/forgottenPassword.php';
    }

    public function changePassword() {
        $email = $_POST['email'];

        if(isset($_POST['email'])) {
            $message = "Bojour, voici votre nouveau mot de passe : ";
            $headers = 'Content-type: text/html; charset=UTF-8';

            if(mail($_POST['email'], 'Mot de passe oublié', $message, $headers)) {
                echo 'Mail envoyé';
            } else {
                echo 'Une erreur est survenue lors de l\'envoi du mail';
            }
        }
    }

}