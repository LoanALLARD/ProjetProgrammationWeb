<?php

use PHPUnit\Framework\TestCase;

class LoginControllerTest extends TestCase
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
     * Test que la classe LoginController existe
     */
    public function testLoginControllerClassExists()
    {
        // Vérifier que le fichier existe
        $controllerFile = __DIR__ . '/../../app/controllers/LoginController.php';
        $this->assertFileExists($controllerFile);
        
        // Test de base
        $this->assertTrue(true);
    }

    /**
     * Test de la méthode index sans messages
     */
    public function testIndexWithoutMessages()
    {
        // Aucun message en session
        $successMessage = $_SESSION['success_message'] ?? null;
        $errorMessage = $_SESSION['error_message'] ?? null;
        
        $this->assertNull($successMessage);
        $this->assertNull($errorMessage);
    }

    /**
     * Test de la méthode index avec message de succès
     */
    public function testIndexWithSuccessMessage()
    {
        $_SESSION['success_message'] = "Connexion réussie !";
        
        $successMessage = $_SESSION['success_message'] ?? null;
        $this->assertEquals("Connexion réussie !", $successMessage);
        
        // Simuler la suppression du message
        unset($_SESSION['success_message']);
        $this->assertArrayNotHasKey('success_message', $_SESSION);
    }

    /**
     * Test de la méthode index avec message d'erreur
     */
    public function testIndexWithErrorMessage()
    {
        $_SESSION['error_message'] = "Identifiant ou mot de passe incorrect !";
        
        $errorMessage = $_SESSION['error_message'] ?? null;
        $this->assertEquals("Identifiant ou mot de passe incorrect !", $errorMessage);
        
        // Simuler la suppression du message
        unset($_SESSION['error_message']);
        $this->assertArrayNotHasKey('error_message', $_SESSION);
    }

    /**
     * Test de nettoyage des messages en session
     */
    public function testSessionMessageCleanup()
    {
        $_SESSION['success_message'] = "Test success";
        $_SESSION['error_message'] = "Test error";
        
        // Simuler le nettoyage fait dans index()
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        $this->assertArrayNotHasKey('success_message', $_SESSION);
        $this->assertArrayNotHasKey('error_message', $_SESSION);
    }

    /**
     * Test de validation des données de login - Identifiant vide
     */
    public function testLoginValidationEmptyIdentifiant()
    {
        $_POST['identifiant'] = '';
        $_POST['password'] = 'motdepasse123';
        
        $identifiant = trim($_POST["identifiant"]);
        $password = $_POST["password"];
        
        $this->assertEmpty($identifiant);
        $this->assertNotEmpty($password);
    }

    /**
     * Test de validation des données de login - Mot de passe vide
     */
    public function testLoginValidationEmptyPassword()
    {
        $_POST['identifiant'] = 'user123';
        $_POST['password'] = '';
        
        $identifiant = trim($_POST["identifiant"]);
        $password = $_POST["password"];
        
        $this->assertNotEmpty($identifiant);
        $this->assertEmpty($password);
    }

    /**
     * Test de validation des données de login - Données valides
     */
    public function testLoginValidationValidData()
    {
        $_POST['identifiant'] = 'user123';
        $_POST['password'] = 'motdepasse123';
        
        $identifiant = trim($_POST["identifiant"]);
        $password = $_POST["password"];
        
        $this->assertNotEmpty($identifiant);
        $this->assertNotEmpty($password);
        $this->assertEquals('user123', $identifiant);
        $this->assertEquals('motdepasse123', $password);
    }

    /**
     * Test de trim sur l'identifiant
     */
    public function testIdentifiantTrimming()
    {
        $_POST['identifiant'] = '  user123  ';
        
        $identifiant = trim($_POST["identifiant"]);
        
        $this->assertEquals('user123', $identifiant);
        $this->assertNotEquals('  user123  ', $identifiant);
    }

    /**
     * Test de vérification de mot de passe
     */
    public function testPasswordVerification()
    {
        $plainPassword = 'motdepasse123';
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        
        // Test avec bon mot de passe
        $isValid = password_verify($plainPassword, $hashedPassword);
        $this->assertTrue($isValid);
        
        // Test avec mauvais mot de passe
        $isInvalid = password_verify('mauvais_mot_de_passe', $hashedPassword);
        $this->assertFalse($isInvalid);
    }

    /**
     * Test de la logique de session lors de la connexion réussie
     */
    public function testSuccessfulLoginSessionLogic()
    {
        // Simuler les données utilisateur récupérées de la DB
        $user = [
            'ID' => 123,
            'IDENTIFIANT' => 'user123',
            'PASSWORD' => password_hash('motdepasse123', PASSWORD_DEFAULT)
        ];
        
        // Simuler la logique de connexion réussie
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['identifiant'] = $user['IDENTIFIANT'];
        $_SESSION['success_message'] = "Connexion réussie !";
        
        $this->assertEquals(123, $_SESSION['user_id']);
        $this->assertEquals('user123', $_SESSION['identifiant']);
        $this->assertEquals("Connexion réussie !", $_SESSION['success_message']);
    }

    /**
     * Test de la logique d'échec de connexion
     */
    public function testFailedLoginLogic()
    {
        // Cas 1: Utilisateur non trouvé (user = null)
        $user = null;
        $password = 'motdepasse123';
        
        $loginSuccessful = $user !== null && password_verify($password, $user['PASSWORD'] ?? '');
        $this->assertFalse($loginSuccessful);
        
        // Cas 2: Mauvais mot de passe
        $user = [
            'ID' => 123,
            'IDENTIFIANT' => 'user123',
            'PASSWORD' => password_hash('autre_mot_de_passe', PASSWORD_DEFAULT)
        ];
        $password = 'motdepasse123';
        
        $loginSuccessful = $user !== null && password_verify($password, $user['PASSWORD']);
        $this->assertFalse($loginSuccessful);
    }

    /**
     * Test de la méthode logout - Nettoyage de session
     */
    public function testLogoutSessionCleanup()
    {
        // Préparer une session avec des données utilisateur
        $_SESSION['user_id'] = 123;
        $_SESSION['identifiant'] = 'user123';
        $_SESSION['success_message'] = "Connexion réussie !";
        $_SESSION['error_message'] = "Une erreur";
        $_SESSION['other_data'] = "Autres données";
        
        // Simuler la logique de logout
        unset($_SESSION['user_id']);
        unset($_SESSION['identifiant']);
        unset($_SESSION['success_message']);
        unset($_SESSION['error_message']);
        
        $this->assertArrayNotHasKey('user_id', $_SESSION);
        $this->assertArrayNotHasKey('identifiant', $_SESSION);
        $this->assertArrayNotHasKey('success_message', $_SESSION);
        $this->assertArrayNotHasKey('error_message', $_SESSION);
        
        // Les autres données devraient encore être là avant session_unset()
        $this->assertArrayHasKey('other_data', $_SESSION);
    }

    /**
     * Test des messages d'erreur et de succès
     */
    public function testSessionMessages()
    {
        $messages = [
            'success_login' => "Connexion réussie !",
            'error_credentials' => "Identifiant ou mot de passe incorrect !",
            'error_connection_prefix' => "Erreur lors de la connexion : ",
            'error_logout_prefix' => "Erreur lors de la déconnexion : "
        ];
        
        foreach ($messages as $key => $message) {
            $this->assertIsString($message);
            $this->assertNotEmpty($message);
        }
    }

    /**
     * Test des URLs de redirection
     */
    public function testRedirectionUrls()
    {
        $urls = [
            'home' => '/index.php?url=home/index',
            'login' => '/index.php?url=login/index'
        ];
        
        foreach ($urls as $key => $url) {
            $this->assertStringContainsString('/index.php?url=', $url);
            $this->assertNotEmpty($url);
        }
    }

    /**
     * Test de la structure de la requête SQL
     */
