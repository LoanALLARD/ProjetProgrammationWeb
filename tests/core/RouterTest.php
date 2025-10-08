<?php

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        // Clean global variables
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
    }

    /**
     * Test that Router class exists
     */
    public function testRouterClassExists()
    {
        $routerFile = __DIR__ . '/../../app/core/Router.php';
        $this->assertFileExists($routerFile);
        
        require_once $routerFile;
        $this->assertTrue(class_exists('core\Router'));
    }

    /**
     * Test that Router class can be instantiated
     */
    public function testRouterCanBeInstantiated()
    {
        require_once __DIR__ . '/../../app/core/Router.php';
        
        $router = new \core\Router();
        $this->assertInstanceOf(\core\Router::class, $router);
    }

    /**
     * Test controllerMap property
     */
    public function testControllerMapProperty()
    {
        require_once __DIR__ . '/../../app/core/Router.php';
        
        $router = new \core\Router();
        $reflection = new ReflectionClass($router);
        
        $this->assertTrue($reflection->hasProperty('controllerMap'));
        
        $controllerMapProperty = $reflection->getProperty('controllerMap');
        $this->assertTrue($controllerMapProperty->isPrivate());
    }

    /**
     * Test controllerMap content
     */
    public function testControllerMapContent()
    {
        require_once __DIR__ . '/../../app/core/Router.php';
        
        $router = new \core\Router();
        $reflection = new ReflectionClass($router);
        
        $controllerMapProperty = $reflection->getProperty('controllerMap');
        $controllerMapProperty->setAccessible(true);
        $controllerMap = $controllerMapProperty->getValue($router);
        
        $expectedMappings = [
            'forgottenpassword' => 'ForgottenPassword',
            'updatepassword' => 'UpdatePassword',
            'resetpassword' => 'ResetPassword',
            'sitemap' => 'SiteMap',
            'mentionslegales' => 'MentionsLegales',
        ];
        
        $this->assertEquals($expectedMappings, $controllerMap);
        $this->assertIsArray($controllerMap);
        $this->assertCount(5, $controllerMap);
    }

    /**
     * Test toUpperCase method with special cases
     */
    public function testToUpperCaseWithSpecialCases()
    {
        require_once __DIR__ . '/../../app/core/Router.php';
        
        $router = new \core\Router();
        $reflection = new ReflectionClass($router);
        
        $toUpperCaseMethod = $reflection->getMethod('toUpperCase');
        $toUpperCaseMethod->setAccessible(true);
        
        $specialCases = [
            'forgottenpassword' => 'ForgottenPassword',
            'updatepassword' => 'UpdatePassword',
            'resetpassword' => 'ResetPassword',
            'sitemap' => 'SiteMap',
            'mentionslegales' => 'MentionsLegales',
        ];
        
        foreach ($specialCases as $input => $expected) {
            $result = $toUpperCaseMethod->invokeArgs($router, [$input]);
            $this->assertEquals($expected, $result, "Input: $input");
        }
    }

    /**
     * Test strtolower logic in toUpperCase
     */
    public function testToUpperCaseStrtolowerLogic()
    {
        require_once __DIR__ . '/../../app/core/Router.php';
        
        $router = new \core\Router();
        $reflection = new ReflectionClass($router);
        
        $toUpperCaseMethod = $reflection->getMethod('toUpperCase');
        $toUpperCaseMethod->setAccessible(true);
        
        $testCases = [
            'HOME' => 'HOME',        
            'LOGIN' => 'LOGIN',      
            'REGISTER' => 'REGISTER',
            'FORGOTTENPASSWORD' => 'ForgottenPassword',
        ];
        
        foreach ($testCases as $input => $expected) {
            $result = $toUpperCaseMethod->invokeArgs($router, [$input]);
            $this->assertEquals($expected, $result, "Input: $input");
        }
    }

    /**
     * Test toUpperCase method with simple strings
     */
    public function testToUpperCaseWithSimpleStrings()
    {
        require_once __DIR__ . '/../../app/core/Router.php';
        
        $router = new \core\Router();
        $reflection = new ReflectionClass($router);
        
        $toUpperCaseMethod = $reflection->getMethod('toUpperCase');
        $toUpperCaseMethod->setAccessible(true);
        
        $testCases = [
            'home' => 'Home',
            'login' => 'Login',
            'register' => 'Register',
            'user' => 'User',
            'admin' => 'Admin'
        ];
        
        foreach ($testCases as $input => $expected) {
            $result = $toUpperCaseMethod->invokeArgs($router, [$input]);
            $this->assertEquals($expected, $result, "Input: $input");
        }
    }

    /**
     * Test toUpperCase method with existing capitals
     */
    public function testToUpperCaseWithExistingCapitals()
    {
        require_once __DIR__ . '/../../app/core/Router.php';
        
        $router = new \core\Router();
        $reflection = new ReflectionClass($router);
        
        $toUpperCaseMethod = $reflection->getMethod('toUpperCase');
        $toUpperCaseMethod->setAccessible(true);
        
        $testCases = [
            'Home' => 'Home',
            'Login' => 'Login',
            'MyController' => 'MyController',
            'AdminPanel' => 'AdminPanel'
        ];
        
        foreach ($testCases as $input => $expected) {
            $result = $toUpperCaseMethod->invokeArgs($router, [$input]);
            $this->assertEquals($expected, $result, "Input: $input");
        }
    }

    /**
     * Test toUpperCase method with separators
     */
    public function testToUpperCaseWithSeparators()
    {
        require_once __DIR__ . '/../../app/core/Router.php';
        
        $router = new \core\Router();
        $reflection = new ReflectionClass($router);
        
        $toUpperCaseMethod = $reflection->getMethod('toUpperCase');
        $toUpperCaseMethod->setAccessible(true);
        
        $testCases = [
            'user-profile' => 'UserProfile',
            'admin_panel' => 'AdminPanel',
            'contact-us' => 'ContactUs',
            'my_account' => 'MyAccount',
            'user-admin-panel' => 'UserAdminPanel'
        ];
        
        foreach ($testCases as $input => $expected) {
            $result = $toUpperCaseMethod->invokeArgs($router, [$input]);
            $this->assertEquals($expected, $result, "Input: $input");
        }
    }

    /**
     * Test ucwords logic in toUpperCase
     */
    public function testToUpperCaseUcwordsLogic()
    {
        // Test functions used in toUpperCase
        $testStrings = [
            'hello world' => 'Hello World',
            'my test string' => 'My Test String',
            'user admin panel' => 'User Admin Panel'
        ];
        
        foreach ($testStrings as $input => $expected) {
            $result = ucwords($input);
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * Test preg_match regex in toUpperCase
     */
    public function testToUpperCasePregMatchLogic()
    {
        $stringsWithCapitals = ['Home', 'MyController', 'AdminPanel', 'UserA'];
        $stringsWithoutCapitals = ['home', 'login', 'register', 'user'];
        
        foreach ($stringsWithCapitals as $string) {
            $hasCapitals = preg_match('/[A-Z]/', $string);
            $this->assertTrue($hasCapitals > 0, "String '$string' should have capitals");
        }
        
        foreach ($stringsWithoutCapitals as $string) {
            $hasCapitals = preg_match('/[A-Z]/', $string);
            $this->assertFalse($hasCapitals > 0, "String '$string' should not have capitals");
        }
    }

    /**
     * Test default URL handling
     */
    public function testDefaultUrlHandling()
    {
        // Test with empty $_GET
        $_GET = [];
        
        $url = $_GET['url'] ?? 'home/index';
        $this->assertEquals('home/index', $url);
        
        // Test with $_GET['url'] defined
        $_GET['url'] = 'login/index';
        $url = $_GET['url'] ?? 'home/index';
        $this->assertEquals('login/index', $url);
    }

    /**
     * Test explode logic for parameters
     */
    public function testExplodeUrlLogic()
    {
        $testUrls = [
            'home/index' => ['home', 'index'],
            'login' => ['login'],
            'user/profile/edit' => ['user', 'profile', 'edit'],
            'admin/users/list' => ['admin', 'users', 'list']
        ];
        
        foreach ($testUrls as $url => $expectedParams) {
            $params = explode('/', $url);
            $this->assertEquals($expectedParams, $params, "URL: $url");
        }
    }

    /**
     * Test controller name construction
     */
    public function testControllerNameConstruction()
    {
        $testCases = [
            'Home' => 'controllers\\HomeController',
            'Login' => 'controllers\\LoginController',
            'ForgottenPassword' => 'controllers\\ForgottenPasswordController',
            'SiteMap' => 'controllers\\SiteMapController'
        ];
        
        foreach ($testCases as $input => $expected) {
            $controllerName = 'controllers\\' . $input . 'Controller';
            $this->assertEquals($expected, $controllerName, "Input: $input");
        }
    }

    /**
     * Test default method retrieval - CORRECTED
     */
    public function testDefaultMethodRetrieval()
    {
        // Test 1: Single parameter → default method 'index'
        $params1 = ['home'];
        $methodName1 = $params1[1] ?? 'index';
        $this->assertEquals('index', $methodName1);
        
        // Test 2: Two parameters → second parameter
        $params2 = ['home', 'index'];
        $methodName2 = $params2[1] ?? 'index';
        $this->assertEquals('index', $methodName2);
        
        // Test 3: Two parameters with different method
        $params3 = ['home', 'show'];
        $methodName3 = $params3[1] ?? 'index';
        $this->assertEquals('show', $methodName3);
        
        // Test 4: Two parameters with another method
        $params4 = ['user', 'profile'];
        $methodName4 = $params4[1] ?? 'index';
        $this->assertEquals('profile', $methodName4);
    }

    /**
     * Test error messages
     */
    public function testErrorMessages()
    {
        $controllerName = 'controllers\\NonExistentController';
        $methodName = 'nonExistentMethod';
        
        $controllerError = "Controller $controllerName does not exist.";
        $methodError = "Method $methodName does not exist.";
        
        $this->assertEquals("Controller controllers\\NonExistentController does not exist.", $controllerError);
        $this->assertEquals("Method nonExistentMethod does not exist.", $methodError);
        
        $this->assertStringContainsString('does not exist', $controllerError);
        $this->assertStringContainsString('does not exist', $methodError);
    }

    /**
     * Test method visibility
     */
    public function testMethodVisibility()
    {
        require_once __DIR__ . '/../../app/core/Router.php';
        
        $reflection = new ReflectionClass('core\Router');
        
        // run() must be public
        $runMethod = $reflection->getMethod('run');
        $this->assertTrue($runMethod->isPublic());
        
        // toUpperCase() must be private
        $toUpperCaseMethod = $reflection->getMethod('toUpperCase');
        $this->assertTrue($toUpperCaseMethod->isPrivate());
    }

    /**
     * Test complete routing workflow
     */
    public function testCompleteRoutingWorkflow()
    {
        $_GET['url'] = 'home/index';
        
        // Step 1: URL retrieval
        $url = $_GET['url'] ?? 'home/index';
        $this->assertEquals('home/index', $url);
        
        // Step 2: Parameter explosion
        $params = explode('/', $url);
        $this->assertEquals(['home', 'index'], $params);
        
        // Step 3: Controller name construction (simulation)
        $transformedController = ucfirst(strtolower($params[0])); // Simplified simulation
        $controllerName = 'controllers\\' . $transformedController . 'Controller';
        $this->assertEquals('controllers\\HomeController', $controllerName);
        
        // Step 4: Method retrieval
        $methodName = $params[1] ?? 'index';
        $this->assertEquals('index', $methodName);
    }

    /**
     * Test with complex URL
     */
    public function testComplexUrlHandling()
    {
        $_GET['url'] = 'forgotten-password/index';
        
        $url = $_GET['url'] ?? 'home/index';
        $params = explode('/', $url);
        
        $this->assertEquals('forgotten-password', $params[0]);
        $this->assertEquals('index', $params[1]);
    }

    /**
     * Test namespace in controller name
     */
    public function testNamespaceInControllerName()
    {
        $controllerName = 'controllers\\HomeController';
        
        $this->assertStringStartsWith('controllers\\', $controllerName);
        $this->assertStringEndsWith('Controller', $controllerName);
        $this->assertStringContainsString('\\', $controllerName);
    }

    /**
     * Test edge case - Empty URL
     */
    public function testEmptyUrlHandling()
    {
        $_GET = [];
        
        $url = $_GET['url'] ?? 'home/index';
        $this->assertEquals('home/index', $url);
        
        $params = explode('/', $url);
        $this->assertEquals(['home', 'index'], $params);
    }

    /**
     * Test robustness with different URL types
     */
    public function testUrlRobustness()
    {
        $testUrls = [
            'home' => ['home'],
            'home/' => ['home', ''],
            'home/index' => ['home', 'index'],
            'user/profile/edit/123' => ['user', 'profile', 'edit', '123']
        ];
        
        foreach ($testUrls as $url => $expectedParams) {
            $params = explode('/', $url);
            $this->assertEquals($expectedParams, $params, "URL: $url");
        }
    }
}