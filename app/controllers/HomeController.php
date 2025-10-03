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
        require __DIR__ . '/../views/home.php';
        //}
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

    public function addNote() {
        if(!empty($_SESSION['identifiant']) && !empty($_POST['titre']) && !empty($_POST['contenu'])) {
            $identifiant = $_SESSION['user_id'];
            $titre = $_POST['titre'];
            $contenu = $_POST['contenu'];
            $inscription_date = date("Y-m-d H:i:s");
    
            $db = Database::getInstance()->getConnection();
            $query = $db->prepare('INSERT INTO NOTES (USER_ID,TITRE,CONTENU,DATE_CREATION) VALUES (:user_id,:titre,:contenu,:inscription_date)');
            $query->bindParam(':user_id', $identifiant, \PDO::PARAM_STR);
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
    
    



    public function showAddForm() {
        $showForm = false;
        if (isset($_GET['action']) && $_GET['action'] === 'add') {
            $showForm = true;
        }
        require __DIR__ . '/../views/home.php';
    }

    public function deleteNote(){
        if(!empty($_SESSION['identifiant'])){
            $identifiant = $_SESSION['identifiant'];
            $db = Database::getInstance()->getConnection();
            $query = $db->prepare('DELETE FROM NOTES WHERE ID = :id AND USER_ID = :user_id');
            $query->bindParam(':id',$_POST['id'],\PDO::PARAM::INT);
            $query->bindParam(':user_id',$identifiant,\PDO::PARAM::STR);
            $query->execute();            
        }
    }


    // ici je vais appeler ma methode qui affiche toutes les notes 

}