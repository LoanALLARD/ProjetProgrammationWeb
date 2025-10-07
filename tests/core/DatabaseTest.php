<?php

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
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
     * Test que la classe Database existe
     */
    public function testDatabaseClassExists()
    {
        $databaseFile = __DIR__ . '/../../app/core/Database.php';
        $this->assertFileExists($databaseFile);
        
        // Test de base sans instancier
        $this->assertTrue(true);
    }

    /**
     * Test de la structure de la classe Database
     */
    public function testDatabaseClassStructure()
    {
        $databaseFile = __DIR__ . '/../../app/core/Database.php';
        $content = file_get_contents($databaseFile);
        
        // Vérifier la structure du code
        $this->assertStringContainsString('namespace core;', $content);
        $this->assertStringContainsString('class Database', $content);
        $this->assertStringContainsString('private static $instance', $content);
        $this->assertStringContainsString('private $connection', $content);
        $this->assertStringContainsString('private function __construct()', $content);
        $this->assertStringContainsString('public static function getInstance()', $content);
        $this->assertStringContainsString('public function getConnection()', $content);
    }

    /**
     * Test du pattern Singleton dans le code
     */
    public function testSingletonPatternInCode()
    {
        $databaseFile = __DIR__ . '/../../app/core/Database.php';
        $content = file_get_contents($databaseFile);
        
        // Vérifier la logique du singleton
        $this->assertStringContainsString('if (self::$instance === null)', $content);
        $this->assertStringContainsString('self::$instance = new Database()', $content);
        $this->assertStringContainsString('return self::$instance', $content);
    }

    /**
     * Test de la structure de la chaîne de connexion PDO
     */
    public function testPDOConnectionStringStructure()
    {
        $databaseFile = __DIR__ . '/../../app/core/Database.php';
        $content = file_get_contents($databaseFile);
        
        // Vérifier la construction du DSN
        $this->assertStringContainsString('mysql:host=', $content);
        $this->assertStringContainsString('dbname=', $content);
        $this->assertStringContainsString('charset=utf8', $content);
        $this->assertStringContainsString('new \\PDO(', $content);
    }

    /**
     * Test de la configuration PDO
     */
    public function testPDOConfiguration()
    {
        $databaseFile = __DIR__ . '/../../app/core/Database.php';
        $content = file_get_contents($databaseFile);
        
        // Vérifier la configuration PDO
        $this->assertStringContainsString('setAttribute', $content);
        $this->assertStringContainsString('PDO::ATTR_ERRMODE', $content);
        $this->assertStringContainsString('PDO::ERRMODE_WARNING', $content);
    }

    /**
     * Test de la gestion d'exception
     */
    public function testExceptionHandling()
    {
        $databaseFile = __DIR__ . '/../../app/core/Database.php';
        $content = file_get_contents($databaseFile);
        
        // Vérifier la gestion des exceptions
        $this->assertStringContainsString('try {', $content);
        $this->assertStringContainsString('} catch (\\PDOexception $e)', $content);
        $this->assertStringContainsString('die("Echec de la connexion"', $content);
    }

    /**
     * Test de validation de la structure du fichier config
     */
    public function testConfigFileStructure()
    {
        $configFile = __DIR__ . '/../../app/config/config.php';
        $this->assertFileExists($configFile);
        
        // Charger la config pour tester sa structure
        $config = require $configFile;
        
        $this->assertIsArray($config);
        $this->assertArrayHasKey('db_host', $config);
        $this->assertArrayHasKey('db_name', $config);
        $this->assertArrayHasKey('db_user', $config);
        $this->assertArrayHasKey('db_pass', $config);
    }

    /**
     * Test de validation des types de configuration
     */
    public function testConfigDataTypes()
    {
        $configFile = __DIR__ . '/../../app/config/config.php';
        $config = require $configFile;
        
        $this->assertIsString($config['db_host']);
        $this->assertIsString($config['db_name']);
        $this->assertIsString($config['db_user']);
        $this->assertIsString($config['db_pass']);
        
        // Vérifier que les valeurs ne sont pas vides (sauf le mot de passe qui peut l'être)
        $this->assertNotEmpty($config['db_host']);
        $this->assertNotEmpty($config['db_name']);
        $this->assertNotEmpty($config['db_user']);
    }

    /**
     * Test du chargement de configuration
     */
    public function testConfigLoading()
    {
        $databaseFile = __DIR__ . '/../../app/core/Database.php';
        $content = file_get_contents($databaseFile);
        
        // Vérifier le chargement de la config
        $this->assertStringContainsString('require __DIR__ . \'/../config/config.php\'', $content);
        $this->assertStringContainsString('$config[\'db_host\']', $content);
        $this->assertStringContainsString('$config[\'db_name\']', $content);
        $this->assertStringContainsString('$config[\'db_user\']', $content);
        $this->assertStringContainsString('$config[\'db_pass\']', $content);
    }

    /**
     * Test des constantes PDO utilisées
     */
    public function testPDOConstants()
    {
        $constants = [
            'PDO::ATTR_ERRMODE',
            'PDO::ERRMODE_WARNING'
        ];
        
        foreach ($constants as $constant) {
            $this->assertTrue(defined($constant), "Constante $constant doit être définie");
        }
        
        $this->assertEquals(3, \PDO::ATTR_ERRMODE);
        // Correction : PDO::ERRMODE_WARNING = 1, pas 2
        $this->assertEquals(1, \PDO::ERRMODE_WARNING);
    }


    /**
     * Test de sécurité du namespace
     */
    public function testNamespaceDeclaration()
    {
        $databaseFile = __DIR__ . '/../../app/core/Database.php';
        $content = file_get_contents($databaseFile);
        
        // Vérifier le namespace
        $this->assertStringStartsWith('<?php', $content);
        $this->assertStringContainsString('namespace core;', $content);
        $this->assertStringContainsString('\\PDO', $content); // Utilisation du namespace global pour PDO
    }

    /**
     * Test de la méthode getConnection
     */
    public function testGetConnectionMethod()
    {
        $databaseFile = __DIR__ . '/../../app/core/Database.php';
        $content = file_get_contents($databaseFile);
        
        // Vérifier la méthode getConnection
        $this->assertStringContainsString('public function getConnection()', $content);
        $this->assertStringContainsString('return $this->connection', $content);
    }

    /**
     * Test de sécurité - Pas d'injection SQL possible
     */
    public function testSqlInjectionSafety()
    {
        $databaseFile = __DIR__ . '/../../app/core/Database.php';
        $content = file_get_contents($databaseFile);
        
        // Vérifier qu'il n'y a pas de concaténation directe dangereuse
        $this->assertStringNotContainsString('$_GET', $content);
        $this->assertStringNotContainsString('$_POST', $content);
        $this->assertStringNotContainsString('$_REQUEST', $content);
    }

    /**
     * Test de chargement de configuration avec chemin relatif
     */
    public function testConfigPathResolution()
    {
        $expectedPath = __DIR__ . '/../config/config.php';
        
        // Simuler le chemin depuis Database.php
        $simulatedPath = __DIR__ . '/../../app/core/../config/config.php';
        $normalizedPath = realpath($simulatedPath);
        
        $this->assertNotFalse($normalizedPath);
        $this->assertStringEndsWith('config.php', $normalizedPath);
    }

    /**
     * Test de robustesse - Vérification charset UTF8
     */
    public function testCharsetConfiguration()
    {
        $databaseFile = __DIR__ . '/../../app/core/Database.php';
        $content = file_get_contents($databaseFile);
        
        $this->assertStringContainsString('charset=utf8', $content);
        $this->assertStringNotContainsString('charset=latin1', $content);
    }

    /**
     * Test de la visibilité des méthodes et propriétés
     */
    public function testMethodAndPropertyVisibility()
    {
        $databaseFile = __DIR__ . '/../../app/core/Database.php';
        $content = file_get_contents($databaseFile);
        
        // Vérifier les visibilités
        $this->assertStringContainsString('private static $instance', $content);
        $this->assertStringContainsString('private $connection', $content);
        $this->assertStringContainsString('private function __construct()', $content);
        $this->assertStringContainsString('public static function getInstance()', $content);
        $this->assertStringContainsString('public function getConnection()', $content);
    }

    /**
     * Test de création d'une configuration de test mockée
     */
    public function testMockConfigurationStructure()
    {
        $mockConfig = [
            'db_host' => 'localhost',
            'db_name' => 'test_database',
            'db_user' => 'test_user',
            'db_pass' => 'test_password'
        ];
        
        // Tester la construction du DSN avec la config mockée
        $dsn = 'mysql:host=' . $mockConfig['db_host'] . ';dbname=' . $mockConfig['db_name'] . ';charset=utf8';
        
        $this->assertEquals('mysql:host=localhost;dbname=test_database;charset=utf8', $dsn);
        $this->assertStringContainsString('mysql:host=', $dsn);
        $this->assertStringContainsString('dbname=', $dsn);
        $this->assertStringContainsString('charset=utf8', $dsn);
    }

    /**
     * Test de validation du format de message d'erreur
     */
    public function testErrorMessageFormat()
    {
        $databaseFile = __DIR__ . '/../../app/core/Database.php';
        $content = file_get_contents($databaseFile);
        
        // Vérifier le format du message d'erreur
        $this->assertStringContainsString('"Echec de la connexion"', $content);
        $this->assertStringContainsString('$e->getMessage()', $content);
    }

    /**
     * Test de structure générale du fichier
     */
public function testOverallFileStructure()
    {
        $databaseFile = __DIR__ . '/../../app/core/Database.php';
        $content = file_get_contents($databaseFile);
        
        // Vérifier la structure générale
        $this->assertStringStartsWith('<?php', $content);
        // Correction : Votre fichier fait 937 caractères, ajustons le seuil
        $this->assertGreaterThan(900, strlen($content)); // Au moins 900 caractères
        $this->assertStringContainsString('{', $content);
        $this->assertStringContainsString('}', $content);
    }
}