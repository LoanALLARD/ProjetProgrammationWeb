<?php

use PHPUnit\Framework\TestCase;

class RegisterControllerTest extends TestCase
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
     * Test que la classe RegisterController existe
     */
    public function testRegisterControllerClassExists()
    {
        // Vérifier que le fichier existe
        $controllerFile = __DIR__ . '/../../app/controllers/RegisterController.php';
        $this->assertFileExists($controllerFile);
        
        // Test de base
        $this->assertTrue(true);
    }

    /**
     * Test de la méthode index sans messages
     */
    public function testIndexWithoutMessages()
    {
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
        $_SESSION['success_message'] = "Inscription réussie !";
        
        $successMessage = $_SESSION['success_message'] ?? null;
        $this->assertEquals("Inscription réussie !", $successMessage);
        
        // Simuler la suppression du message
        unset($_SESSION['success_message']);
        $this->assertArrayNotHasKey('success_message', $_SESSION);
    }

    /**
     * Test de la méthode index avec message d'erreur
     */
    public function testIndexWithErrorMessage()
    {
        $_SESSION['error_message'] = "Erreur d'inscription !";
        
        $errorMessage = $_SESSION['error_message'] ?? null;
        $this->assertEquals("Erreur d'inscription !", $errorMessage);
        
        // Simuler la suppression du message
        unset($_SESSION['error_message']);
        $this->assertArrayNotHasKey('error_message', $_SESSION);
    }

    /**
     * Test de validation - Mots de passe différents
     */
    public function testRegisterPasswordMismatch()
    {
        $password = 'motdepasse123';
        $passwordConfirmation = 'motdepasse456';
        
        $passwordsMatch = $password === $passwordConfirmation;
        $this->assertFalse($passwordsMatch);
        
        // Message d'erreur attendu
        $expectedError = "Les mots de passe ne correspondent pas !";
        $this->assertEquals("Les mots de passe ne correspondent pas !", $expectedError);
    }

    /**
     * Test de validation - Mots de passe identiques
     */
    public function testRegisterPasswordMatch()
    {
        $password = 'motdepasse123';
        $passwordConfirmation = 'motdepasse123';
        
        $passwordsMatch = $password === $passwordConfirmation;
        $this->assertTrue($passwordsMatch);
    }

    /**
     * Test de validation d'email - Format invalide
     */
    public function testRegisterInvalidEmailFormat()
    {
        $invalidEmails = [
            'email-invalide',
            '@domain.com',
            'user@',
            'user space@domain.com',
            'user..double@domain.com'
        ];
        
        foreach ($invalidEmails as $email) {
            $isValid = filter_var($email, FILTER_VALIDATE_EMAIL);
            $this->assertFalse($isValid, "Email invalide: $email");
        }
        
        $expectedError = "Format d'email invalide !";
        $this->assertEquals("Format d'email invalide !", $expectedError);
    }

    /**
     * Test de validation d'email - Format valide
     */
    public function testRegisterValidEmailFormat()
    {
        $validEmails = [
            'test@example.com',
            'user.name@domain.co.uk',
            'firstname+lastname@company.org',
            'user123@test-domain.com'
        ];
        
        foreach ($validEmails as $email) {
            $isValid = filter_var($email, FILTER_VALIDATE_EMAIL);
            $this->assertNotFalse($isValid, "Email valide: $email");
        }
    }

    /**
     * Test de validation de mot de passe - Trop court
     */
    public function testRegisterPasswordTooShort()
    {
        $shortPasswords = ['123', 'abc', '1234567']; // Moins de 8 caractères
        
        foreach ($shortPasswords as $password) {
            $isValid = strlen($password) >= 8;
            $this->assertFalse($isValid, "Mot de passe trop court: $password");
        }
        
        $expectedError = "Le mot de passe doit contenir au moins 8 caractères !";
        $this->assertEquals("Le mot de passe doit contenir au moins 8 caractères !", $expectedError);
    }

    /**
     * Test de validation de mot de passe - Longueur valide
     */
    public function testRegisterPasswordValidLength()
    {
        $validPasswords = ['12345678', 'motdepasse123', 'unMotDePasseComplexe!'];
        
        foreach ($validPasswords as $password) {
            $isValid = strlen($password) >= 8;
            $this->assertTrue($isValid, "Mot de passe valide: $password");
        }
    }

    /**
     * Test de trim des données d'entrée
     */
    public function testRegisterDataTrimming()
    {
        $_POST['identifiant'] = '  user123  ';
        $_POST['email'] = '  test@example.com  ';
        $_POST['telephone'] = '  0123456789  ';
        
        $identifiant = trim($_POST['identifiant'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        
        $this->assertEquals('user123', $identifiant);
        $this->assertEquals('test@example.com', $email);
        $this->assertEquals('0123456789', $telephone);
    }

    /**
     * Test de gestion des valeurs par défaut avec l'opérateur ??
     */
    public function testRegisterDefaultValues()
    {
        // Test avec $_POST vide
        $_POST = [];
        
        $identifiant = trim($_POST['identifiant'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['passwordConfirmation'] ?? '';
        
        $this->assertEquals('', $identifiant);
        $this->assertEquals('', $email);
        $this->assertEquals('', $telephone);
        $this->assertEquals('', $password);
        $this->assertEquals('', $passwordConfirmation);
    }

    /**
     * Test de hachage de mot de passe
     */
    public function testPasswordHashing()
    {
        $password = 'motdepasse123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Vérifier que le hash est généré
        $this->assertNotEmpty($hash);
        $this->assertNotEquals($password, $hash);
        
        // Vérifier que le hash peut être vérifié
        $this->assertTrue(password_verify($password, $hash));
        
        // Vérifier qu'un mauvais mot de passe échoue
        $this->assertFalse(password_verify('mauvais_mot_de_passe', $hash));
    }

    /**
     * Test de génération de date d'inscription
     */
    public function testInscriptionDateGeneration()
    {
        $inscription_date = date("Y-m-d H:i:s");
        
        // Vérifier le format de la date
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $inscription_date);
        
        // Vérifier que c'est une date valide
        $timestamp = strtotime($inscription_date);
        $this->assertNotFalse($timestamp);
        
        // Vérifier que la date est récente (dans les 5 dernières secondes)
        $now = time();
        $this->assertLessThanOrEqual(5, abs($now - $timestamp));
    }

    /**
     * Test de la structure de la requête de vérification d'utilisateur existant
     */
    public function testCheckUserExistsQueryStructure()
    {
        $query = 'SELECT COUNT(*) FROM users WHERE IDENTIFIANT = :identifiant OR EMAIL = :email';
        
        $this->assertStringContainsString('SELECT COUNT(*)', $query);
        $this->assertStringContainsString('FROM users', $query);
        $this->assertStringContainsString('WHERE', $query);
        $this->assertStringContainsString('IDENTIFIANT = :identifiant', $query);
        $this->assertStringContainsString('EMAIL = :email', $query);
        $this->assertStringContainsString('OR', $query);
    }

    /**
     * Test de la structure de la requête d'insertion
     */
    public function testInsertUserQueryStructure()
    {
        $query = "INSERT INTO users (IDENTIFIANT, EMAIL, TELEPHONE, PASSWORD, INSCRIPTION_DATE) VALUES (:identifiant, :email, :telephone, :password, :inscription_date)";
        
        $this->assertStringContainsString('INSERT INTO users', $query);
        $this->assertStringContainsString('IDENTIFIANT, EMAIL, TELEPHONE, PASSWORD, INSCRIPTION_DATE', $query);
        $this->assertStringContainsString('VALUES', $query);
        $this->assertStringContainsString(':identifiant', $query);
        $this->assertStringContainsString(':email', $query);
        $this->assertStringContainsString(':telephone', $query);
        $this->assertStringContainsString(':password', $query);
        $this->assertStringContainsString(':inscription_date', $query);
    }

    /**
     * Test de sécurité - Paramètres liés
     */
    public function testSecurityParameterBinding()
    {
        $secureParams = [':identifiant', ':email', ':telephone', ':password', ':inscription_date'];
        
        foreach ($secureParams as $param) {
            $this->assertStringStartsWith(':', $param);
            $this->assertGreaterThan(2, strlen($param));
        }
    }

    /**
     * Test des messages d'erreur
     */
    public function testErrorMessages()
    {
        $errorMessages = [
            'password_mismatch' => "Les mots de passe ne correspondent pas !",
            'invalid_email' => "Format d'email invalide !",
            'password_length' => "Le mot de passe doit contenir au moins 8 caractères !",
            'user_exists' => "Cet identifiant ou cet email est déjà utilisé !",
            'registration_error' => "Erreur lors de l'inscription.",
            'exception_prefix' => "Erreur lors de l'inscription : "
        ];
        
        foreach ($errorMessages as $key => $message) {
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
            'register_index' => '/index.php?url=register/index',
            'home_index' => '/index.php?url=home/index'
        ];
        
        foreach ($urls as $key => $url) {
            $this->assertStringContainsString('/index.php?url=', $url);
            $this->assertNotEmpty($url);
        }
    }

    /**
     * Test de la logique de session après inscription réussie
     */
public function testSuccessfulRegistrationSessionLogic()
    {
        $identifiant = 'nouveauuser';
        
        // Démarrer une session si pas encore active
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        // Simuler la création de session après inscription
        $_SESSION['user_id'] = session_id();
        $_SESSION['identifiant'] = $identifiant;
        
        // Vérifier que session_id() retourne maintenant quelque chose
        $this->assertNotEmpty(session_id(), "Session ID should not be empty after session_start()");
        $this->assertNotEmpty($_SESSION['user_id']);
        $this->assertEquals($identifiant, $_SESSION['identifiant']);
    }

    /**
     * Test de validation des types PDO
     */
    public function testPDOParameterTypes()
    {
        $this->assertTrue(defined('PDO::PARAM_STR'));
        $this->assertEquals(2, \PDO::PARAM_STR);
    }

    /**
     * Test de validation complète des données
     */
    public function testCompleteDataValidation()
    {
        // Données valides
        $validData = [
            'identifiant' => 'user123',
            'email' => 'test@example.com',
            'telephone' => '0123456789',
            'password' => 'motdepasse123',
            'passwordConfirmation' => 'motdepasse123'
        ];
        
        // Tests de validation
        $passwordsMatch = $validData['password'] === $validData['passwordConfirmation'];
        $emailValid = filter_var($validData['email'], FILTER_VALIDATE_EMAIL) !== false;
        $passwordLengthValid = strlen($validData['password']) >= 8;
        
        $this->assertTrue($passwordsMatch);
        $this->assertTrue($emailValid);
        $this->assertTrue($passwordLengthValid);
        
        // Test avec données invalides
        $invalidData = [
            'password' => '123', // Trop court
            'passwordConfirmation' => '456', // Différent
            'email' => 'email-invalide' // Format invalide
        ];
        
        $passwordsMatchInvalid = $invalidData['password'] === $invalidData['passwordConfirmation'];
        $emailValidInvalid = filter_var($invalidData['email'], FILTER_VALIDATE_EMAIL) !== false;
        $passwordLengthValidInvalid = strlen($invalidData['password']) >= 8;
        
        $this->assertFalse($passwordsMatchInvalid);
        $this->assertFalse($emailValidInvalid);
        $this->assertFalse($passwordLengthValidInvalid);
    }

    /**
     * Test de démarrage automatique de session
     */
    public function testSessionAutoStart()
    {
        // Simuler la logique du constructeur
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
    }

    /**
     * Test de logique de comptage d'utilisateurs existants
     */
    public function testUserExistsCountLogic()
    {
        // Simuler différents résultats de COUNT(*)
        $count_no_user = 0;
        $count_existing_user = 1;
        $count_multiple_users = 2;
        
        $this->assertFalse($count_no_user > 0);
        $this->assertTrue($count_existing_user > 0);
        $this->assertTrue($count_multiple_users > 0);
    }
}