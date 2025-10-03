<?php
namespace controllers;

class MentionsLegalesController
{
    public function index()
    {
        // Inclut la vue des mentions légales
        require __DIR__ . '/../views/mentionsLegales.php';
    }
}
