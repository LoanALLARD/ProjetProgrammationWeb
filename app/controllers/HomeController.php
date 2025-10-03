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

    protected function getNoteById($note_id){
        if(!empty($_SESSION['user_id'])){
            $user_id = $_SESSION['user_id'];
            $db = Database::getInstance()->getConnection();

            // Vérifier que la note appartient bien à l'utilisateur
            $query = $db->prepare('SELECT TITRE, CONTENU FROM NOTES WHERE ID = :note_id AND USER_ID = :user_id');
            $query->bindParam(':note_id', $note_id, \PDO::PARAM_INT);
            $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
            $query->execute();

            $note = $query->fetch(\PDO::FETCH_ASSOC);

            if($note) {
                echo "<h3>" . htmlspecialchars($note['TITRE']) . "</h3>";
                echo "<p>" . nl2br(htmlspecialchars($note['CONTENU'])) . "</p>";
            }
        }
    }

    public function addNote() {
        if(!empty($_SESSION['user_id']) && !empty($_POST['titre']) && !empty($_POST['contenu'])) {
            $user_id = $_SESSION['user_id']; // Utilisez user_id (l'ID numérique)
            $titre = $_POST['titre'];
            $contenu = $_POST['contenu'];
            $inscription_date = date("Y-m-d");

            $db = Database::getInstance()->getConnection();
            $query = $db->prepare('INSERT INTO notes (USER_ID, TITRE, CONTENU, DATE_CREATION) VALUES (:user_id,:titre,:contenu,:inscription_date)');
            $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT); // PARAM_INT car c'est un ID
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