<?php

namespace controllers;

class HomeController
{

    public function index() {
        $pageTitle = "Accueil";
        require __DIR__ . '/../views/home.php';
    }

    // ici je vais appeler ma methode qui affiche toutes les notes 

}