<?php

namespace controllers;
require_once __DIR__ . '/../core/Database.php';
use core\Database;
class HomeController
{

    public function index() {
        $pageTitle = "Accueil";

        if(empty($_SESSION['email']))
            require __DIR__.'/../views/login.php';
        else{
        require __DIR__ . '/../views/home.php';
        }
    }
    
    protected function getAllById(){
        if(!empty($_SESSION['identifiant'])){
            $identifiant = $_SESSION['identifiant'];
            $db = Database::getInstance()->getConnection();
            $query = $db->prepare('SELECT TITRE,CONTENU FROM NOTES WHERE ID ==' . 10);
            $query->bindParam(':identifiant',$identifiant,\PDO::PARAM::STR);
            $query->execute();
            $notes->$query->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($notes as $note) {
                echo "<h3>" . htmlspecialchars($note['TITRE']) . "</h3>";
                echo "<p>" . nl2br(htmlspecialchars($note['CONTENU'])) . "</p>";
                echo "<hr>"; // Ligne de séparation entre les notes
            }   
        }
    }
    // ici je vais appeler ma methode qui affiche toutes les notes 

}