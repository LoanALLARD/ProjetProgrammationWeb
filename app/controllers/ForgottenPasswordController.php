<?php

namespace controllers;

class ForgottenPasswordController
{

    public function index() {
        $pageTitle = "Mot de passe oublié";
        require __DIR__ . '/../views/forgottenPassword.php';
    }

}