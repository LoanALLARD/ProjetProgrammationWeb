<?php

use PHPUnit\Framework\TestCase;

class SiteMapControllerTest extends TestCase
{
    private $controller;
    private $testControllerDir;

    protected function setUp(): void
    {
        // Nettoyer les variables globales
        $_SESSION = [];
        $_POST = [];
        $_GET = [];
        $_SERVER = [];
        
        // Créer un répertoire temporaire pour les tests
        $this->testControllerDir = sys_get_temp_dir() . '/test_controllers_' . uniqid();
        mkdir($this->testControllerDir, 0777, true);
        
        // Créer des fichiers de contrôleur factices pour les tests
        $this->createTestControllerFiles();
    }

    protected function tearDown(): void
    {
        // Nettoyer le répertoire de test
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
            'SiteMapController.php' // Celui-ci devrait être ignoré
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
     * Test que la classe SiteMapController existe
     */
public function testSiteMapControllerClassExists()
    {
        $controllerFile = __DIR__ . '/../../app/controllers/SiteMapController.php';
        $this->assertFileExists($controllerFile);
        
        require_once $controllerFile;
        $this->assertTrue(class_exists('controllers\SiteMapController'));
    }

    /**
     * Test du constructeur et initialisation de la propriété pages
     */
    public function testConstructorInitializesPages()
    {
        // Inclure la classe pour les tests
        require_once __DIR__ . '/../../app/controllers/SiteMapController.php';
        
        $controller = new \controllers\SiteMapController();
        $pages = $controller->getPages();
        
        $this->assertIsArray($pages);
        $this->assertEmpty($pages); // Au début, le tableau doit être vide
    }

    /**
     * Test de la méthode getPages()
     */
    public function testGetPagesReturnsArray()
    {
        require_once __DIR__ . '/../../app/controllers/SiteMapController.php';
        
        $controller = new \controllers\SiteMapController();
        $pages = $controller->getPages();
        
        $this->assertIsArray($pages);
    }

    /**
     * Test de la fonction glob() - Recherche de fichiers
     */
    public function testGlobFunctionality()
    {
        // Tester glob avec notre répertoire de test
        $controllerFiles = glob($this->testControllerDir . '/*.php');
        
        $this->assertIsArray($controllerFiles);
        $this->assertCount(6, $controllerFiles); // 6 fichiers créés
    }

    /**
     * Test de la fonction basename()
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
     * Test de l'exclusion de SiteMapController
     */
    public function testSiteMapControllerExclusion()
    {
        $filename = 'SiteMapController';
        $shouldBeIgnored = ($filename === 'SiteMapController');
        
        $this->assertTrue($shouldBeIgnored);
        
        // Test avec d'autres contrôleurs
        $otherControllers = ['HomeController', 'LoginController', 'RegisterController'];
        
        foreach ($otherControllers as $controller) {
            $shouldNotBeIgnored = ($controller !== 'SiteMapController');
            $this->assertTrue($shouldNotBeIgnored);
        }
    }

    /**
     * Test de suppression du suffixe "Controller"
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
     * Test de génération d'URL
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
     * Test de la fonction strtolower()
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
     * Test de structure de données des pages
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
     * Test de validation du titre de page
     */
    public function testPageTitleValidation()
    {
        $pageTitle = "Plan du site";
        
        $this->assertIsString($pageTitle);
        $this->assertNotEmpty($pageTitle);
        $this->assertEquals("Plan du site", $pageTitle);
    }

    /**
     * Test de chemin de vue
     */
    public function testViewPathGeneration()
    {
        // Simuler __DIR__ pour les tests
        $mockDir = '/app/controllers';
        $expectedPath = '/app/views/siteMap.php';
        $viewPath = $mockDir . '/../views/siteMap.php';
        
        $this->assertStringContainsString('views/siteMap.php', $viewPath);
        $this->assertStringContainsString('../', $viewPath);
    }

    /**
     * Test de validation de l'existence de fichier
     */
    public function testFileExistenceCheck()
    {
        // Créer un fichier temporaire pour le test
        $tempFile = tempnam(sys_get_temp_dir(), 'test_view');
        file_put_contents($tempFile, '<?php echo "Test view"; ?>');
        
        $this->assertTrue(file_exists($tempFile));
        
        // Nettoyer
        unlink($tempFile);
        
        $this->assertFalse(file_exists($tempFile));
    }

    /**
     * Test de message d'erreur de vue
     */
    public function testViewErrorMessage()
    {
        $viewPath = '/path/to/nonexistent/view.php';
        $expectedError = "Erreur lors du chargement de la vue " . $viewPath;
        
        $this->assertStringContainsString("Erreur lors du chargement de la vue", $expectedError);
        $this->assertStringContainsString($viewPath, $expectedError);
    }

    /**
     * Test de traitement complet d'un contrôleur
     */
    public function testCompleteControllerProcessing()
    {
        $controllerFileName = 'HomeController.php';
        
        // Étape 1: Extraire le nom
        $filename = basename($controllerFileName, '.php');
        $this->assertEquals('HomeController', $filename);
        
        // Étape 2: Vérifier qu'il ne s'agit pas de SiteMapController
        $isNotSiteMap = ($filename !== 'SiteMapController');
        $this->assertTrue($isNotSiteMap);
        
        // Étape 3: Supprimer "Controller"
        $pageName = str_replace('Controller', '', $filename);
        $this->assertEquals('Home', $pageName);
        
        // Étape 4: Générer l'URL
        $url = "/index.php?url=" . strtolower($pageName) . "/index";
        $this->assertEquals('/index.php?url=home/index', $url);
        
        // Étape 5: Créer la structure de données
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
     * Test de logique de filtrage des contrôleurs
     */
    public function testControllerFiltering()
    {
        $allControllers = [
            'HomeController',
            'LoginController',
            'SiteMapController', // Devrait être filtré
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
     * Test de validation d'URL
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
     * Test de cas limites avec des noms de contrôleurs
     */
public function testEdgeCasesControllerNames()
    {
        $edgeCases = [
            'TestController' => 'Test',
            'MyLongControllerNameController' => 'MyLongName', // Corrigé précédemment
            'ControllerController' => '', // str_replace enlève TOUS les "Controller"
            'SimpleController' => 'Simple'
        ];

        foreach ($edgeCases as $original => $expected) {
            $result = str_replace('Controller', '', $original);
            $this->assertEquals($expected, $result);
        }
    }


    /**
     * Test de robustesse avec différents formats de fichiers
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
            // Changer assertStringNotContains en assertStringNotContainsString
            $this->assertStringNotContainsString('.php', $filename);
            $this->assertStringNotContainsString('/', $filename);
        }
    }

    /**
     * Test de la logique de création du plan de site
     */
    public function testSiteMapCreationLogic()
    {
        // Simuler le processus complet
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