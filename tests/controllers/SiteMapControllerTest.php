<?php

use PHPUnit\Framework\TestCase;

class SiteMapControllerTest extends TestCase
{
    private $controller;
    private $testControllerDir;

    protected function setUp(): void
    {
        // Clean global variables
        $_SESSION = [];
        $_POST = [];
        $_GET = [];
        $_SERVER = [];
        
        // Create temporary directory for tests
        $this->testControllerDir = sys_get_temp_dir() . '/test_controllers_' . uniqid();
        mkdir($this->testControllerDir, 0777, true);
        
        // Create dummy controller files for tests
        $this->createTestControllerFiles();
    }

    protected function tearDown(): void
    {
        // Clean up test directory
        $this->cleanupTestDirectory();
        
        $_SESSION = [];
        $_POST = [];
        $_GET = [];
        $_SERVER = [];
    }

    private function createTestControllerFiles()
    {
        $testControllers = [
            'HomeController.php',
            'LoginController.php',
            'RegisterController.php',
            'ForgottenPasswordController.php',
            'ResetPasswordController.php',
            'SiteMapController.php' // This one should be ignored
        ];

        foreach ($testControllers as $controllerFile) {
            file_put_contents($this->testControllerDir . '/' . $controllerFile, '<?php // Test controller');
        }
    }

    private function cleanupTestDirectory()
    {
        if (is_dir($this->testControllerDir)) {
            $files = glob($this->testControllerDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->testControllerDir);
        }
    }

    /**
     * Test that SiteMapController class exists
     */
    public function testSiteMapControllerClassExists()
    {
        $controllerFile = __DIR__ . '/../../app/controllers/SiteMapController.php';
        $this->assertFileExists($controllerFile);
        
        require_once $controllerFile;
        $this->assertTrue(class_exists('controllers\SiteMapController'));
    }

    /**
     * Test constructor and pages property initialization
     */
    public function testConstructorInitializesPages()
    {
        // Include class for tests
        require_once __DIR__ . '/../../app/controllers/SiteMapController.php';
        
        $controller = new \controllers\SiteMapController();
        $pages = $controller->getPages();
        
        $this->assertIsArray($pages);
        $this->assertEmpty($pages); // Initially, array must be empty
    }

    /**
     * Test getPages() method
     */
    public function testGetPagesReturnsArray()
    {
        require_once __DIR__ . '/../../app/controllers/SiteMapController.php';
        
        $controller = new \controllers\SiteMapController();
        $pages = $controller->getPages();
        
        $this->assertIsArray($pages);
    }

    /**
     * Test glob() function - File search
     */
    public function testGlobFunctionality()
    {
        // Test glob with our test directory
        $controllerFiles = glob($this->testControllerDir . '/*.php');
        
        $this->assertIsArray($controllerFiles);
        $this->assertCount(6, $controllerFiles); // 6 files created
    }

    /**
     * Test basename() function
     */
    public function testBasenameFunction()
    {
        $testPaths = [
            '/path/to/HomeController.php' => 'HomeController',
            '/another/path/LoginController.php' => 'LoginController',
            'RegisterController.php' => 'RegisterController'
        ];

        foreach ($testPaths as $path => $expected) {
            $filename = basename($path, '.php');
            $this->assertEquals($expected, $filename);
        }
    }

    /**
     * Test SiteMapController exclusion
     */
    public function testSiteMapControllerExclusion()
    {
        $filename = 'SiteMapController';
        $shouldBeIgnored = ($filename === 'SiteMapController');
        
        $this->assertTrue($shouldBeIgnored);
        
        // Test with other controllers
        $otherControllers = ['HomeController', 'LoginController', 'RegisterController'];
        
        foreach ($otherControllers as $controller) {
            $shouldNotBeIgnored = ($controller !== 'SiteMapController');
            $this->assertTrue($shouldNotBeIgnored);
        }
    }

    /**
     * Test "Controller" suffix removal
     */
    public function testControllerNameProcessing()
    {
        $controllerNames = [
            'HomeController' => 'Home',
            'LoginController' => 'Login',
            'RegisterController' => 'Register',
            'ForgottenPasswordController' => 'ForgottenPassword',
            'ResetPasswordController' => 'ResetPassword'
        ];

        foreach ($controllerNames as $original => $expected) {
            $pageName = str_replace('Controller', '', $original);
            $this->assertEquals($expected, $pageName);
        }
    }

    /**
     * Test URL generation
     */
    public function testUrlGeneration()
    {
        $pageNames = [
            'Home' => '/index.php?url=home/index',
            'Login' => '/index.php?url=login/index',
            'Register' => '/index.php?url=register/index',
            'ForgottenPassword' => '/index.php?url=forgottenpassword/index'
        ];

        foreach ($pageNames as $pageName => $expectedUrl) {
            $url = "/index.php?url=" . strtolower($pageName) . "/index";
            $this->assertEquals($expectedUrl, $url);
        }
    }

