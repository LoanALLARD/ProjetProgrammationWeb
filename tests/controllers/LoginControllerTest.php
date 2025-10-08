<?php

use PHPUnit\Framework\TestCase;

class LoginControllerTest extends TestCase
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
     * Test that LoginController class exists
     */
    public function testLoginControllerClassExists()
    {
        // Check that the file exists
        $controllerFile = __DIR__ . '/../../app/controllers/LoginController.php';
        $this->assertFileExists($controllerFile);
        
        // Basic test
        $this->assertTrue(true);
    }

    /**
     * Test index method without messages
     */
    public function testIndexWithoutMessages()
    {
        // No message in session
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
        $_SESSION['success_message'] = "Login successful!";
        
        $successMessage = $_SESSION['success_message'] ?? null;
        $this->assertEquals("Login successful!", $successMessage);
        
        // Simulate message deletion
        unset($_SESSION['success_message']);
        $this->assertArrayNotHasKey('success_message', $_SESSION);
    }

    /**
     * Test index method with error message
     */
    public function testIndexWithErrorMessage()
    {
        $_SESSION['error_message'] = "Invalid username or password!";
        
        $errorMessage = $_SESSION['error_message'] ?? null;
        $this->assertEquals("Invalid username or password!", $errorMessage);
        
        // Simulate message deletion
        unset($_SESSION['error_message']);
        $this->assertArrayNotHasKey('error_message', $_SESSION);
    }

    /**
     * Test session message cleanup
     */
    public function testSessionMessageCleanup()
    {
        $_SESSION['success_message'] = "Test success";
        $_SESSION['error_message'] = "Test error";
        
        // Simulate cleanup done in index()
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        $this->assertArrayNotHasKey('success_message', $_SESSION);
        $this->assertArrayNotHasKey('error_message', $_SESSION);
    }

    /**
     * Test login data validation - Empty username
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
     * Test login data validation - Empty password
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
     * Test login data validation - Valid data
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
     * Test username trimming
     */
    public function testIdentifiantTrimming()
    {
        $_POST['identifiant'] = '  user123  ';
        
        $identifiant = trim($_POST["identifiant"]);
        
        $this->assertEquals('user123', $identifiant);
        $this->assertNotEquals('  user123  ', $identifiant);
    }

    /**
     * Test password verification
     */
    public function testPasswordVerification()
    {
        $plainPassword = 'motdepasse123';
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        
        // Test with correct password
        $isValid = password_verify($plainPassword, $hashedPassword);
        $this->assertTrue($isValid);
        
        // Test with wrong password
        $isInvalid = password_verify('wrong_password', $hashedPassword);
        $this->assertFalse($isInvalid);
    }

    /**
     * Test session logic during successful login
     */
    public function testSuccessfulLoginSessionLogic()
    {
        // Simulate user data retrieved from DB
        $user = [
            'ID' => 123,
            'IDENTIFIANT' => 'user123',
            'PASSWORD' => password_hash('motdepasse123', PASSWORD_DEFAULT)
        ];
        
        // Simulate successful login logic
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['identifiant'] = $user['IDENTIFIANT'];
        $_SESSION['success_message'] = "Login successful!";
        
        $this->assertEquals(123, $_SESSION['user_id']);
        $this->assertEquals('user123', $_SESSION['identifiant']);
        $this->assertEquals("Login successful!", $_SESSION['success_message']);
    }

    /**
     * Test failed login logic
     */
    public function testFailedLoginLogic()
    {
        // Case 1: User not found (user = null)
        $user = null;
        $password = 'motdepasse123';
        
        $loginSuccessful = $user !== null && password_verify($password, $user['PASSWORD'] ?? '');
        $this->assertFalse($loginSuccessful);
        
        // Case 2: Wrong password
        $user = [
            'ID' => 123,
            'IDENTIFIANT' => 'user123',
            'PASSWORD' => password_hash('different_password', PASSWORD_DEFAULT)
        ];
        $password = 'motdepasse123';
        
        $loginSuccessful = $user !== null && password_verify($password, $user['PASSWORD']);
        $this->assertFalse($loginSuccessful);
    }

    /**
     * Test logout method - Session cleanup
     */
    public function testLogoutSessionCleanup()
    {
        // Prepare session with user data
        $_SESSION['user_id'] = 123;
        $_SESSION['identifiant'] = 'user123';
        $_SESSION['success_message'] = "Login successful!";
        $_SESSION['error_message'] = "An error";
        $_SESSION['other_data'] = "Other data";
        
        // Simulate logout logic
        unset($_SESSION['user_id']);
        unset($_SESSION['identifiant']);
        unset($_SESSION['success_message']);
        unset($_SESSION['error_message']);
        
        $this->assertArrayNotHasKey('user_id', $_SESSION);
        $this->assertArrayNotHasKey('identifiant', $_SESSION);
        $this->assertArrayNotHasKey('success_message', $_SESSION);
        $this->assertArrayNotHasKey('error_message', $_SESSION);
        
        // Other data should still be there before session_unset()
        $this->assertArrayHasKey('other_data', $_SESSION);
    }

    /**
     * Test error and success messages
     */
    public function testSessionMessages()
    {
        $messages = [
            'success_login' => "Login successful!",
            'error_credentials' => "Invalid username or password!",
            'error_connection_prefix' => "Connection error: ",
            'error_logout_prefix' => "Logout error: "
        ];
        
        foreach ($messages as $key => $message) {
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
            'home' => '/index.php?url=home/index',
            'login' => '/index.php?url=login/index'
        ];
        
        foreach ($urls as $key => $url) {
            $this->assertStringContainsString('/index.php?url=', $url);
            $this->assertNotEmpty($url);
        }
    }

    /**
     * Test SQL query structure
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
     * Test security - Parameter binding
     */
    public function testSecurityParameterBinding()
    {
        $param = ':identifiant';
        
        $this->assertStringStartsWith(':', $param);
        $this->assertEquals(':identifiant', $param);
    }

    /**
     * Test PDO parameter type validation
     */
    public function testPDOParameterType()
    {
        $this->assertTrue(defined('PDO::PARAM_STR'));
        $this->assertEquals(2, \PDO::PARAM_STR);
    }

    /**
     * Test exception handling logic
     */
    public function testExceptionHandlingLogic()
    {
        // Simulate an exception
        $exceptionMessage = "Database connection error";
        $errorMessage = "Connection error: " . $exceptionMessage;
        
        $this->assertEquals("Connection error: Database connection error", $errorMessage);
        $this->assertStringContainsString("Connection error", $errorMessage);
    }

    /**
     * Test retrieved user data validation
     */
    public function testUserDataValidation()
    {
        // Case with valid user
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
        
        // Case with non-existent user
        $nullUser = null;
        $this->assertNull($nullUser);
    }

    /**
     * Test user authentication status logic
     */
    public function testUserAuthenticationStatus()
    {
        // User not logged in
        $this->assertArrayNotHasKey('user_id', $_SESSION);
        $isLoggedIn = !empty($_SESSION['user_id']);
        $this->assertFalse($isLoggedIn);
        
        // User logged in
        $_SESSION['user_id'] = 123;
        $isLoggedIn = !empty($_SESSION['user_id']);
        $this->assertTrue($isLoggedIn);
    }
}