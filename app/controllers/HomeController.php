<?php
namespace controllers;
require_once __DIR__ . '/../core/Database.php';
use core\Database;
class HomeController
{

    public function index() {
        session_start();
        $pageTitle = "Accueil";

        //if(empty($_SESSION['users_id']))
            //require __DIR__.'/../views/login.php';
        //else {
            $notes = $this->getAllById();

            require __DIR__ . '/../views/home.php';
        //}
    }
    
    public function getAllById(){
        if(!empty($_SESSION['identifiant'])){
            //$identifiant = $_SESSION['identifiant'];
            $identifiant = 10;
            $db = Database::getInstance()->getConnection();
            $query = $db->prepare('SELECT TITRE, CONTENU FROM notes WHERE USER_ID = :user_id');
            $query->bindParam(':user_id',$identifiant,\PDO::PARAM_INT);
            $query->execute();
            $notes=$query->fetchAll(\PDO::FETCH_ASSOC);

            return $notes;
            // foreach ($notes as $note) {
            //     echo "<h3>" . htmlspecialchars($note['TITRE']) . "</h3>";
            //     echo "<p>" . nl2br(htmlspecialchars($note['CONTENU'])) . "</p>";
            //     echo "<hr>"; // Ligne de séparation entre les notes
            // }   
        }
        return[];
    }
    // ici je vais appeler ma methode qui affiche toutes les notes 

}