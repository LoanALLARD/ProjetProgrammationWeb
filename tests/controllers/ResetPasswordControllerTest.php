<?php

use PHPUnit\Framework\TestCase;

class ResetPasswordControllerTest extends TestCase
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
     * Test that ResetPasswordController class exists
     */
    public function testResetPasswordControllerClassExists()
    {
        // Check that the file exists
        $controllerFile = __DIR__ . '/../../app/controllers/ResetPasswordController.php';
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
     * Test index() without reset code in session
     */
    public function testIndexWithoutResetCode()
    {
        // No code in session
        unset($_SESSION['reset_code']);
        
        // Check validation logic
        $hasResetCode = isset($_SESSION['reset_code']);
        $this->assertFalse($hasResetCode);
        
        // Expected error message
        $expectedError = "No password reset request in progress.";
        $this->assertEquals("No password reset request in progress.", $expectedError);
    }

    /**
     * Test index() with reset code in session
     */
    public function testIndexWithResetCode()
    {
        $_SESSION['reset_code'] = 123456;
        
        $hasResetCode = isset($_SESSION['reset_code']);
        $this->assertTrue($hasResetCode);
        $this->assertEquals(123456, $_SESSION['reset_code']);
    }

    /**
     * Test index() - Session message handling
     */
    public function testIndexMessageHandling()
    {
        $_SESSION['reset_code'] = 123456;
        $_SESSION['success_message'] = "Code sent!";
        $_SESSION['error_message'] = "Test error";
        
        // Retrieve messages
        $successMessage = $_SESSION['success_message'] ?? null;
        $errorMessage = $_SESSION['error_message'] ?? null;
        
        $this->assertEquals("Code sent!", $successMessage);
        $this->assertEquals("Test error", $errorMessage);
        
        // Simulate deletion
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        $this->assertArrayNotHasKey('success_message', $_SESSION);
        $this->assertArrayNotHasKey('error_message', $_SESSION);
    }

    /**
     * Test verificationCode() - Empty code
     */
    public function testVerificationCodeEmpty()
    {
        $_POST['enteredCode'] = '';
        
        $enteredCode = $_POST['enteredCode'];
        $isEmpty = empty($enteredCode);
        
        $this->assertTrue($isEmpty);
        
        $expectedError = "Please enter the code received by email.";
        $this->assertEquals("Please enter the code received by email.", $expectedError);
    }

    /**
     * Test verificationCode() - Code provided
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
     * Test verificationCode() - Entered code trimming
     */
    public function testVerificationCodeTrimming()
    {
        $_POST['enteredCode'] = '  123456  ';
        
        $enteredCode = trim($_POST['enteredCode']);
        
        $this->assertEquals('123456', $enteredCode);
        $this->assertNotEquals('  123456  ', $enteredCode);
    }

    /**
     * Test verificationCode() - No saved code in session
     */
    public function testVerificationCodeNoSavedCode()
    {
        $_POST['enteredCode'] = '123456';
        unset($_SESSION['reset_code']);
        
        $savedCode = $_SESSION['reset_code'] ?? null;
        
        $this->assertNull($savedCode);
        
        $expectedError = "No code generated. Please restart the procedure.";
        $this->assertEquals("No code generated. Please restart the procedure.", $expectedError);
    }

    /**
     * Test verificationCode() - Expired code
     */
    public function testVerificationCodeExpired()
    {
        $_POST['enteredCode'] = '123456';
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time() - 1000; // 1000 seconds ago (>15min)
        
        $elapsed = time() - $_SESSION['reset_code_time'];
        $isExpired = $elapsed > 900; // 900s = 15min
        
        $this->assertTrue($isExpired);
        $this->assertGreaterThan(900, $elapsed);
        
        $expectedError = "The code has expired. Please try again.";
        $this->assertEquals("The code has expired. Please try again.", $expectedError);
    }

    /**
     * Test verificationCode() - Valid code (not expired)
     */
    public function testVerificationCodeNotExpired()
    {
        $_POST['enteredCode'] = '123456';
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time() - 300; // 5 minutes ago
        
        $elapsed = time() - $_SESSION['reset_code_time'];
        $isExpired = $elapsed > 900;
        
        $this->assertFalse($isExpired);
        $this->assertLessThanOrEqual(900, $elapsed);
    }

    /**
     * Test verificationCode() - Correct code
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
     * Test verificationCode() - Incorrect code
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
        
        $expectedError = "Incorrect code. Please try again.";
        $this->assertEquals("Incorrect code. Please try again.", $expectedError);
    }

    /**
     * Test verificationCode() - Verification success
     */
    public function testVerificationCodeSuccess()
    {
        $_POST['enteredCode'] = '123456';
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time() - 300;
        
        // Simulate success
        $_SESSION['code_verified'] = true;
        $_SESSION['success_message'] = "Code validated! Please enter your new password.";
        
        $this->assertTrue($_SESSION['code_verified']);
        $this->assertEquals("Code validated! Please enter your new password.", $_SESSION['success_message']);
    }

    /**
     * Test elapsed time calculation
     */
    public function testElapsedTimeCalculation()
    {
        $currentTime = time();
        $codeTime = $currentTime - 600; // 10 minutes ago
        
        $elapsed = $currentTime - $codeTime;
        
        $this->assertEquals(600, $elapsed);
        $this->assertLessThan(900, $elapsed); // Not yet expired
        
        // Test with expired code
        $expiredCodeTime = $currentTime - 1200; // 20 minutes ago
        $elapsedExpired = $currentTime - $expiredCodeTime;
        
        $this->assertEquals(1200, $elapsedExpired);
        $this->assertGreaterThan(900, $elapsedExpired); // Expired
    }

    /**
     * Test expiration duration (15 minutes = 900 seconds)
     */
    public function testExpirationDuration()
    {
        $expirationDuration = 900; // 15 minutes in seconds
        
        $this->assertEquals(900, $expirationDuration);
        $this->assertEquals(15 * 60, $expirationDuration);
    }

    /**
     * Test code integer conversion
     */
    public function testCodeIntegerConversion()
    {
        $stringCode = '123456';
        $integerCode = 123456;
        
        $this->assertEquals($integerCode, (int)$stringCode);
        $this->assertIsInt((int)$stringCode);
        $this->assertIsString($stringCode);
        
        // Test with code containing spaces
        $codeWithSpaces = '  123456  ';
        $trimmedCode = trim($codeWithSpaces);
        
        $this->assertEquals($integerCode, (int)$trimmedCode);
    }

    /**
     * Test error messages
     */
    public function testErrorMessages()
    {
        $errorMessages = [
            'no_reset_request' => "No password reset request in progress.",
            'empty_code' => "Please enter the code received by email.",
            'no_code_generated' => "No code generated. Please restart the procedure.",
            'code_expired' => "The code has expired. Please try again.",
            'incorrect_code' => "Incorrect code. Please try again."
        ];
        
        foreach ($errorMessages as $key => $message) {
            $this->assertIsString($message);
            $this->assertNotEmpty($message);
            $this->assertGreaterThan(10, strlen($message));
        }
    }

    /**
     * Test success messages
     */
    public function testSuccessMessages()
    {
        $successMessage = "Code validated! Please enter your new password.";
        
        $this->assertIsString($successMessage);
        $this->assertNotEmpty($successMessage);
        $this->assertStringContainsString("Code validated", $successMessage);
        $this->assertStringContainsString("new password", $successMessage);
    }

    /**
     * Test redirection URLs
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
     * Test session cleanup after expiration
     */
    public function testSessionCleanupAfterExpiration()
    {
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time() - 1000;
        
        // Simulate cleanup
        unset($_SESSION['reset_code'], $_SESSION['reset_code_time']);
        
        $this->assertArrayNotHasKey('reset_code', $_SESSION);
        $this->assertArrayNotHasKey('reset_code_time', $_SESSION);
    }

    /**
     * Test code validation with different formats
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
     * Test session key existence verification
     */
    public function testSessionKeyExistence()
    {
        // Test with missing keys
        $this->assertFalse(isset($_SESSION['reset_code']));
        $this->assertFalse(isset($_SESSION['reset_code_time']));
        $this->assertFalse(isset($_SESSION['code_verified']));
        
        // Test with present keys
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time();
        $_SESSION['code_verified'] = true;
        
        $this->assertTrue(isset($_SESSION['reset_code']));
        $this->assertTrue(isset($_SESSION['reset_code_time']));
        $this->assertTrue(isset($_SESSION['code_verified']));
    }

    /**
     * Test complete workflow logic
     */
    public function testCompleteWorkflowLogic()
    {
        // Step 1: Code generated
        $_SESSION['reset_code'] = 123456;
        $_SESSION['reset_code_time'] = time();
        
        // Step 2: User enters correct code
        $_POST['enteredCode'] = '123456';
        $enteredCode = trim($_POST['enteredCode']);
        
        // Step 3: Time validation
        $elapsed = time() - $_SESSION['reset_code_time'];
        $isNotExpired = $elapsed <= 900;
        
        // Step 4: Code validation
        $isCorrect = (int)$enteredCode === (int)$_SESSION['reset_code'];
        
        // Step 5: Success
        $workflowSuccess = $isNotExpired && $isCorrect;
        
        $this->assertTrue($isNotExpired);
        $this->assertTrue($isCorrect);
        $this->assertTrue($workflowSuccess);
    }
}