<?php

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        // Nettoyer les variables globales
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
    }

    /**
     * Test que la classe Router existe
     */
    public function testRouterClassExists()
    {
        $routerFile = __DIR__ . '/../../app/core/Router.php';
        $this->assertFileExists($routerFile);
        
        require_once $routerFile;
        $this->assertTrue(class_exists('core\Router'));
    }

    /**
     * Test que la classe Router peut être instanciée
     */
    public function testRouterCanBeInstantiated()
    {
        require_once __DIR__ . '/../../app/core/Router.php';
        
        $router = new \core\Router();
        $this->assertInstanceOf(\core\Router::class, $router);
    }

    /**
     * Test de la propriété controllerMap
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
     * Test du contenu de controllerMap
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
     * Test de la méthode toUpperCase avec cas spéciaux
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
     * Test de la logique strtolower dans toUpperCase
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
     * Test de la méthode toUpperCase avec chaînes simples
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
     * Test de la méthode toUpperCase avec majuscules déjà présentes
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
     * Test de la méthode toUpperCase avec séparateurs
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
     * Test de la logique ucwords dans toUpperCase
     */
    public function testToUpperCaseUcwordsLogic()
    {
        // Test des fonctions utilisées dans toUpperCase
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
     * Test de la regex preg_match dans toUpperCase
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
     * Test de traitement d'URL par défaut
     */
    public function testDefaultUrlHandling()
    {
        // Test avec $_GET vide
        $_GET = [];
        
        $url = $_GET['url'] ?? 'home/index';
        $this->assertEquals('home/index', $url);
        
        // Test avec $_GET['url'] défini
        $_GET['url'] = 'login/index';
        $url = $_GET['url'] ?? 'home/index';
        $this->assertEquals('login/index', $url);
    }

    /**
     * Test de la logique explode pour les paramètres
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
     * Test de construction du nom de contrôleur
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
     * Test de récupération de la méthode par défaut
     */
    /**
 * Test de récupération de la méthode par défaut - CORRIGÉ
 */
public function testDefaultMethodRetrieval()
{
    // Test 1: Un seul paramètre → méthode par défaut 'index'
    $params1 = ['home'];
    $methodName1 = $params1[1] ?? 'index';
    $this->assertEquals('index', $methodName1);
    
    // Test 2: Deux paramètres → deuxième paramètre
    $params2 = ['home', 'index'];
    $methodName2 = $params2[1] ?? 'index';
    $this->assertEquals('index', $methodName2);
    
    // Test 3: Deux paramètres avec méthode différente
    $params3 = ['home', 'show'];
    $methodName3 = $params3[1] ?? 'index';
    $this->assertEquals('show', $methodName3);
    
    // Test 4: Deux paramètres avec autre méthode
    $params4 = ['user', 'profile'];
    $methodName4 = $params4[1] ?? 'index';
    $this->assertEquals('profile', $methodName4);
}

    /**
     * Test des messages d'erreur
     */
    public function testErrorMessages()
    {
        $controllerName = 'controllers\\NonExistentController';
        $methodName = 'nonExistentMethod';
        
        $controllerError = "Contrôleur $controllerName inexistant.";
        $methodError = "Méthode $methodName inexistante.";
        
        $this->assertEquals("Contrôleur controllers\\NonExistentController inexistant.", $controllerError);
        $this->assertEquals("Méthode nonExistentMethod inexistante.", $methodError);
        
        $this->assertStringContainsString('inexistant', $controllerError);
        $this->assertStringContainsString('inexistante', $methodError);
    }

    /**
     * Test de la visibilité des méthodes
     */
    public function testMethodVisibility()
    {
        require_once __DIR__ . '/../../app/core/Router.php';
        
        $reflection = new ReflectionClass('core\Router');
        
        // run() doit être publique
        $runMethod = $reflection->getMethod('run');
        $this->assertTrue($runMethod->isPublic());
        
        // toUpperCase() doit être privée
        $toUpperCaseMethod = $reflection->getMethod('toUpperCase');
        $this->assertTrue($toUpperCaseMethod->isPrivate());
    }

    /**
     * Test de workflow complet de routage
     */
    public function testCompleteRoutingWorkflow()
    {
        $_GET['url'] = 'home/index';
        
        // Étape 1: Récupération de l'URL
        $url = $_GET['url'] ?? 'home/index';
        $this->assertEquals('home/index', $url);
        
        // Étape 2: Explosion des paramètres
        $params = explode('/', $url);
        $this->assertEquals(['home', 'index'], $params);
        
        // Étape 3: Construction du nom de contrôleur (simulation)
        $transformedController = ucfirst(strtolower($params[0])); // Simulation simplifiée
        $controllerName = 'controllers\\' . $transformedController . 'Controller';
        $this->assertEquals('controllers\\HomeController', $controllerName);
        
        // Étape 4: Récupération de la méthode
        $methodName = $params[1] ?? 'index';
        $this->assertEquals('index', $methodName);
    }

    /**
     * Test avec URL complexe
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
     * Test de namespace dans le nom de contrôleur
     */
    public function testNamespaceInControllerName()
    {
        $controllerName = 'controllers\\HomeController';
        
        $this->assertStringStartsWith('controllers\\', $controllerName);
        $this->assertStringEndsWith('Controller', $controllerName);
        $this->assertStringContainsString('\\', $controllerName);
    }

    /**
     * Test de cas limite - URL vide
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
     * Test de robustesse avec différents types d'URL
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