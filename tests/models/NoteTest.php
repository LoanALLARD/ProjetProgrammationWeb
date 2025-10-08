<?php

use PHPUnit\Framework\TestCase;

class NoteTest extends TestCase
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
     * Test que la classe Note existe
     */
    public function testNoteClassExists()
    {
        $noteFile = __DIR__ . '/../../app/models/Note.php';
        $this->assertFileExists($noteFile);
        
        require_once $noteFile;
        $this->assertTrue(class_exists('Note'));
    }

    /**
     * Test de création d'une note avec données valides
     */
    public function testNoteCreationWithValidData()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $data = [
            'titre' => 'Ma première note',
            'contenue' => 'Ceci est le contenu de ma note',
            'date' => '2024-01-15 10:30:00'
        ];
        
        $note = new Note($data);
        
        $this->assertInstanceOf(Note::class, $note);
        $this->assertEquals('Ma première note', $note->getTitre());
        $this->assertEquals('Ceci est le contenu de ma note', $note->getContenue());
        $this->assertEquals('2024-01-15 10:30:00', $note->getDate());
    }

    /**
     * Test de création d'une note avec données partielles
     */
    public function testNoteCreationWithPartialData()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $data = [
            'titre' => 'Titre seulement'
        ];
        
        $note = new Note($data);
        
        $this->assertEquals('Titre seulement', $note->getTitre());
        $this->assertNull($note->getContenue());
        $this->assertNull($note->getDate());
    }

    /**
     * Test de création d'une note avec array vide
     */
    public function testNoteCreationWithEmptyArray()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $data = [];
        $note = new Note($data);
        
        $this->assertInstanceOf(Note::class, $note);
        $this->assertNull($note->getTitre());
        $this->assertNull($note->getContenue());
        $this->assertNull($note->getDate());
    }

    /**
     * Test de l'hydratation avec différentes clés
     */
    public function testHydrationWithDifferentKeys()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $data = [
            'titre' => 'Test Hydratation',
            'contenue' => 'Test contenu',
            'date' => '2024-01-15',
            'invalid_key' => 'Should be ignored' // Cette clé sera ignorée
        ];
        
        $note = new Note($data);
        
        $this->assertEquals('Test Hydratation', $note->getTitre());
        $this->assertEquals('Test contenu', $note->getContenue());
        $this->assertEquals('2024-01-15', $note->getDate());
    }

    /**
     * Test du setter setTitre avec string valide
     */
    public function testSetTitreWithValidString()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $note = new Note([]);
        $note->setTitre('Nouveau titre');
        
        $this->assertEquals('Nouveau titre', $note->getTitre());
    }

    /**
     * Test du setter setTitre avec type invalide
     */
    public function testSetTitreWithInvalidType()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $note = new Note([]);
        $originalTitre = $note->getTitre();
        
        // Tenter de définir avec un type non-string
        $note->setTitre(123); // int
        $note->setTitre([]); // array
        $note->setTitre(null); // null
        
        // Le titre ne devrait pas changer
        $this->assertEquals($originalTitre, $note->getTitre());
    }

    /**
     * Test du setter setContenue avec string valide
     */
    public function testSetContenueWithValidString()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $note = new Note([]);
        $note->setContenue('Nouveau contenu de la note');
        
        $this->assertEquals('Nouveau contenu de la note', $note->getContenue());
    }

    /**
     * Test du setter setContenue avec type invalide
     */
    public function testSetContenueWithInvalidType()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $note = new Note([]);
        $originalContenue = $note->getContenue();
        
        // Tenter de définir avec un type non-string
        $note->setContenue(456); // int
        $note->setContenue(true); // boolean
        $note->setContenue([]); // array
        
        // Le contenu ne devrait pas changer
        $this->assertEquals($originalContenue, $note->getContenue());
    }

    /**
     * Test du setter setDate
     */
    public function testSetDate()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $note = new Note([]);
        
        // Test avec string
        $note->setDate('2024-01-15 14:30:00');
        $this->assertEquals('2024-01-15 14:30:00', $note->getDate());
        
        // Test avec timestamp
        $note->setDate(1642251000);
        $this->assertEquals(1642251000, $note->getDate());
        
        // Test avec null
        $note->setDate(null);
        $this->assertNull($note->getDate());
    }

    /**
     * Test des getters avec valeurs nulles
     */
    public function testGettersWithNullValues()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $note = new Note([]);
        
        $this->assertNull($note->getTitre());
        $this->assertNull($note->getContenue());
        $this->assertNull($note->getDate());
    }

    /**
     * Test de l'hydratation avec ucfirst sur les clés
     */
    public function testHydrationUcfirstLogic()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        // Test de la logique ucfirst
        $testCases = [
            'titre' => 'setTitre',
            'contenue' => 'setContenue',
            'date' => 'setDate'
        ];
        
        foreach ($testCases as $key => $expectedMethod) {
            $method = 'set' . ucfirst($key);
            $this->assertEquals($expectedMethod, $method);
        }
    }

    /**
     * Test de method_exists dans l'hydratation
     */
    public function testMethodExistsInHydration()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $note = new Note([]);
        
        // Méthodes qui devraient exister
        $this->assertTrue(method_exists($note, 'setTitre'));
        $this->assertTrue(method_exists($note, 'setContenue'));
        $this->assertTrue(method_exists($note, 'setDate'));
        
        // Méthodes qui ne devraient pas exister
        $this->assertFalse(method_exists($note, 'setInvalidMethod'));
        $this->assertFalse(method_exists($note, 'setNonExistent'));
    }

    /**
     * Test avec des données réalistes de base de données
     */
    public function testWithRealisticDatabaseData()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        // Simuler des données venant de la DB
        $dbData = [
            'id' => 15,
            'titre' => 'Réunion équipe projet',
            'contenue' => 'Points à aborder : budget, planning, ressources humaines',
            'date' => '2024-01-15 09:30:00',
            'user_id' => 42
        ];
        
        $note = new Note($dbData);
        
        // Seules les propriétés définies dans la classe devraient être affectées
        $this->assertEquals('Réunion équipe projet', $note->getTitre());
        $this->assertEquals('Points à aborder : budget, planning, ressources humaines', $note->getContenue());
        $this->assertEquals('2024-01-15 09:30:00', $note->getDate());
    }

    /**
     * Test de validation des types de données
     */
    public function testDataTypeValidation()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $note = new Note([
            'titre' => 'Test titre',
            'contenue' => 'Test contenu',
            'date' => '2024-01-15'
        ]);
        
        // Vérifier que les getters retournent les bons types
        $this->assertIsString($note->getTitre());
        $this->assertIsString($note->getContenue());
        $this->assertIsString($note->getDate());
    }

    /**
     * Test avec des chaînes vides
     */
    public function testWithEmptyStrings()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $data = [
            'titre' => '',
            'contenue' => '',
            'date' => ''
        ];
        
        $note = new Note($data);
        
        $this->assertEquals('', $note->getTitre());
        $this->assertEquals('', $note->getContenue());
        $this->assertEquals('', $note->getDate());
    }

    /**
     * Test avec des chaînes contenant des caractères spéciaux
     */
    public function testWithSpecialCharacters()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $data = [
            'titre' => 'Note avec éà çù characters!',
            'contenue' => 'Contenu avec "guillemets" et \'apostrophes\' & caractères spéciaux @#$%',
            'date' => '2024-01-15 14:30:00'
        ];
        
        $note = new Note($data);
        
        $this->assertEquals('Note avec éà çù characters!', $note->getTitre());
        $this->assertEquals('Contenu avec "guillemets" et \'apostrophes\' & caractères spéciaux @#$%', $note->getContenue());
    }

    /**
     * Test de modification après création
     */
    public function testModificationAfterCreation()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $initialData = [
            'titre' => 'Titre initial',
            'contenue' => 'Contenu initial'
        ];
        
        $note = new Note($initialData);
        
        // Modifier après création
        $note->setTitre('Titre modifié');
        $note->setContenue('Contenu modifié');
        $note->setDate('2024-01-16 15:45:00');
        
        $this->assertEquals('Titre modifié', $note->getTitre());
        $this->assertEquals('Contenu modifié', $note->getContenue());
        $this->assertEquals('2024-01-16 15:45:00', $note->getDate());
    }

    /**
     * Test de cas limites pour l'hydratation
     */
    public function testHydrationEdgeCases()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        // Test avec clés en majuscules (ne devraient pas marcher)
        $data = [
            'TITRE' => 'TITRE MAJUSCULE',
            'titre' => 'titre minuscule'
        ];
        
        $note = new Note($data);
        
        // Seule la clé en minuscule devrait fonctionner
        $this->assertEquals('titre minuscule', $note->getTitre());
    }

    /**
     * Test de performance avec grandes données
     */
    public function testPerformanceWithLargeData()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $largeContent = str_repeat('Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', 1000);
        
        $data = [
            'titre' => 'Note avec beaucoup de contenu',
            'contenue' => $largeContent,
            'date' => '2024-01-15 10:00:00'
        ];
        
        $note = new Note($data);
        
        $this->assertEquals('Note avec beaucoup de contenu', $note->getTitre());
        $this->assertEquals($largeContent, $note->getContenue());
        $this->assertGreaterThan(10000, strlen($note->getContenue()));
    }
}