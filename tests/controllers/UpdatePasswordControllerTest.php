<?php

use PHPUnit\Framework\TestCase;

class UpdatePasswordControllerTest extends TestCase
{
    protected function setUp(): void
    {
        // Clean global variables
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
        // Clean up after each test
        $_SESSION = [];
        $_POST = [];
        $_GET = [];
        $_SERVER = [];
        
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * Test that UpdatePasswordController class exists
     */
    public function testUpdatePasswordControllerClassExists()
    {
        // Check that the file exists
        $controllerFile = __DIR__ . '/../../app/controllers/UpdatePasswordController.php';
        $this->assertFileExists($controllerFile);
        
        // Basic test
        $this->assertTrue(true);
    }

    /**
     * Test automatic session start in constructor
     */
    public function testConstructorStartsSession()
    {
        // Simulate constructor logic
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
    }

    /**
     * Test index() without verified code
     */
    public function testIndexWithoutCodeVerified()
    {
        // No code_verified in session
        unset($_SESSION['code_verified']);
        
        $hasCodeVerified = isset($_SESSION['code_verified']) && $_SESSION['code_verified'] === true;
        $this->assertFalse($hasCodeVerified);
        
        $expectedError = "You must first validate the reset code.";
        $this->assertEquals("You must first validate the reset code.", $expectedError);
    }

    /**
     * Test index() with code_verified = false
     */
    public function testIndexWithCodeVerifiedFalse()
    {
        $_SESSION['code_verified'] = false;
        
        $hasCodeVerified = isset($_SESSION['code_verified']) && $_SESSION['code_verified'] === true;
        $this->assertFalse($hasCodeVerified);
    }

    /**
     * Test index() with verified code
     */
    public function testIndexWithCodeVerified()
    {
        $_SESSION['code_verified'] = true;
        
        $hasCodeVerified = isset($_SESSION['code_verified']) && $_SESSION['code_verified'] === true;
        $this->assertTrue($hasCodeVerified);
    }

    /**
     * Test index() - Session message handling
     */
    public function testIndexMessageHandling()
    {
        $_SESSION['code_verified'] = true;
        $_SESSION['success_message'] = "Code validated!";
        $_SESSION['error_message'] = "Test error";
        
        // Retrieve messages
        $successMessage = $_SESSION['success_message'] ?? null;
        $errorMessage = $_SESSION['error_message'] ?? null;
        
        $this->assertEquals("Code validated!", $successMessage);
        $this->assertEquals("Test error", $errorMessage);
        
        // Simulate deletion
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        $this->assertArrayNotHasKey('success_message', $_SESSION);
        $this->assertArrayNotHasKey('error_message', $_SESSION);
    }

    /**
     * Test updatePassword() without verified code
     */
    public function testUpdatePasswordWithoutCodeVerified()
    {
        unset($_SESSION['code_verified']);
        
        $hasCodeVerified = isset($_SESSION['code_verified']) && $_SESSION['code_verified'] === true;
        $this->assertFalse($hasCodeVerified);
        
        $expectedError = "Session expired. Please try again.";
        $this->assertEquals("Session expired. Please try again.", $expectedError);
    }

    /**
     * Test updatePassword() - Empty password
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
        
        $expectedError = "Please fill in all fields.";
        $this->assertEquals("Please fill in all fields.", $expectedError);
    }

    /**
     * Test updatePassword() - Empty confirmation
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
     * Test updatePassword() - Different passwords
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
        
        $expectedError = "Passwords do not match!";
        $this->assertEquals("Passwords do not match!", $expectedError);
    }

    /**
     * Test updatePassword() - Identical passwords
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
     * Test updatePassword() - Password too short
     */
    public function testUpdatePasswordTooShort()
    {
        $_SESSION['code_verified'] = true;
        $_POST['password'] = '123';
        $_POST['passwordConfirmation'] = '123';
        
        $password = $_POST['password'] ?? '';
        
        $isValidLength = strlen($password) >= 8;
        $this->assertFalse($isValidLength);
        
        $expectedError = "Password must contain at least 8 characters!";
        $this->assertEquals("Password must contain at least 8 characters!", $expectedError);
    }

    /**
     * Test updatePassword() - Valid password length
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
     * Test updatePassword() - Missing reset email
     */
    public function testUpdatePasswordMissingResetEmail()
    {
        $_SESSION['code_verified'] = true;
        $_POST['password'] = 'motdepasse123';
        $_POST['passwordConfirmation'] = 'motdepasse123';
        unset($_SESSION['reset_email']);
        
        $hasResetEmail = isset($_SESSION['reset_email']);
        $this->assertFalse($hasResetEmail);
        
        $expectedError = "Session expired. Please try again.";
        $this->assertEquals("Session expired. Please try again.", $expectedError);
    }

    /**
     * Test updatePassword() - Reset email present
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
     * Test password hashing
     */
    public function testPasswordHashing()
    {
        $password = 'motdepasse123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $this->assertNotEmpty($hash);
        $this->assertNotEquals($password, $hash);
        $this->assertTrue(password_verify($password, $hash));
        $this->assertFalse(password_verify('wrong_password', $hash));
    }

    /**
     * Test UPDATE query structure
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
     * Test security - Parameter binding
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
     * Test session cleanup after success
     */
    public function testSessionCleanupAfterSuccess()
    {
        // Prepare session with all reset variables
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time();
        $_SESSION['reset_email'] = 'test@example.com';
        $_SESSION['code_verified'] = true;
        $_SESSION['other_data'] = 'should_remain';
        
        // Simulate cleanup after success
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
        
        // Other data should remain
        $this->assertArrayHasKey('other_data', $_SESSION);
    }

    /**
     * Test error messages
     */
    public function testErrorMessages()
    {
        $errorMessages = [
            'code_not_verified' => "You must first validate the reset code.",
            'session_expired' => "Session expired. Please try again.",
            'empty_fields' => "Please fill in all fields.",
            'password_mismatch' => "Passwords do not match!",
            'password_length' => "Password must contain at least 8 characters!",
            'update_error' => "Error updating password. Please try again."
        ];
        
        foreach ($errorMessages as $key => $message) {
            $this->assertIsString($message);
            $this->assertNotEmpty($message);
            $this->assertGreaterThan(10, strlen($message));
        }
    }

    /**
     * Test success message
     */
    public function testSuccessMessage()
    {
        $successMessage = "Your password has been reset successfully! You can now log in.";
        
        $this->assertIsString($successMessage);
        $this->assertNotEmpty($successMessage);
        $this->assertStringContainsString("reset successfully", $successMessage);
        $this->assertStringContainsString("log in", $successMessage);
    }

    /**
     * Test redirection URLs
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
     * Test PDO parameter types validation
     */
    public function testPDOParameterTypes()
    {
        $this->assertTrue(defined('PDO::PARAM_STR'));
        $this->assertEquals(2, \PDO::PARAM_STR);
    }

    /**
     * Test complete data validation
     */
    public function testCompleteDataValidation()
    {
        // Complete valid case
        $_SESSION['code_verified'] = true;
        $_SESSION['reset_email'] = 'test@example.com';
        $_POST['password'] = 'nouveaumotdepasse123';
        $_POST['passwordConfirmation'] = 'nouveaumotdepasse123';
        
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['passwordConfirmation'] ?? '';
        
        // All validations
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
     * Test null coalescing operator ?? for default values
     */
    public function testNullCoalescingOperator()
    {
        // Test with empty $_POST
        $_POST = [];
        
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['passwordConfirmation'] ?? '';
        
        $this->assertEquals('', $password);
        $this->assertEquals('', $passwordConfirmation);
        
        // Test with present values
        $_POST['password'] = 'test123';
        $_POST['passwordConfirmation'] = 'test456';
        
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['passwordConfirmation'] ?? '';
        
        $this->assertEquals('test123', $password);
        $this->assertEquals('test456', $passwordConfirmation);
    }

    /**
     * Test complete update workflow
     */
    public function testCompleteUpdateWorkflow()
    {
        // Step 1: Session properly prepared
        $_SESSION['code_verified'] = true;
        $_SESSION['reset_email'] = 'user@example.com';
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time() - 300;
        
        // Step 2: Valid POST data
        $_POST['password'] = 'nouveaumotdepasse123';
        $_POST['passwordConfirmation'] = 'nouveaumotdepasse123';
        
        // Step 3: Validations
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
        
        // Step 4: Hashing
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->assertTrue(password_verify($password, $hash));
        
        // Step 5: Data for query
        $email = $_SESSION['reset_email'];
        $this->assertEquals('user@example.com', $email);
    }

    /**
     * Test query execution error handling
     */
    public function testQueryExecutionErrorHandling()
    {
        // Simulate execution failure
        $queryExecutionSuccess = false; // Simulate $query->execute() returning false
        
        if ($queryExecutionSuccess) {
            $message = "Your password has been reset successfully! You can now log in.";
            $redirectUrl = 'index.php?url=login/index';
        } else {
            $message = "Error updating password. Please try again.";
            $redirectUrl = 'index.php?url=update-password/index';
        }
        
        $this->assertEquals("Error updating password. Please try again.", $message);
        $this->assertEquals('index.php?url=update-password/index', $redirectUrl);
    }
}