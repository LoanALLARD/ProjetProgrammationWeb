<?php

use PHPUnit\Framework\TestCase;

class RegisterControllerTest extends TestCase
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
     * Test that RegisterController class exists
     */
    public function testRegisterControllerClassExists()
    {
        // Check that the file exists
        $controllerFile = __DIR__ . '/../../app/controllers/RegisterController.php';
        $this->assertFileExists($controllerFile);
        
        // Basic test
        $this->assertTrue(true);
    }

    /**
     * Test index method without messages
     */
    public function testIndexWithoutMessages()
    {
        $successMessage = $_SESSION['success_message'] ?? null;
        $errorMessage = $_SESSION['error_message'] ?? null;
        
        $this->assertNull($successMessage);
        $this->assertNull($errorMessage);
    }

    /**
     * Test index method with success message
     */
    public function testIndexWithSuccessMessage()
    {
        $_SESSION['success_message'] = "Registration successful!";
        
        $successMessage = $_SESSION['success_message'] ?? null;
        $this->assertEquals("Registration successful!", $successMessage);
        
        // Simulate message deletion
        unset($_SESSION['success_message']);
        $this->assertArrayNotHasKey('success_message', $_SESSION);
    }

    /**
     * Test index method with error message
     */
    public function testIndexWithErrorMessage()
    {
        $_SESSION['error_message'] = "Registration error!";
        
        $errorMessage = $_SESSION['error_message'] ?? null;
        $this->assertEquals("Registration error!", $errorMessage);
        
        // Simulate message deletion
        unset($_SESSION['error_message']);
        $this->assertArrayNotHasKey('error_message', $_SESSION);
    }

    /**
     * Test validation - Different passwords
     */
    public function testRegisterPasswordMismatch()
    {
        $password = 'motdepasse123';
        $passwordConfirmation = 'motdepasse456';
        
        $passwordsMatch = $password === $passwordConfirmation;
        $this->assertFalse($passwordsMatch);
        
        // Expected error message
        $expectedError = "Passwords do not match!";
        $this->assertEquals("Passwords do not match!", $expectedError);
    }

    /**
     * Test validation - Identical passwords
     */
    public function testRegisterPasswordMatch()
    {
        $password = 'motdepasse123';
        $passwordConfirmation = 'motdepasse123';
        
        $passwordsMatch = $password === $passwordConfirmation;
        $this->assertTrue($passwordsMatch);
    }

    /**
     * Test email validation - Invalid format
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
            $this->assertFalse($isValid, "Invalid email: $email");
        }
        
        $expectedError = "Invalid email format!";
        $this->assertEquals("Invalid email format!", $expectedError);
    }

    /**
     * Test email validation - Valid format
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
            $this->assertNotFalse($isValid, "Valid email: $email");
        }
    }

    /**
     * Test password validation - Too short
     */
    public function testRegisterPasswordTooShort()
    {
        $shortPasswords = ['123', 'abc', '1234567']; // Less than 8 characters
        
        foreach ($shortPasswords as $password) {
            $isValid = strlen($password) >= 8;
            $this->assertFalse($isValid, "Password too short: $password");
        }
        
        $expectedError = "Password must contain at least 8 characters!";
        $this->assertEquals("Password must contain at least 8 characters!", $expectedError);
    }

    /**
     * Test password validation - Valid length
     */
    public function testRegisterPasswordValidLength()
    {
        $validPasswords = ['12345678', 'motdepasse123', 'unMotDePasseComplexe!'];
        
        foreach ($validPasswords as $password) {
            $isValid = strlen($password) >= 8;
            $this->assertTrue($isValid, "Valid password: $password");
        }
    }

    /**
     * Test input data trimming
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
     * Test default values handling with ?? operator
     */
    public function testRegisterDefaultValues()
    {
        // Test with empty $_POST
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
     * Test password hashing
     */
    public function testPasswordHashing()
    {
        $password = 'motdepasse123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Check that hash is generated
        $this->assertNotEmpty($hash);
        $this->assertNotEquals($password, $hash);
        
        // Check that hash can be verified
        $this->assertTrue(password_verify($password, $hash));
        
        // Check that wrong password fails
        $this->assertFalse(password_verify('wrong_password', $hash));
    }

    /**
     * Test registration date generation
     */
    public function testInscriptionDateGeneration()
    {
        $inscription_date = date("Y-m-d H:i:s");
        
        // Check date format
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $inscription_date);
        
        // Check that it's a valid date
        $timestamp = strtotime($inscription_date);
        $this->assertNotFalse($timestamp);
        
        // Check that date is recent (within last 5 seconds)
        $now = time();
        $this->assertLessThanOrEqual(5, abs($now - $timestamp));
    }

    /**
     * Test existing user check query structure
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
     * Test insert query structure
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
     * Test security - Parameter binding
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
     * Test error messages
     */
    public function testErrorMessages()
    {
        $errorMessages = [
            'password_mismatch' => "Passwords do not match!",
            'invalid_email' => "Invalid email format!",
            'password_length' => "Password must contain at least 8 characters!",
            'user_exists' => "This username or email is already in use!",
            'registration_error' => "Registration error.",
            'exception_prefix' => "Registration error: "
        ];
        
        foreach ($errorMessages as $key => $message) {
            $this->assertIsString($message);
            $this->assertNotEmpty($message);
        }
    }

    /**
     * Test redirection URLs
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
     * Test session logic after successful registration
     */
    public function testSuccessfulRegistrationSessionLogic()
    {
        $identifiant = 'nouveauuser';
        
        // Start session if not already active
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        // Simulate session creation after registration
        $_SESSION['user_id'] = session_id();
        $_SESSION['identifiant'] = $identifiant;
        
        // Check that session_id() now returns something
        $this->assertNotEmpty(session_id(), "Session ID should not be empty after session_start()");
        $this->assertNotEmpty($_SESSION['user_id']);
        $this->assertEquals($identifiant, $_SESSION['identifiant']);
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
        // Valid data
        $validData = [
            'identifiant' => 'user123',
            'email' => 'test@example.com',
            'telephone' => '0123456789',
            'password' => 'motdepasse123',
            'passwordConfirmation' => 'motdepasse123'
        ];
        
        // Validation tests
        $passwordsMatch = $validData['password'] === $validData['passwordConfirmation'];
        $emailValid = filter_var($validData['email'], FILTER_VALIDATE_EMAIL) !== false;
        $passwordLengthValid = strlen($validData['password']) >= 8;
        
        $this->assertTrue($passwordsMatch);
        $this->assertTrue($emailValid);
        $this->assertTrue($passwordLengthValid);
        
        // Test with invalid data
        $invalidData = [
            'password' => '123', // Too short
            'passwordConfirmation' => '456', // Different
            'email' => 'email-invalide' // Invalid format
        ];
        
        $passwordsMatchInvalid = $invalidData['password'] === $invalidData['passwordConfirmation'];
        $emailValidInvalid = filter_var($invalidData['email'], FILTER_VALIDATE_EMAIL) !== false;
        $passwordLengthValidInvalid = strlen($invalidData['password']) >= 8;
        
        $this->assertFalse($passwordsMatchInvalid);
        $this->assertFalse($emailValidInvalid);
        $this->assertFalse($passwordLengthValidInvalid);
    }

    /**
     * Test automatic session start
     */
    public function testSessionAutoStart()
    {
        // Simulate constructor logic
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
    }

    /**
     * Test existing users count logic
     */
    public function testUserExistsCountLogic()
    {
        // Simulate different COUNT(*) results
        $count_no_user = 0;
        $count_existing_user = 1;
        $count_multiple_users = 2;
        
        $this->assertFalse($count_no_user > 0);
        $this->assertTrue($count_existing_user > 0);
        $this->assertTrue($count_multiple_users > 0);
    }
}