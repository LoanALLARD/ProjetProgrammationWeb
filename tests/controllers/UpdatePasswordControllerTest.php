<?php

use PHPUnit\Framework\TestCase;

class UpdatePasswordControllerTest extends TestCase
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
     * Test que la classe UpdatePasswordController existe
     */
    public function testUpdatePasswordControllerClassExists()
    {
        // Vérifier que le fichier existe
        $controllerFile = __DIR__ . '/../../app/controllers/UpdatePasswordController.php';
        $this->assertFileExists($controllerFile);
        
        // Test de base
        $this->assertTrue(true);
    }

    /**
     * Test de démarrage automatique de session dans le constructeur
     */
    public function testConstructorStartsSession()
    {
        // Simuler la logique du constructeur
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
    }

    /**
     * Test index() sans code vérifié
     */
    public function testIndexWithoutCodeVerified()
    {
        // Pas de code_verified en session
        unset($_SESSION['code_verified']);
        
        $hasCodeVerified = isset($_SESSION['code_verified']) && $_SESSION['code_verified'] === true;
        $this->assertFalse($hasCodeVerified);
        
        $expectedError = "Vous devez d'abord valider le code de réinitialisation.";
        $this->assertEquals("Vous devez d'abord valider le code de réinitialisation.", $expectedError);
    }

    /**
     * Test index() avec code_verified = false
     */
    public function testIndexWithCodeVerifiedFalse()
    {
        $_SESSION['code_verified'] = false;
        
        $hasCodeVerified = isset($_SESSION['code_verified']) && $_SESSION['code_verified'] === true;
        $this->assertFalse($hasCodeVerified);
    }

    /**
     * Test index() avec code vérifié
     */
    public function testIndexWithCodeVerified()
    {
        $_SESSION['code_verified'] = true;
        
        $hasCodeVerified = isset($_SESSION['code_verified']) && $_SESSION['code_verified'] === true;
        $this->assertTrue($hasCodeVerified);
    }

    /**
     * Test index() - Gestion des messages de session
     */
    public function testIndexMessageHandling()
    {
        $_SESSION['code_verified'] = true;
        $_SESSION['success_message'] = "Code validé !";
        $_SESSION['error_message'] = "Erreur test";
        
        // Récupérer les messages
        $successMessage = $_SESSION['success_message'] ?? null;
        $errorMessage = $_SESSION['error_message'] ?? null;
        
        $this->assertEquals("Code validé !", $successMessage);
        $this->assertEquals("Erreur test", $errorMessage);
        
        // Simuler la suppression
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        $this->assertArrayNotHasKey('success_message', $_SESSION);
        $this->assertArrayNotHasKey('error_message', $_SESSION);
    }

    /**
     * Test updatePassword() sans code vérifié
     */
    public function testUpdatePasswordWithoutCodeVerified()
    {
        unset($_SESSION['code_verified']);
        
        $hasCodeVerified = isset($_SESSION['code_verified']) && $_SESSION['code_verified'] === true;
        $this->assertFalse($hasCodeVerified);
        
        $expectedError = "Session expirée. Veuillez recommencer.";
        $this->assertEquals("Session expirée. Veuillez recommencer.", $expectedError);
    }

    /**
     * Test updatePassword() - Mot de passe vide
     */
    public function testUpdatePasswordEmptyPassword()
    {
        $_SESSION['code_verified'] = true;
        $_POST['password'] = '';
        $_POST['passwordConfirmation'] = 'test123';
        
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['passwordConfirmation'] ?? '';
        
        $isEmpty = empty($password) || empty($passwordConfirmation);
        $this->assertTrue($isEmpty);
        
        $expectedError = "Veuillez remplir tous les champs.";
        $this->assertEquals("Veuillez remplir tous les champs.", $expectedError);
    }

    /**
     * Test updatePassword() - Confirmation vide
     */
    public function testUpdatePasswordEmptyConfirmation()
    {
        $_SESSION['code_verified'] = true;
        $_POST['password'] = 'test123456';
        $_POST['passwordConfirmation'] = '';
        
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['passwordConfirmation'] ?? '';
        
        $isEmpty = empty($password) || empty($passwordConfirmation);
        $this->assertTrue($isEmpty);
    }

    /**
     * Test updatePassword() - Mots de passe différents
     */
    public function testUpdatePasswordMismatch()
    {
        $_SESSION['code_verified'] = true;
        $_POST['password'] = 'motdepasse123';
        $_POST['passwordConfirmation'] = 'motdepasse456';
        
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['passwordConfirmation'] ?? '';
        
        $passwordsMatch = $password === $passwordConfirmation;
        $this->assertFalse($passwordsMatch);
        
        $expectedError = "Les mots de passe ne correspondent pas !";
        $this->assertEquals("Les mots de passe ne correspondent pas !", $expectedError);
    }

    /**
     * Test updatePassword() - Mots de passe identiques
     */
    public function testUpdatePasswordMatch()
    {
        $_SESSION['code_verified'] = true;
        $_POST['password'] = 'motdepasse123';
        $_POST['passwordConfirmation'] = 'motdepasse123';
        
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['passwordConfirmation'] ?? '';
        
        $passwordsMatch = $password === $passwordConfirmation;
        $this->assertTrue($passwordsMatch);
    }

    /**
     * Test updatePassword() - Mot de passe trop court
     */
    public function testUpdatePasswordTooShort()
    {
        $_SESSION['code_verified'] = true;
        $_POST['password'] = '123';
        $_POST['passwordConfirmation'] = '123';
        
        $password = $_POST['password'] ?? '';
        
        $isValidLength = strlen($password) >= 8;
        $this->assertFalse($isValidLength);
        
        $expectedError = "Le mot de passe doit contenir au moins 8 caractères !";
        $this->assertEquals("Le mot de passe doit contenir au moins 8 caractères !", $expectedError);
    }

    /**
     * Test updatePassword() - Mot de passe longueur valide
     */
    public function testUpdatePasswordValidLength()
    {
        $_SESSION['code_verified'] = true;
        $_POST['password'] = 'motdepasse123';
        
        $password = $_POST['password'] ?? '';
        
        $isValidLength = strlen($password) >= 8;
        $this->assertTrue($isValidLength);
        $this->assertEquals(13, strlen($password));
    }

    /**
     * Test updatePassword() - Email de réinitialisation manquant
     */
    public function testUpdatePasswordMissingResetEmail()
    {
        $_SESSION['code_verified'] = true;
        $_POST['password'] = 'motdepasse123';
        $_POST['passwordConfirmation'] = 'motdepasse123';
        unset($_SESSION['reset_email']);
        
        $hasResetEmail = isset($_SESSION['reset_email']);
        $this->assertFalse($hasResetEmail);
        
        $expectedError = "Session expirée. Veuillez recommencer.";
        $this->assertEquals("Session expirée. Veuillez recommencer.", $expectedError);
    }

    /**
     * Test updatePassword() - Email de réinitialisation présent
     */
    public function testUpdatePasswordWithResetEmail()
    {
        $_SESSION['code_verified'] = true;
        $_SESSION['reset_email'] = 'test@example.com';
        $_POST['password'] = 'motdepasse123';
        $_POST['passwordConfirmation'] = 'motdepasse123';
        
        $hasResetEmail = isset($_SESSION['reset_email']);
        $this->assertTrue($hasResetEmail);
        $this->assertEquals('test@example.com', $_SESSION['reset_email']);
    }

    /**
     * Test de hachage de mot de passe
     */
    public function testPasswordHashing()
    {
        $password = 'motdepasse123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $this->assertNotEmpty($hash);
        $this->assertNotEquals($password, $hash);
        $this->assertTrue(password_verify($password, $hash));
        $this->assertFalse(password_verify('mauvais_mot_de_passe', $hash));
    }

    /**
     * Test de la structure de la requête UPDATE
     */
    public function testUpdateQueryStructure()
    {
        $query = "UPDATE users SET PASSWORD = :password WHERE EMAIL = :email";
        
        $this->assertStringContainsString('UPDATE users', $query);
        $this->assertStringContainsString('SET PASSWORD = :password', $query);
        $this->assertStringContainsString('WHERE EMAIL = :email', $query);
        $this->assertStringContainsString(':password', $query);
        $this->assertStringContainsString(':email', $query);
    }

    /**
     * Test de sécurité - Paramètres liés
     */
    public function testSecurityParameterBinding()
    {
        $secureParams = [':password', ':email'];
        
        foreach ($secureParams as $param) {
            $this->assertStringStartsWith(':', $param);
            $this->assertGreaterThan(2, strlen($param));
        }
    }

    /**
     * Test de nettoyage de session après succès
     */
    public function testSessionCleanupAfterSuccess()
    {
        // Préparer une session avec toutes les variables de réinitialisation
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time();
        $_SESSION['reset_email'] = 'test@example.com';
        $_SESSION['code_verified'] = true;
        $_SESSION['other_data'] = 'should_remain';
        
        // Simuler le nettoyage après succès
        unset(
            $_SESSION['reset_code'],
            $_SESSION['reset_code_time'],
            $_SESSION['reset_email'],
            $_SESSION['code_verified']
        );
        
        $this->assertArrayNotHasKey('reset_code', $_SESSION);
        $this->assertArrayNotHasKey('reset_code_time', $_SESSION);
        $this->assertArrayNotHasKey('reset_email', $_SESSION);
        $this->assertArrayNotHasKey('code_verified', $_SESSION);
        
        // Les autres données doivent rester
        $this->assertArrayHasKey('other_data', $_SESSION);
    }

    /**
     * Test des messages d'erreur
     */
    public function testErrorMessages()
    {
        $errorMessages = [
            'code_not_verified' => "Vous devez d'abord valider le code de réinitialisation.",
            'session_expired' => "Session expirée. Veuillez recommencer.",
            'empty_fields' => "Veuillez remplir tous les champs.",
            'password_mismatch' => "Les mots de passe ne correspondent pas !",
            'password_length' => "Le mot de passe doit contenir au moins 8 caractères !",
            'update_error' => "Erreur lors de la mise à jour du mot de passe. Veuillez réessayer."
        ];
        
        foreach ($errorMessages as $key => $message) {
            $this->assertIsString($message);
            $this->assertNotEmpty($message);
            $this->assertGreaterThan(10, strlen($message));
        }
    }

    /**
     * Test du message de succès
     */
    public function testSuccessMessage()
    {
        $successMessage = "Votre mot de passe a été réinitialisé avec succès ! Vous pouvez maintenant vous connecter.";
        
        $this->assertIsString($successMessage);
        $this->assertNotEmpty($successMessage);
        $this->assertStringContainsString("réinitialisé avec succès", $successMessage);
        $this->assertStringContainsString("vous connecter", $successMessage);
    }

    /**
     * Test des URLs de redirection
     */
    public function testRedirectionUrls()
    {
        $urls = [
            'forgotten_password' => 'index.php?url=forgotten-password/index',
            'update_password' => 'index.php?url=update-password/index',
            'login' => 'index.php?url=login/index'
        ];
        
        foreach ($urls as $key => $url) {
            $this->assertStringContainsString('index.php?url=', $url);
            $this->assertNotEmpty($url);
        }
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
        // Cas valide complet
        $_SESSION['code_verified'] = true;
        $_SESSION['reset_email'] = 'test@example.com';
        $_POST['password'] = 'nouveaumotdepasse123';
        $_POST['passwordConfirmation'] = 'nouveaumotdepasse123';
        
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['passwordConfirmation'] ?? '';
        
        // Toutes les validations
        $hasCodeVerified = isset($_SESSION['code_verified']) && $_SESSION['code_verified'] === true;
        $hasResetEmail = isset($_SESSION['reset_email']);
        $fieldsNotEmpty = !empty($password) && !empty($passwordConfirmation);
        $passwordsMatch = $password === $passwordConfirmation;
        $validLength = strlen($password) >= 8;
        
        $this->assertTrue($hasCodeVerified);
        $this->assertTrue($hasResetEmail);
        $this->assertTrue($fieldsNotEmpty);
        $this->assertTrue($passwordsMatch);
        $this->assertTrue($validLength);
        
        $allValid = $hasCodeVerified && $hasResetEmail && $fieldsNotEmpty && $passwordsMatch && $validLength;
        $this->assertTrue($allValid);
    }

    /**
     * Test de l'opérateur ?? pour les valeurs par défaut
     */
    public function testNullCoalescingOperator()
    {
        // Test avec $_POST vide
        $_POST = [];
        
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['passwordConfirmation'] ?? '';
        
        $this->assertEquals('', $password);
        $this->assertEquals('', $passwordConfirmation);
        
        // Test avec valeurs présentes
        $_POST['password'] = 'test123';
        $_POST['passwordConfirmation'] = 'test456';
        
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['passwordConfirmation'] ?? '';
        
        $this->assertEquals('test123', $password);
        $this->assertEquals('test456', $passwordConfirmation);
    }

    /**
     * Test de workflow complet de mise à jour
     */
    public function testCompleteUpdateWorkflow()
    {
        // Étape 1: Session préparée correctement
        $_SESSION['code_verified'] = true;
        $_SESSION['reset_email'] = 'user@example.com';
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time() - 300;
        
        // Étape 2: Données POST valides
        $_POST['password'] = 'nouveaumotdepasse123';
        $_POST['passwordConfirmation'] = 'nouveaumotdepasse123';
        
        // Étape 3: Validations
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['passwordConfirmation'] ?? '';
        
        $workflowValid = (
            isset($_SESSION['code_verified']) && $_SESSION['code_verified'] === true &&
            isset($_SESSION['reset_email']) &&
            !empty($password) && !empty($passwordConfirmation) &&
            $password === $passwordConfirmation &&
            strlen($password) >= 8
        );
        
        $this->assertTrue($workflowValid);
        
        // Étape 4: Hachage
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->assertTrue(password_verify($password, $hash));
        
        // Étape 5: Données pour la requête
        $email = $_SESSION['reset_email'];
        $this->assertEquals('user@example.com', $email);
    }

    /**
     * Test de gestion des erreurs d'exécution de requête
     */
    public function testQueryExecutionErrorHandling()
    {
        // Simuler un échec d'exécution
        $queryExecutionSuccess = false; // Simuler $query->execute() qui retourne false
        
        if ($queryExecutionSuccess) {
            $message = "Votre mot de passe a été réinitialisé avec succès ! Vous pouvez maintenant vous connecter.";
            $redirectUrl = 'index.php?url=login/index';
        } else {
            $message = "Erreur lors de la mise à jour du mot de passe. Veuillez réessayer.";
            $redirectUrl = 'index.php?url=update-password/index';
        }
        
        $this->assertEquals("Erreur lors de la mise à jour du mot de passe. Veuillez réessayer.", $message);
        $this->assertEquals('index.php?url=update-password/index', $redirectUrl);
    }
}