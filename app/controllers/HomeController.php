<?php

namespace controllers;
require_once __DIR__ . '/../core/Database.php';
use core\Database;
class HomeController
{
    //recovered the information of the user in the database
    public function index() {
        $pageTitle = "Accueil";
        $showForm = false;

        //ask the database if the sessions is not initialized
        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $db = Database::getInstance()->getConnection();

            $query = $db->prepare('SELECT id, titre, contenu FROM notes WHERE USER_ID = :user_id');
            $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
            $query->execute();

            $notes = $query->fetchAll(\PDO::FETCH_ASSOC);
        }

        require __DIR__ . '/../views/home.php';
    }

    //recovered the information of the user in the database + the form to add a new note
    public function showAddForm() {
        $pageTitle = "Accueil";
        $showForm = true;

        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $db = Database::getInstance()->getConnection();

            $query = $db->prepare('SELECT id, titre, contenu FROM notes WHERE USER_ID = :user_id');
            $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
            $query->execute();

            $notes = $query->fetchAll(\PDO::FETCH_ASSOC);
        }

        require __DIR__ . '/../views/home.php';
    }

    // form to create a new note in the database (create)
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

            $_SESSION['success'] = "Note ajoutée avec succès !";
            header('Location: index.php?url=home/index');
            exit;
        } else {
            $_SESSION['error'] = "Vous devez être connecté et remplir tous les champs.";
            header("Location: index.php?url=home/showAddForm&action=add");
            exit;
        }
    }

    // form to modify a note in the database (update)
    public function modifyNote() {
        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];

            if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['id'])) {
                $note_id = $_GET['id'];
                $db = Database::getInstance()->getConnection();

                $query = $db->prepare('SELECT id, titre, contenu FROM notes WHERE ID = :id AND USER_ID = :user_id');
                $query->bindParam(':id', $note_id, \PDO::PARAM_INT);
                $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
                $query->execute();

                $noteToEdit = $query->fetch(\PDO::FETCH_ASSOC);

                if($noteToEdit) {
                    $pageTitle = "Modifier une note";
                    $showEditForm = true;

                    // Récupérer toutes les notes pour les afficher aussi
                    $query = $db->prepare('SELECT id, titre, contenu FROM notes WHERE USER_ID = :user_id');
                    $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
                    $query->execute();
                    $notes = $query->fetchAll(\PDO::FETCH_ASSOC);

                    require __DIR__ . '/../views/home.php';
                    return;
                } else {
                    $_SESSION['error'] = "Note introuvable.";
                    header('Location: index.php?url=home/index');
                    exit;
                }
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id']) && !empty($_POST['titre']) && !empty($_POST['contenu'])) {
                $note_id = $_POST['id'];
                $titre = $_POST['titre'];
                $contenu = $_POST['contenu'];

                $db = Database::getInstance()->getConnection();

                $query = $db->prepare('UPDATE notes SET TITRE = :titre, CONTENU = :contenu WHERE ID = :id AND USER_ID = :user_id');
                $query->bindParam(':titre', $titre, \PDO::PARAM_STR);
                $query->bindParam(':contenu', $contenu, \PDO::PARAM_STR);
                $query->bindParam(':id', $note_id, \PDO::PARAM_INT);
                $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);

                $query->execute();

                $_SESSION['success'] = "Note modifiée avec succès !";
                header('Location: index.php?url=home/index');
                exit;
            } else {
                $_SESSION['error'] = "Tous les champs sont requis.";
                header('Location: index.php?url=home/index');
                exit;
            }
        } else {
            $_SESSION['error'] = "Vous devez être connecté.";
            header('Location: index.php?url=auth/login');
            exit;
        }
    }

    // form to delete a note in the database (delete)
    public function deleteNote(){
        if(!empty($_SESSION['user_id']) && !empty($_POST['id'])){
            $user_id = $_SESSION['user_id'];
            $note_id = $_POST['id'];

            $db = Database::getInstance()->getConnection();
            $query = $db->prepare('DELETE FROM notes WHERE ID = :id AND USER_ID = :user_id');
            $query->bindParam(':id', $note_id, \PDO::PARAM_INT);
            $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
            $query->execute();

            $_SESSION['success'] = "Note supprimée avec succès !";
            header('Location: index.php?url=home/index');
            exit;
        } else {
            $_SESSION['error'] = "Impossible de supprimer la note.";
            header('Location: index.php?url=home/index');
            exit;
        }
    }
}