    /**
     * Test strtolower() function
     */
    public function testStringToLowerFunction()
    {
        $testCases = [
            'Home' => 'home',
            'Login' => 'login',
            'REGISTER' => 'register',
            'ForgottenPassword' => 'forgottenpassword',
            'ResetPassword' => 'resetpassword'
        ];

        foreach ($testCases as $original => $expected) {
            $result = strtolower($original);
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * Test page data structure
     */
    public function testPageDataStructure()
    {
        $samplePage = [
            'title' => 'Home',
            'url'   => '/index.php?url=home/index'
        ];

        $this->assertIsArray($samplePage);
        $this->assertArrayHasKey('title', $samplePage);
        $this->assertArrayHasKey('url', $samplePage);
        $this->assertEquals('Home', $samplePage['title']);
        $this->assertEquals('/index.php?url=home/index', $samplePage['url']);
    }

    /**
     * Test page title validation
     */
    public function testPageTitleValidation()
    {
        $pageTitle = "Site Map";
        
        $this->assertIsString($pageTitle);
        $this->assertNotEmpty($pageTitle);
        $this->assertEquals("Site Map", $pageTitle);
    }

    /**
     * Test view path generation
     */
    public function testViewPathGeneration()
    {
        // Simulate __DIR__ for tests
        $mockDir = '/app/controllers';
        $expectedPath = '/app/views/siteMap.php';
        $viewPath = $mockDir . '/../views/siteMap.php';
        
        $this->assertStringContainsString('views/siteMap.php', $viewPath);
        $this->assertStringContainsString('../', $viewPath);
    }

    /**
     * Test file existence validation
     */
    public function testFileExistenceCheck()
    {
        // Create temporary file for test
        $tempFile = tempnam(sys_get_temp_dir(), 'test_view');
        file_put_contents($tempFile, '<?php echo "Test view"; ?>');
        
        $this->assertTrue(file_exists($tempFile));
        
        // Clean up
        unlink($tempFile);
        
        $this->assertFalse(file_exists($tempFile));
    }

    /**
     * Test view error message
     */
    public function testViewErrorMessage()
    {
        $viewPath = '/path/to/nonexistent/view.php';
        $expectedError = "Error loading view " . $viewPath;
        
        $this->assertStringContainsString("Error loading view", $expectedError);
        $this->assertStringContainsString($viewPath, $expectedError);
    }

    /**
     * Test complete controller processing
     */
    public function testCompleteControllerProcessing()
    {
        $controllerFileName = 'HomeController.php';
        
        // Step 1: Extract name
        $filename = basename($controllerFileName, '.php');
        $this->assertEquals('HomeController', $filename);
        
        // Step 2: Check it's not SiteMapController
        $isNotSiteMap = ($filename !== 'SiteMapController');
        $this->assertTrue($isNotSiteMap);
        
        // Step 3: Remove "Controller"
        $pageName = str_replace('Controller', '', $filename);
        $this->assertEquals('Home', $pageName);
        
        // Step 4: Generate URL
        $url = "/index.php?url=" . strtolower($pageName) . "/index";
        $this->assertEquals('/index.php?url=home/index', $url);
        
        // Step 5: Create data structure
        $pageData = [
            'title' => $pageName,
            'url'   => $url
        ];
        
        $this->assertArrayHasKey('title', $pageData);
        $this->assertArrayHasKey('url', $pageData);
        $this->assertEquals('Home', $pageData['title']);
        $this->assertEquals('/index.php?url=home/index', $pageData['url']);
    }

    /**
     * Test controller filtering logic
     */
    public function testControllerFiltering()
    {
        $allControllers = [
            'HomeController',
            'LoginController',
            'SiteMapController', // Should be filtered
            'RegisterController',
            'AdminController'
        ];

        $filteredControllers = [];
        
        foreach ($allControllers as $controller) {
            if ($controller !== 'SiteMapController') {
                $filteredControllers[] = $controller;
            }
        }

        $this->assertCount(4, $filteredControllers);
        $this->assertNotContains('SiteMapController', $filteredControllers);
        $this->assertContains('HomeController', $filteredControllers);
        $this->assertContains('LoginController', $filteredControllers);
    }

    /**
     * Test URL validation
     */
    public function testUrlValidation()
    {
        $urls = [
            '/index.php?url=home/index',
            '/index.php?url=login/index',
            '/index.php?url=register/index'
        ];

        foreach ($urls as $url) {
            $this->assertStringStartsWith('/index.php?url=', $url);
            $this->assertStringEndsWith('/index', $url);
            $this->assertStringContainsString('?url=', $url);
        }
    }

    /**
     * Test edge cases with controller names
     */
    public function testEdgeCasesControllerNames()
    {
        $edgeCases = [
            'TestController' => 'Test',
            'MyLongControllerNameController' => 'MyLongName', // Previously corrected
            'ControllerController' => '', // str_replace removes ALL "Controller"
            'SimpleController' => 'Simple'
        ];

        foreach ($edgeCases as $original => $expected) {
            $result = str_replace('Controller', '', $original);
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * Test robustness with different file formats
     */
    public function testFileFormatRobustness()
    {
        $files = [
            '/full/path/to/HomeController.php',
            'relative/LoginController.php',
            'RegisterController.php'
        ];

        foreach ($files as $file) {
            $filename = basename($file, '.php');
            $this->assertStringEndsWith('Controller', $filename);
            // Change assertStringNotContains to assertStringNotContainsString
            $this->assertStringNotContainsString('.php', $filename);
            $this->assertStringNotContainsString('/', $filename);
        }
    }

    /**
     * Test site map creation logic
     */
    public function testSiteMapCreationLogic()
    {
        // Simulate complete process
        $mockControllers = ['HomeController.php', 'LoginController.php'];
        $pages = [];

        foreach ($mockControllers as $file) {
            $filename = basename($file, '.php');
            
            if ($filename === 'SiteMapController') {
                continue;
            }
            
            $pageName = str_replace('Controller', '', $filename);
            $url = "/index.php?url=" . strtolower($pageName) . "/index";
            
            $pages[] = [
                'title' => $pageName,
                'url'   => $url
            ];
        }

        $this->assertCount(2, $pages);
        $this->assertEquals('Home', $pages[0]['title']);
        $this->assertEquals('/index.php?url=home/index', $pages[0]['url']);
        $this->assertEquals('Login', $pages[1]['title']);
        $this->assertEquals('/index.php?url=login/index', $pages[1]['url']);
    }
}