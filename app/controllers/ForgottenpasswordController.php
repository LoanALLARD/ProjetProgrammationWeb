<?php

namespace controllers;

class ForgottenpasswordController
{

    public function index() {
        $pageTitle = "Mot de passe oublié";
        require __DIR__ . '/../views/forgottenPassword.php';
    }

}