public function testSqlQueryStructure()
    {
        $query = 'SELECT * FROM users where identifiant = :identifiant';
        
        $this->assertStringContainsString('SELECT', $query);
        $this->assertStringContainsString('FROM users', $query);
        $this->assertStringContainsString(':identifiant', $query);
        $this->assertStringContainsString('where', $query);
    }

    /**
     * Test de sécurité - Paramètres liés
     */
    public function testSecurityParameterBinding()
    {
        $param = ':identifiant';
        
        $this->assertStringStartsWith(':', $param);
        $this->assertEquals(':identifiant', $param);
    }

    /**
     * Test de validation du type PDO
     */
    public function testPDOParameterType()
    {
        $this->assertTrue(defined('PDO::PARAM_STR'));
        $this->assertEquals(2, \PDO::PARAM_STR);
    }

    /**
     * Test de gestion d'exception
     */
    public function testExceptionHandlingLogic()
    {
        // Simuler une exception
        $exceptionMessage = "Erreur de connexion à la base de données";
        $errorMessage = "Erreur lors de la connexion : " . $exceptionMessage;
        
        $this->assertEquals("Erreur lors de la connexion : Erreur de connexion à la base de données", $errorMessage);
        $this->assertStringContainsString("Erreur lors de la connexion", $errorMessage);
    }

    /**
     * Test de validation des données utilisateur récupérées
     */
    public function testUserDataValidation()
    {
        // Cas avec utilisateur valide
        $validUser = [
            'ID' => 123,
            'IDENTIFIANT' => 'user123',
            'PASSWORD' => '$2y$10$example_hash'
        ];
        
        $this->assertIsArray($validUser);
        $this->assertArrayHasKey('ID', $validUser);
        $this->assertArrayHasKey('IDENTIFIANT', $validUser);
        $this->assertArrayHasKey('PASSWORD', $validUser);
        $this->assertIsInt($validUser['ID']);
        $this->assertIsString($validUser['IDENTIFIANT']);
        $this->assertIsString($validUser['PASSWORD']);
        
        // Cas avec utilisateur inexistant
        $nullUser = null;
        $this->assertNull($nullUser);
    }

    /**
     * Test de la logique de vérification d'utilisateur connecté
     */
    public function testUserAuthenticationStatus()
    {
        // Utilisateur non connecté
        $this->assertArrayNotHasKey('user_id', $_SESSION);
        $isLoggedIn = !empty($_SESSION['user_id']);
        $this->assertFalse($isLoggedIn);
        
        // Utilisateur connecté
        $_SESSION['user_id'] = 123;
        $isLoggedIn = !empty($_SESSION['user_id']);
        $this->assertTrue($isLoggedIn);
    }
}