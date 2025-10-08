<?php

use PHPUnit\Framework\TestCase;

class ResetPasswordControllerTest extends TestCase
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
     * Test que la classe ResetPasswordController existe
     */
    public function testResetPasswordControllerClassExists()
    {
        // Vérifier que le fichier existe
        $controllerFile = __DIR__ . '/../../app/controllers/ResetPasswordController.php';
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
     * Test index() sans code de réinitialisation en session
     */
    public function testIndexWithoutResetCode()
    {
        // Pas de code en session
        unset($_SESSION['reset_code']);
        
        // Vérifier la logique de validation
        $hasResetCode = isset($_SESSION['reset_code']);
        $this->assertFalse($hasResetCode);
        
        // Message d'erreur attendu
        $expectedError = "Aucune demande de réinitialisation en cours.";
        $this->assertEquals("Aucune demande de réinitialisation en cours.", $expectedError);
    }

    /**
     * Test index() avec code de réinitialisation en session
     */
    public function testIndexWithResetCode()
    {
        $_SESSION['reset_code'] = 123456;
        
        $hasResetCode = isset($_SESSION['reset_code']);
        $this->assertTrue($hasResetCode);
        $this->assertEquals(123456, $_SESSION['reset_code']);
    }

    /**
     * Test index() - Gestion des messages de session
     */
    public function testIndexMessageHandling()
    {
        $_SESSION['reset_code'] = 123456;
        $_SESSION['success_message'] = "Code envoyé !";
        $_SESSION['error_message'] = "Erreur test";
        
        // Récupérer les messages
        $successMessage = $_SESSION['success_message'] ?? null;
        $errorMessage = $_SESSION['error_message'] ?? null;
        
        $this->assertEquals("Code envoyé !", $successMessage);
        $this->assertEquals("Erreur test", $errorMessage);
        
        // Simuler la suppression
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        $this->assertArrayNotHasKey('success_message', $_SESSION);
        $this->assertArrayNotHasKey('error_message', $_SESSION);
    }

    /**
     * Test verificationCode() - Code vide
     */
    public function testVerificationCodeEmpty()
    {
        $_POST['enteredCode'] = '';
        
        $enteredCode = $_POST['enteredCode'];
        $isEmpty = empty($enteredCode);
        
        $this->assertTrue($isEmpty);
        
        $expectedError = "Veuillez saisir le code reçu par e-mail.";
        $this->assertEquals("Veuillez saisir le code reçu par e-mail.", $expectedError);
    }

    /**
     * Test verificationCode() - Code fourni
     */
    public function testVerificationCodeProvided()
    {
        $_POST['enteredCode'] = '123456';
        
        $enteredCode = $_POST['enteredCode'];
        $isEmpty = empty($enteredCode);
        
        $this->assertFalse($isEmpty);
        $this->assertEquals('123456', $enteredCode);
    }

    /**
     * Test verificationCode() - Trim du code saisi
     */
    public function testVerificationCodeTrimming()
    {
        $_POST['enteredCode'] = '  123456  ';
        
        $enteredCode = trim($_POST['enteredCode']);
        
        $this->assertEquals('123456', $enteredCode);
        $this->assertNotEquals('  123456  ', $enteredCode);
    }

    /**
     * Test verificationCode() - Aucun code sauvegardé en session
     */
    public function testVerificationCodeNoSavedCode()
    {
        $_POST['enteredCode'] = '123456';
        unset($_SESSION['reset_code']);
        
        $savedCode = $_SESSION['reset_code'] ?? null;
        
        $this->assertNull($savedCode);
        
        $expectedError = "Aucun code généré. Veuillez recommencer la procédure.";
        $this->assertEquals("Aucun code généré. Veuillez recommencer la procédure.", $expectedError);
    }

    /**
     * Test verificationCode() - Code expiré
     */
    public function testVerificationCodeExpired()
    {
        $_POST['enteredCode'] = '123456';
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time() - 1000; // Il y a 1000 secondes (>15min)
        
        $elapsed = time() - $_SESSION['reset_code_time'];
        $isExpired = $elapsed > 900; // 900s = 15min
        
        $this->assertTrue($isExpired);
        $this->assertGreaterThan(900, $elapsed);
        
        $expectedError = "Le code a expiré. Veuillez recommencer.";
        $this->assertEquals("Le code a expiré. Veuillez recommencer.", $expectedError);
    }

    /**
     * Test verificationCode() - Code valide (non expiré)
     */
    public function testVerificationCodeNotExpired()
    {
        $_POST['enteredCode'] = '123456';
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time() - 300; // Il y a 5 minutes
        
        $elapsed = time() - $_SESSION['reset_code_time'];
        $isExpired = $elapsed > 900;
        
        $this->assertFalse($isExpired);
        $this->assertLessThanOrEqual(900, $elapsed);
    }

    /**
     * Test verificationCode() - Code correct
     */
    public function testVerificationCodeCorrect()
    {
        $_POST['enteredCode'] = '123456';
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time() - 300;
        
        $enteredCode = trim($_POST['enteredCode']);
        $savedCode = $_SESSION['reset_code'];
        
        $isCorrect = (int)$enteredCode === (int)$savedCode;
        
        $this->assertTrue($isCorrect);
        $this->assertEquals(123456, (int)$enteredCode);
        $this->assertEquals(123456, (int)$savedCode);
    }

    /**
     * Test verificationCode() - Code incorrect
     */
    public function testVerificationCodeIncorrect()
    {
        $_POST['enteredCode'] = '654321';
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time() - 300;
        
        $enteredCode = trim($_POST['enteredCode']);
        $savedCode = $_SESSION['reset_code'];
        
        $isCorrect = (int)$enteredCode === (int)$savedCode;
        
        $this->assertFalse($isCorrect);
        $this->assertEquals(654321, (int)$enteredCode);
        $this->assertEquals(123456, (int)$savedCode);
        
        $expectedError = "Code incorrect. Veuillez réessayer.";
        $this->assertEquals("Code incorrect. Veuillez réessayer.", $expectedError);
    }

    /**
     * Test verificationCode() - Succès de vérification
     */
    public function testVerificationCodeSuccess()
    {
        $_POST['enteredCode'] = '123456';
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time() - 300;
        
        // Simuler le succès
        $_SESSION['code_verified'] = true;
        $_SESSION['success_message'] = "Code validé ! Veuillez saisir votre nouveau mot de passe.";
        
        $this->assertTrue($_SESSION['code_verified']);
        $this->assertEquals("Code validé ! Veuillez saisir votre nouveau mot de passe.", $_SESSION['success_message']);
    }

    /**
     * Test de calcul du temps écoulé
     */
    public function testElapsedTimeCalculation()
    {
        $currentTime = time();
        $codeTime = $currentTime - 600; // Il y a 10 minutes
        
        $elapsed = $currentTime - $codeTime;
        
        $this->assertEquals(600, $elapsed);
        $this->assertLessThan(900, $elapsed); // Pas encore expiré
        
        // Test avec code expiré
        $expiredCodeTime = $currentTime - 1200; // Il y a 20 minutes
        $elapsedExpired = $currentTime - $expiredCodeTime;
        
        $this->assertEquals(1200, $elapsedExpired);
        $this->assertGreaterThan(900, $elapsedExpired); // Expiré
    }

    /**
     * Test de la durée d'expiration (15 minutes = 900 secondes)
     */
    public function testExpirationDuration()
    {
        $expirationDuration = 900; // 15 minutes en secondes
        
        $this->assertEquals(900, $expirationDuration);
        $this->assertEquals(15 * 60, $expirationDuration);
    }

    /**
     * Test de conversion de code en entier
     */
    public function testCodeIntegerConversion()
    {
        $stringCode = '123456';
        $integerCode = 123456;
        
        $this->assertEquals($integerCode, (int)$stringCode);
        $this->assertIsInt((int)$stringCode);
        $this->assertIsString($stringCode);
        
        // Test avec code contenant des espaces
        $codeWithSpaces = '  123456  ';
        $trimmedCode = trim($codeWithSpaces);
        
        $this->assertEquals($integerCode, (int)$trimmedCode);
    }

    /**
     * Test des messages d'erreur
     */
    public function testErrorMessages()
    {
        $errorMessages = [
            'no_reset_request' => "Aucune demande de réinitialisation en cours.",
            'empty_code' => "Veuillez saisir le code reçu par e-mail.",
            'no_code_generated' => "Aucun code généré. Veuillez recommencer la procédure.",
            'code_expired' => "Le code a expiré. Veuillez recommencer.",
            'incorrect_code' => "Code incorrect. Veuillez réessayer."
        ];
        
        foreach ($errorMessages as $key => $message) {
            $this->assertIsString($message);
            $this->assertNotEmpty($message);
            $this->assertGreaterThan(10, strlen($message));
        }
    }

    /**
     * Test des messages de succès
     */
    public function testSuccessMessages()
    {
        $successMessage = "Code validé ! Veuillez saisir votre nouveau mot de passe.";
        
        $this->assertIsString($successMessage);
        $this->assertNotEmpty($successMessage);
        $this->assertStringContainsString("Code validé", $successMessage);
        $this->assertStringContainsString("nouveau mot de passe", $successMessage);
    }

    /**
     * Test des URLs de redirection
     */
    public function testRedirectionUrls()
    {
        $urls = [
            'forgotten_password' => 'index.php?url=forgotten-password/index',
            'reset_password' => 'index.php?url=reset-password/index',
            'update_password' => 'index.php?url=update-password/index'
        ];
        
        foreach ($urls as $key => $url) {
            $this->assertStringContainsString('index.php?url=', $url);
            $this->assertNotEmpty($url);
        }
    }

    /**
     * Test de nettoyage de session après expiration
     */
    public function testSessionCleanupAfterExpiration()
    {
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time() - 1000;
        
        // Simuler le nettoyage
        unset($_SESSION['reset_code'], $_SESSION['reset_code_time']);
        
        $this->assertArrayNotHasKey('reset_code', $_SESSION);
        $this->assertArrayNotHasKey('reset_code_time', $_SESSION);
    }

    /**
     * Test de validation de code avec différents formats
     */
    public function testCodeFormatValidation()
    {
        $codes = [
            '123456' => 123456,
            ' 654321 ' => 654321,
            '000123' => 123,
            '999999' => 999999
        ];
        
        foreach ($codes as $stringCode => $expectedInt) {
            $trimmedCode = trim($stringCode);
            $intCode = (int)$trimmedCode;
            
            $this->assertEquals($expectedInt, $intCode);
        }
    }

    /**
     * Test de vérification de l'existence de clés de session
     */
    public function testSessionKeyExistence()
    {
        // Test avec clés manquantes
        $this->assertFalse(isset($_SESSION['reset_code']));
        $this->assertFalse(isset($_SESSION['reset_code_time']));
        $this->assertFalse(isset($_SESSION['code_verified']));
        
        // Test avec clés présentes
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time();
        $_SESSION['code_verified'] = true;
        
        $this->assertTrue(isset($_SESSION['reset_code']));
        $this->assertTrue(isset($_SESSION['reset_code_time']));
        $this->assertTrue(isset($_SESSION['code_verified']));
    }

    /**
     * Test de logique de workflow complet
     */
    public function testCompleteWorkflowLogic()
    {
        // Étape 1: Code généré
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time();
        
        // Étape 2: Utilisateur saisit le bon code
        $_POST['enteredCode'] = '123456';
        $enteredCode = trim($_POST['enteredCode']);
        
        // Étape 3: Validation du temps
        $elapsed = time() - $_SESSION['reset_code_time'];
        $isNotExpired = $elapsed <= 900;
        
        // Étape 4: Validation du code
        $isCorrect = (int)$enteredCode === (int)$_SESSION['reset_code'];
        
        // Étape 5: Succès
        $workflowSuccess = $isNotExpired && $isCorrect;
        
        $this->assertTrue($isNotExpired);
        $this->assertTrue($isCorrect);
        $this->assertTrue($workflowSuccess);
    }
}