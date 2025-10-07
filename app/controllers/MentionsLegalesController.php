<?php
namespace controllers;

class MentionsLegalesController
{
    public function index()
    {
        // Include the view of legal mentions
        require __DIR__ . '/../views/mentionsLegales.php';
    }
}
