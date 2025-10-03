<?php
namespace controllers;

require_once __DIR__ . '/../core/Database.php';
use core\Database;

class HomeController
{
    public function index() {
        $pageTitle = "Accueil";
        $notes = $this->getAllById(); // récupère les notes
        require __DIR__ . '/../views/home.php';
    }
    
    public function getAllById(){
        session_start();
        //var_dump($_SESSION);
        if (!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            //$identifiant = 10; 
            $db = Database::getInstance()->getConnection();
            $query = $db->prepare('SELECT TITRE, CONTENU FROM notes WHERE USER_ID = :user_id');
            $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
            $query->execute();
            $notes = $query->fetchAll(\PDO::FETCH_ASSOC);

            return $notes;
        }
        return [];
    }
}
