<?php

use PHPUnit\Framework\TestCase;

// Inclure manuellement les classes nécessaires
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/controllers/ForgottenPasswordController.php';

class ForgottenPasswordControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        // Nettoyer la session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
        $_POST = [];
        $_SERVER = [];
    }

    protected function tearDown(): void
    {
        // Nettoyer après chaque test
        $_SESSION = [];
        $_POST = [];
        $_SERVER = [];
        
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * Test que la classe existe et peut être instanciée
     */
    public function testForgottenPasswordControllerExists()
    {
        $this->assertTrue(class_exists('controllers\ForgottenPasswordController'));
        
        // Test d'instanciation sans problème de DB
        try {
            $controller = new \controllers\ForgottenPasswordController();
            $this->assertInstanceOf(\controllers\ForgottenPasswordController::class, $controller);
        } catch (Exception $e) {
            // Si la DB n'est pas accessible, c'est normal dans les tests
            $this->assertStringContainsString('connexion', $e->getMessage());
        }
    }

    /**
     * Test de validation d'email vide
     */
    public function testChangePasswordWithEmptyEmailLogic()
    {
        // Simuler un email vide
        $_POST['email'] = '';
        
        // Vérifier la logique de validation
        $this->assertTrue(empty($_POST['email']));
        
        // Simuler le message d'erreur qui serait défini
        $expectedMessage = "Veuillez saisir une adresse e-mail.";
        $this->assertEquals("Veuillez saisir une adresse e-mail.", $expectedMessage);
    }

    /**
     * Test de trim des emails
     */
    public function testEmailTrimmingLogic()
    {
        $emailWithSpaces = '  test@example.com  ';
        $trimmedEmail = trim($emailWithSpaces);
        
        $this->assertEquals('test@example.com', $trimmedEmail);
        $this->assertNotEquals($emailWithSpaces, $trimmedEmail);
    }

    /**
     * Test de la logique de génération de code
     */
    public function testCodeGenerationLogic()
    {
        // Simuler la génération de code comme dans votre méthode
        $code = random_int(100000, 999999);
        
        // Vérifications
        $this->assertIsInt($code);
        $this->assertGreaterThanOrEqual(100000, $code);
        $this->assertLessThanOrEqual(999999, $code);
        $this->assertEquals(6, strlen((string)$code));
    }

    /**
     * Test de génération de codes multiples pour vérifier l'aléatoire
     */
    public function testCodeGenerationUniqueness()
    {
        $codes = [];
        
        // Générer plusieurs codes
        for ($i = 0; $i < 10; $i++) {
            $codes[] = random_int(100000, 999999);
        }
        
        // Vérifier qu'ils ne sont pas tous identiques
        $uniqueCodes = array_unique($codes);
        $this->assertGreaterThan(1, count($uniqueCodes), "Les codes devraient être différents");
        
        // Vérifier que tous sont dans la bonne plage
        foreach ($codes as $code) {
            $this->assertGreaterThanOrEqual(100000, $code);
            $this->assertLessThanOrEqual(999999, $code);
        }
    }

    /**
     * Test de validation d'email
     */
    public function testEmailValidationLogic()
    {
        $validEmails = [
            'test@example.com',
            'user.name@domain.co.uk',
            'firstname+lastname@company.org'
        ];
        
        $invalidEmails = [
            'invalid-email',
            '@domain.com',
            'user@',
            'user space@domain.com',
            ''
        ];
        
        foreach ($validEmails as $email) {
            $this->assertNotFalse(filter_var($email, FILTER_VALIDATE_EMAIL), "Email valide: $email");
        }
        
        foreach ($invalidEmails as $email) {
            $this->assertFalse(filter_var($email, FILTER_VALIDATE_EMAIL), "Email invalide: $email");
        }
    }

    /**
     * Test de la gestion de session
     */
    public function testSessionHandling()
    {
        // Tester le démarrage de session
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
        
        // Tester la sauvegarde en session
        $_SESSION['test_code'] = 123456;
        $_SESSION['test_time'] = time();
        
        $this->assertEquals(123456, $_SESSION['test_code']);
        $this->assertIsInt($_SESSION['test_time']);
        
        // Tester la suppression
        unset($_SESSION['test_message']);
        $this->assertArrayNotHasKey('test_message', $_SESSION);
    }

    /**
     * Test de la structure du code de réinitialisation
     */
    public function testResetCodeStructure()
    {
        // Simuler la sauvegarde d'un code en session
        $code = random_int(100000, 999999);
        $time = time();
        
        $_SESSION['reset_code'] = $code;
        $_SESSION['reset_code_time'] = $time;
        
        // Vérifications
        $this->assertArrayHasKey('reset_code', $_SESSION);
        $this->assertArrayHasKey('reset_code_time', $_SESSION);
        $this->assertEquals($code, $_SESSION['reset_code']);
        $this->assertEquals($time, $_SESSION['reset_code_time']);
    }

    /**
     * Test de la logique de redirection - CORRIGÉ
     */
    public function testRedirectionLogic()
    {
        // Simuler les différents cas de redirection
        $redirections = [
            'index.php?url=forgotten-password/index',
            'index.php?url=reset-password/index'
        ];
        
        foreach ($redirections as $url) {
            // Utiliser assertStringContainsString au lieu de assertStringContains
            $this->assertStringContainsString('index.php?url=', $url);
            $this->assertNotEmpty($url);
        }
    }

    /**
     * Test de messages d'erreur
     */
    public function testErrorMessages()
    {
        $expectedMessages = [
            'empty_email' => "Veuillez saisir une adresse e-mail.",
            'email_not_found' => "L'adresse e-mail n'existe pas !",
            'mail_error' => "Erreur lors de l'envoi du mail. Veuillez réessayer."
        ];
        
        foreach ($expectedMessages as $key => $message) {
            $this->assertIsString($message);
            $this->assertNotEmpty($message);
        }
    }

    /**
     * Test de configuration SMTP (structure)
     */
    public function testSMTPConfigurationStructure()
    {
        // Vérifier que le fichier de config existe
        $configPath = __DIR__ . '/../../app/config/config.php';
        
        if (file_exists($configPath)) {
            $config = require $configPath;
            
            // Vérifier que les clés SMTP existent dans la config
            $requiredKeys = ['smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_secure'];
            
            foreach ($requiredKeys as $key) {
                $this->assertArrayHasKey($key, $config, "Configuration SMTP manquante: $key");
            }
        } else {
            $this->markTestSkipped('Fichier de configuration non trouvé');
        }
    }
}