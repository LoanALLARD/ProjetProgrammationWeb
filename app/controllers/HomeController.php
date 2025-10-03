<?php

namespace controllers;
require_once __DIR__ . '/../core/Database.php';
use core\Database;
class HomeController
{

    public function index() {
        $pageTitle = "Accueil";

        //if(empty($_SESSION['email']))
           //require __DIR__.'/../views/login.php';
        //else{
        //}

        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $db = Database::getInstance()->getConnection();

            $query = $db->prepare('SELECT titre, contenu FROM notes WHERE USER_ID = :user_id');
            $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
            $query->execute();

            $notes = $query->fetchAll(\PDO::FETCH_ASSOC);
        }

        require __DIR__ . '/../views/home.php';
    }

    public function addNote() {
        if(!empty($_SESSION['user_id']) && !empty($_POST['titre']) && !empty($_POST['contenu'])) {
            $user_id = $_SESSION['user_id'];
            $titre = $_POST['titre'];
            $contenu = $_POST['contenu'];
            $inscription_date = date("Y-m-d");

            $db = Database::getInstance()->getConnection();
            $query = $db->prepare('INSERT INTO notes (USER_ID, TITRE, CONTENU, DATE_CREATION) VALUES (:user_id,:titre,:contenu,:inscription_date)');
            $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
            $query->bindParam(':titre', $titre, \PDO::PARAM_STR);
            $query->bindParam(':contenu', $contenu, \PDO::PARAM_STR);
            $query->bindParam(':inscription_date', $inscription_date, \PDO::PARAM_STR);

            $query->execute();

            header('Location: index.php?url=home/index');
            exit;
        } else {
            echo "Erreur : vous devez être connecté et remplir tous les champs.";
        }
    }

    public function modifyNote() {
    
    }

    public function deleteNote(){
        if(!empty($_SESSION['user_id'])){
            $user_id = $_SESSION['user_id'];
            $db = Database::getInstance()->getConnection();
            $query = $db->prepare('DELETE FROM NOTES WHERE ID = :id AND USER_ID = :user_id');
            $query->bindParam(':id', $_POST['id'], \PDO::PARAM_INT);
            $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
            $query->execute();

            header('Location: index.php?url=home/index');
            exit;
        }
    }
}