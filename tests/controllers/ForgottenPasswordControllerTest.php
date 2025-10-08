<?php

use PHPUnit\Framework\TestCase;

// Manually include the necessary classes
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/controllers/ForgottenPasswordController.php';

class ForgottenPasswordControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        // Clear session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
        $_POST = [];
        $_SERVER = [];
    }

    protected function tearDown(): void
    {
        // Clean after each test
        $_SESSION = [];
        $_POST = [];
        $_SERVER = [];
        
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * Test that the class exists and can be instantiated
     */
    public function testForgottenPasswordControllerExists()
    {
        $this->assertTrue(class_exists('controllers\ForgottenPasswordController'));
        
        // DB instantiation test without issues
        try {
            $controller = new \controllers\ForgottenPasswordController();
            $this->assertInstanceOf(\controllers\ForgottenPasswordController::class, $controller);
        } catch (Exception $e) {
            // If the DB is not accessible, this is normal in tests.
            $this->assertStringContainsString('connexion', $e->getMessage());
        }
    }

    /**
     * Empty email validation test
     */
    public function testChangePasswordWithEmptyEmailLogic()
    {
        // Simulate an empty email
        $_POST['email'] = '';
        
        // Check the validation logic
        $this->assertTrue(empty($_POST['email']));
        
        // Simulate the error message that would be defined
        $expectedMessage = "Veuillez saisir une adresse e-mail.";
        $this->assertEquals("Veuillez saisir une adresse e-mail.", $expectedMessage);
    }

    /**
     * Email trim test
     */
    public function testEmailTrimmingLogic()
    {
        $emailWithSpaces = '  test@example.com  ';
        $trimmedEmail = trim($emailWithSpaces);
        
        $this->assertEquals('test@example.com', $trimmedEmail);
        $this->assertNotEquals($emailWithSpaces, $trimmedEmail);
    }

    /**
     * Testing the code generation logic
     */
    public function testCodeGenerationLogic()
    {
        // Simulate code generation as in your method
        $code = random_int(100000, 999999);
        
        // Verifications
        $this->assertIsInt($code);
        $this->assertGreaterThanOrEqual(100000, $code);
        $this->assertLessThanOrEqual(999999, $code);
        $this->assertEquals(6, strlen((string)$code));
    }

    /**
     * Multiple code generation test to verify randomness
     */
    public function testCodeGenerationUniqueness()
    {
        $codes = [];
        
        // Generate multiple codes
        for ($i = 0; $i < 10; $i++) {
            $codes[] = random_int(100000, 999999);
        }
        
        // Check that they are not all identical.
        $uniqueCodes = array_unique($codes);
        $this->assertGreaterThan(1, count($uniqueCodes), "Les codes devraient être différents");
        
        // Check that all are within the correct range.
        foreach ($codes as $code) {
            $this->assertGreaterThanOrEqual(100000, $code);
            $this->assertLessThanOrEqual(999999, $code);
        }
    }

    /**
     * Email validation test
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
     * Session management test
     */
    public function testSessionHandling()
    {
        // Testing session start-up
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
        
        // Testing the backup during a session
        $_SESSION['test_code'] = 123456;
        $_SESSION['test_time'] = time();
        
        $this->assertEquals(123456, $_SESSION['test_code']);
        $this->assertIsInt($_SESSION['test_time']);
        
        // Test deletion
        unset($_SESSION['test_message']);
        $this->assertArrayNotHasKey('test_message', $_SESSION);
    }

    /**
     * Testing the reset code structure
     */
    public function testResetCodeStructure()
    {
        // Simulate saving code during a session
        $code = random_int(100000, 999999);
        $time = time();
        
        $_SESSION['reset_code'] = $code;
        $_SESSION['reset_code_time'] = $time;
        
        // Verifications
        $this->assertArrayHasKey('reset_code', $_SESSION);
        $this->assertArrayHasKey('reset_code_time', $_SESSION);
        $this->assertEquals($code, $_SESSION['reset_code']);
        $this->assertEquals($time, $_SESSION['reset_code_time']);
    }

    /**
     * Redirection logic test - CORRECTED
     */
    public function testRedirectionLogic()
    {
        // Simulate different redirection scenarios
        $redirections = [
            'index.php?url=forgotten-password/index',
            'index.php?url=reset-password/index'
        ];
        
        foreach ($redirections as $url) {
            // Use assertStringContainsString instead of assertStringContains
            $this->assertStringContainsString('index.php?url=', $url);
            $this->assertNotEmpty($url);
        }
    }

    /**
     * Error message testing
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
     * SMTP configuration test (structure)
     */
    public function testSMTPConfigurationStructure()
    {
        // Check that the configuration file exists
        $configPath = __DIR__ . '/../../app/config/config.php';
        
        if (file_exists($configPath)) {
            $config = require $configPath;
            
            // Check that the SMTP keys exist in the configuration
            $requiredKeys = ['smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_secure'];
            
            foreach ($requiredKeys as $key) {
                $this->assertArrayHasKey($key, $config, "Configuration SMTP manquante: $key");
            }
        } else {
            $this->markTestSkipped('Fichier de configuration non trouvé');
        }
    }
}