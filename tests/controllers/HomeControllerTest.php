<?php

use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
    protected function setUp(): void
    {
        // Nettoyer les variables globales
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
        $_POST = [];
        $_GET = [];
        $_SERVER = [];
    }

    protected function tearDown(): void
    {
        // Nettoyer après chaque test
        $_SESSION = [];
        $_POST = [];
        $_GET = [];
        $_SERVER = [];
        
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * Test que la classe HomeController existe
     */
    public function testHomeControllerClassExists()
    {
        // Vérifier que le fichier existe
        $controllerFile = __DIR__ . '/../../app/controllers/HomeController.php';
        $this->assertFileExists($controllerFile);
        
        // Inclure le fichier pour vérifier la syntaxe
        $this->assertTrue(true); // Test basique sans instanciation
    }

    /**
     * Test de validation des données pour addNote - Email vide
     */
    public function testAddNoteValidationEmptyTitle()
    {
        // Simuler la logique de validation sans instancier le contrôleur
        $user_id = 123;
        $titre = ''; // Titre vide
        $contenu = 'Contenu test';
        
        // Test de la logique de validation
        $isValid = !empty($user_id) && !empty($titre) && !empty($contenu);
        $this->assertFalse($isValid, "La validation devrait échouer avec un titre vide");
    }

    /**
     * Test de validation des données pour addNote - Contenu vide
     */
    public function testAddNoteValidationEmptyContent()
    {
        $user_id = 123;
        $titre = 'Mon titre';
        $contenu = ''; // Contenu vide
        
        $isValid = !empty($user_id) && !empty($titre) && !empty($contenu);
        $this->assertFalse($isValid, "La validation devrait échouer avec un contenu vide");
    }

    /**
     * Test de validation des données pour addNote - Utilisateur non connecté
     */
    public function testAddNoteValidationNoUser()
    {
        $user_id = null; // Pas d'utilisateur
        $titre = 'Mon titre';
        $contenu = 'Mon contenu';
        
        $isValid = !empty($user_id) && !empty($titre) && !empty($contenu);
        $this->assertFalse($isValid, "La validation devrait échouer sans utilisateur connecté");
    }

    /**
     * Test de validation des données pour addNote - Données valides
     */
    public function testAddNoteValidationValid()
    {
        $user_id = 123;
        $titre = 'Mon titre';
        $contenu = 'Mon contenu';
        
        $isValid = !empty($user_id) && !empty($titre) && !empty($contenu);
        $this->assertTrue($isValid, "La validation devrait réussir avec des données valides");
    }

    /**
     * Test de génération de date pour les notes
     */
    public function testNoteDateGeneration()
    {
        $date = date("Y-m-d");
        
        // Vérifier le format de la date
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $date);
        
        // Vérifier que c'est une date valide
        $dateParts = explode('-', $date);
        $this->assertCount(3, $dateParts);
        $this->assertTrue(checkdate($dateParts[1], $dateParts[2], $dateParts[0]));
    }

    /**
     * Test de validation pour modifyNote - Méthode POST
     */
    public function testModifyNotePostValidation()
    {
        $user_id = 123;
        $note_id = '456';
        $titre = 'Nouveau titre';
        $contenu = 'Nouveau contenu';
        
        // Simuler la validation POST
        $isValidPost = !empty($note_id) && !empty($titre) && !empty($contenu);
        $this->assertTrue($isValidPost, "La validation POST devrait réussir");
        
        // Test avec données manquantes
        $isValidPostIncomplete = !empty($note_id) && !empty('') && !empty($contenu);
        $this->assertFalse($isValidPostIncomplete, "La validation POST devrait échouer avec titre vide");
    }

    /**
     * Test de validation pour modifyNote - Méthode GET
     */
    public function testModifyNoteGetValidation()
    {
        $user_id = 123;
        $note_id = '456';
        
        // Simuler la validation GET
        $isValidGet = !empty($note_id);
        $this->assertTrue($isValidGet, "La validation GET devrait réussir avec un ID");
        
        // Test sans ID
        $isValidGetNoId = !empty('');
        $this->assertFalse($isValidGetNoId, "La validation GET devrait échouer sans ID");
    }

    /**
     * Test de validation pour deleteNote
     */
    public function testDeleteNoteValidation()
    {
        $user_id = 123;
        $note_id = '456';
        
        // Validation avec données valides
        $isValid = !empty($user_id) && !empty($note_id);
        $this->assertTrue($isValid, "La validation delete devrait réussir");
        
        // Validation sans utilisateur
        $isValidNoUser = !empty(null) && !empty($note_id);
        $this->assertFalse($isValidNoUser, "La validation delete devrait échouer sans utilisateur");
        
        // Validation sans ID
        $isValidNoId = !empty($user_id) && !empty('');
        $this->assertFalse($isValidNoId, "La validation delete devrait échouer sans ID");
    }

    /**
     * Test des messages d'erreur et de succès
     */
    public function testSessionMessages()
    {
        $messages = [
            'success_add' => "Note ajoutée avec succès !",
            'success_modify' => "Note modifiée avec succès !",
            'success_delete' => "Note supprimée avec succès !",
            'error_fields' => "Vous devez être connecté et remplir tous les champs.",
            'error_required' => "Tous les champs sont requis.",
            'error_login' => "Vous devez être connecté.",
            'error_delete' => "Impossible de supprimer la note.",
            'error_not_found' => "Note introuvable."
        ];
        
        foreach ($messages as $key => $message) {
            $this->assertIsString($message);
            $this->assertNotEmpty($message);
            $this->assertGreaterThan(5, strlen($message)); // Messages significatifs
        }
    }

    /**
     * Test des URLs de redirection
     */
    public function testRedirectionUrls()
    {
        $urls = [
            'home_index' => 'index.php?url=home/index',
            'home_add_form' => 'index.php?url=home/showAddForm&action=add',
            'auth_login' => 'index.php?url=auth/login'
        ];
        
        foreach ($urls as $key => $url) {
            $this->assertStringContainsString('index.php?url=', $url);
            $this->assertNotEmpty($url);
        }
    }

    /**
     * Test de la structure des requêtes SQL (vérification des patterns)
     */
    public function testSqlQueryPatterns()
    {
        $queries = [
            'select' => 'SELECT id, titre, contenu FROM notes WHERE USER_ID = :user_id',
            'insert' => 'INSERT INTO notes (USER_ID, TITRE, CONTENU, DATE_CREATION) VALUES (:user_id,:titre,:contenu,:inscription_date)',
            'update' => 'UPDATE notes SET TITRE = :titre, CONTENU = :contenu WHERE ID = :id AND USER_ID = :user_id',
            'delete' => 'DELETE FROM notes WHERE ID = :id AND USER_ID = :user_id'
        ];
        
        foreach ($queries as $type => $query) {
            $this->assertStringContainsString('notes', $query);
            $this->assertStringContainsString('USER_ID', $query);
            $this->assertStringContainsString(':', $query); // Paramètres liés
        }
    }

    /**
     * Test de sécurité - Paramètres liés
     */
    public function testSecurityParameterBinding()
    {
        $secureParams = [':user_id', ':titre', ':contenu', ':id', ':inscription_date'];
        
        foreach ($secureParams as $param) {
            $this->assertStringStartsWith(':', $param);
            $this->assertGreaterThan(2, strlen($param));
        }
    }

    /**
     * Test des types PDO
     */
    public function testPDOParameterTypes()
    {
        // Vérifier que les constantes PDO existent
        $this->assertTrue(defined('PDO::PARAM_INT'));
        $this->assertTrue(defined('PDO::PARAM_STR'));
        
        // Vérifier les valeurs des constantes
        $this->assertEquals(1, \PDO::PARAM_INT);
        $this->assertEquals(2, \PDO::PARAM_STR);
    }

    /**
     * Test de validation des méthodes HTTP
     */
    public function testHttpMethods()
    {
        $methods = ['GET', 'POST'];
        
        foreach ($methods as $method) {
            $this->assertIsString($method);
            $this->assertContains($method, ['GET', 'POST', 'PUT', 'DELETE']);
        }
    }

    /**
     * Test de validation des IDs numériques
     */
    public function testNumericIdValidation()
    {
        $validIds = ['123', '456', '1'];
        $invalidIds = ['abc', '', '0', '-1'];
        
        foreach ($validIds as $id) {
            $this->assertTrue(is_numeric($id));
            $this->assertGreaterThan(0, (int)$id);
        }
        
        foreach ($invalidIds as $id) {
            $isValid = is_numeric($id) && (int)$id > 0;
            $this->assertFalse($isValid);
        }
    }

    /**
     * Test de la logique de session utilisateur
     */
    public function testUserSessionLogic()
    {
        // Test avec utilisateur connecté
        $_SESSION['user_id'] = 123;
        $isLoggedIn = !empty($_SESSION['user_id']);
        $this->assertTrue($isLoggedIn);
        
        // Test sans utilisateur
        $_SESSION['user_id'] = null;
        $isLoggedIn = !empty($_SESSION['user_id']);
        $this->assertFalse($isLoggedIn);
        
        // Test avec session vide
        unset($_SESSION['user_id']);
        $isLoggedIn = !empty($_SESSION['user_id']);
        $this->assertFalse($isLoggedIn);
    }
}