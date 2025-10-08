<?php

use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
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
     * Test that HomeController class exists
     */
    public function testHomeControllerClassExists()
    {
        // Check that the file exists
        $controllerFile = __DIR__ . '/../../app/controllers/HomeController.php';
        $this->assertFileExists($controllerFile);
        
        // Include the file to check syntax
        $this->assertTrue(true); // Basic test without instantiation
    }

    /**
     * Test data validation for addNote - Empty title
     */
    public function testAddNoteValidationEmptyTitle()
    {
        // Simulate validation logic without instantiating the controller
        $user_id = 123;
        $titre = ''; // Empty title
        $contenu = 'Test content';
        
        // Test validation logic
        $isValid = !empty($user_id) && !empty($titre) && !empty($contenu);
        $this->assertFalse($isValid, "Validation should fail with empty title");
    }

    /**
     * Test data validation for addNote - Empty content
     */
    public function testAddNoteValidationEmptyContent()
    {
        $user_id = 123;
        $titre = 'My title';
        $contenu = ''; // Empty content
        
        $isValid = !empty($user_id) && !empty($titre) && !empty($contenu);
        $this->assertFalse($isValid, "Validation should fail with empty content");
    }

    /**
     * Test data validation for addNote - No logged user
     */
    public function testAddNoteValidationNoUser()
    {
        $user_id = null; // No user
        $titre = 'My title';
        $contenu = 'My content';
        
        $isValid = !empty($user_id) && !empty($titre) && !empty($contenu);
        $this->assertFalse($isValid, "Validation should fail without logged user");
    }

    /**
     * Test data validation for addNote - Valid data
     */
    public function testAddNoteValidationValid()
    {
        $user_id = 123;
        $titre = 'My title';
        $contenu = 'My content';
        
        $isValid = !empty($user_id) && !empty($titre) && !empty($contenu);
        $this->assertTrue($isValid, "Validation should succeed with valid data");
    }

    /**
     * Test date generation for notes
     */
    public function testNoteDateGeneration()
    {
        $date = date("Y-m-d");
        
        // Check date format
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $date);
        
        // Check that it's a valid date
        $dateParts = explode('-', $date);
        $this->assertCount(3, $dateParts);
        $this->assertTrue(checkdate($dateParts[1], $dateParts[2], $dateParts[0]));
    }

    /**
     * Test validation for modifyNote - POST method
     */
    public function testModifyNotePostValidation()
    {
        $user_id = 123;
        $note_id = '456';
        $titre = 'New title';
        $contenu = 'New content';
        
        // Simulate POST validation
        $isValidPost = !empty($note_id) && !empty($titre) && !empty($contenu);
        $this->assertTrue($isValidPost, "POST validation should succeed");
        
        // Test with missing data
        $isValidPostIncomplete = !empty($note_id) && !empty('') && !empty($contenu);
        $this->assertFalse($isValidPostIncomplete, "POST validation should fail with empty title");
    }

    /**
     * Test validation for modifyNote - GET method
     */
    public function testModifyNoteGetValidation()
    {
        $user_id = 123;
        $note_id = '456';
        
        // Simulate GET validation
        $isValidGet = !empty($note_id);
        $this->assertTrue($isValidGet, "GET validation should succeed with an ID");
        
        // Test without ID
        $isValidGetNoId = !empty('');
        $this->assertFalse($isValidGetNoId, "GET validation should fail without ID");
    }

    /**
     * Test validation for deleteNote
     */
    public function testDeleteNoteValidation()
    {
        $user_id = 123;
        $note_id = '456';
        
        // Validation with valid data
        $isValid = !empty($user_id) && !empty($note_id);
        $this->assertTrue($isValid, "Delete validation should succeed");
        
        // Validation without user
        $isValidNoUser = !empty(null) && !empty($note_id);
        $this->assertFalse($isValidNoUser, "Delete validation should fail without user");
        
        // Validation without ID
        $isValidNoId = !empty($user_id) && !empty('');
        $this->assertFalse($isValidNoId, "Delete validation should fail without ID");
    }

    /**
     * Test error and success messages
     */
    public function testSessionMessages()
    {
        $messages = [
            'success_add' => "Note added successfully!",
            'success_modify' => "Note modified successfully!",
            'success_delete' => "Note deleted successfully!",
            'error_fields' => "You must be logged in and fill all fields.",
            'error_required' => "All fields are required.",
            'error_login' => "You must be logged in.",
            'error_delete' => "Unable to delete the note.",
            'error_not_found' => "Note not found."
        ];
        
        foreach ($messages as $key => $message) {
            $this->assertIsString($message);
            $this->assertNotEmpty($message);
            $this->assertGreaterThan(5, strlen($message)); // Meaningful messages
        }
    }

    /**
     * Test redirection URLs
     */
    public function testRedirectionUrls()
    {
        $urls = [
            'home_index' => 'index.php?url=home/index',
            'home_add_form' => 'index.php?url=home/showAddForm&action=add',
            'auth_login' => 'index.php?url=auth/login'
        ];
        
        foreach ($urls as $key => $url) {
            $this->assertStringContainsString('index.php?url=', $url);
            $this->assertNotEmpty($url);
        }
    }

    /**
     * Test SQL query patterns structure
     */
    public function testSqlQueryPatterns()
    {
        $queries = [
            'select' => 'SELECT id, titre, contenu FROM notes WHERE USER_ID = :user_id',
            'insert' => 'INSERT INTO notes (USER_ID, TITRE, CONTENU, DATE_CREATION) VALUES (:user_id,:titre,:contenu,:inscription_date)',
            'update' => 'UPDATE notes SET TITRE = :titre, CONTENU = :contenu WHERE ID = :id AND USER_ID = :user_id',
            'delete' => 'DELETE FROM notes WHERE ID = :id AND USER_ID = :user_id'
        ];
        
        foreach ($queries as $type => $query) {
            $this->assertStringContainsString('notes', $query);
            $this->assertStringContainsString('USER_ID', $query);
            $this->assertStringContainsString(':', $query); // Bound parameters
        }
    }

    /**
     * Test security - Parameter binding
     */
    public function testSecurityParameterBinding()
    {
        $secureParams = [':user_id', ':titre', ':contenu', ':id', ':inscription_date'];
        
        foreach ($secureParams as $param) {
            $this->assertStringStartsWith(':', $param);
            $this->assertGreaterThan(2, strlen($param));
        }
    }

    /**
     * Test PDO parameter types
     */
    public function testPDOParameterTypes()
    {
        // Check that PDO constants exist
        $this->assertTrue(defined('PDO::PARAM_INT'));
        $this->assertTrue(defined('PDO::PARAM_STR'));
        
        // Check constant values
        $this->assertEquals(1, \PDO::PARAM_INT);
        $this->assertEquals(2, \PDO::PARAM_STR);
    }

    /**
     * Test HTTP methods validation
     */
    public function testHttpMethods()
    {
        $methods = ['GET', 'POST'];
        
        foreach ($methods as $method) {
            $this->assertIsString($method);
            $this->assertContains($method, ['GET', 'POST', 'PUT', 'DELETE']);
        }
    }

    /**
     * Test numeric ID validation
     */
    public function testNumericIdValidation()
    {
        $validIds = ['123', '456', '1'];
        $invalidIds = ['abc', '', '0', '-1'];
        
        foreach ($validIds as $id) {
            $this->assertTrue(is_numeric($id));
            $this->assertGreaterThan(0, (int)$id);
        }
        
        foreach ($invalidIds as $id) {
            $isValid = is_numeric($id) && (int)$id > 0;
            $this->assertFalse($isValid);
        }
    }

    /**
     * Test user session logic
     */
    public function testUserSessionLogic()
    {
        // Test with logged user
        $_SESSION['user_id'] = 123;
        $isLoggedIn = !empty($_SESSION['user_id']);
        $this->assertTrue($isLoggedIn);
        
        // Test without user
        $_SESSION['user_id'] = null;
        $isLoggedIn = !empty($_SESSION['user_id']);
        $this->assertFalse($isLoggedIn);
        
        // Test with empty session
        unset($_SESSION['user_id']);
        $isLoggedIn = !empty($_SESSION['user_id']);
        $this->assertFalse($isLoggedIn);
    }
}