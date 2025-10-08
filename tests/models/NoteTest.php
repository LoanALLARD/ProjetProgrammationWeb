<?php

use PHPUnit\Framework\TestCase;

class NoteTest extends TestCase
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
     * Test that Note class exists
     */
    public function testNoteClassExists()
    {
        $noteFile = __DIR__ . '/../../app/models/Note.php';
        $this->assertFileExists($noteFile);
        
        require_once $noteFile;
        $this->assertTrue(class_exists('Note'));
    }

    /**
     * Test note creation with valid data
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
     * Test note creation with partial data
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
     * Test note creation with empty array
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
     * Test hydration with different keys
     */
    public function testHydrationWithDifferentKeys()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $data = [
            'titre' => 'Test Hydratation',
            'contenue' => 'Test contenu',
            'date' => '2024-01-15',
            'invalid_key' => 'Should be ignored' // This key will be ignored
        ];
        
        $note = new Note($data);
        
        $this->assertEquals('Test Hydratation', $note->getTitre());
        $this->assertEquals('Test contenu', $note->getContenue());
        $this->assertEquals('2024-01-15', $note->getDate());
    }

    /**
     * Test setTitre setter with valid string
     */
    public function testSetTitreWithValidString()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $note = new Note([]);
        $note->setTitre('Nouveau titre');
        
        $this->assertEquals('Nouveau titre', $note->getTitre());
    }

    /**
     * Test setTitre setter with invalid type
     */
    public function testSetTitreWithInvalidType()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $note = new Note([]);
        $originalTitre = $note->getTitre();
        
        // Try to set with non-string type
        $note->setTitre(123); // int
        $note->setTitre([]); // array
        $note->setTitre(null); // null
        
        // Title should not change
        $this->assertEquals($originalTitre, $note->getTitre());
    }

    /**
     * Test setContenue setter with valid string
     */
    public function testSetContenueWithValidString()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $note = new Note([]);
        $note->setContenue('Nouveau contenu de la note');
        
        $this->assertEquals('Nouveau contenu de la note', $note->getContenue());
    }

    /**
     * Test setContenue setter with invalid type
     */
    public function testSetContenueWithInvalidType()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $note = new Note([]);
        $originalContenue = $note->getContenue();
        
        // Try to set with non-string type
        $note->setContenue(456); // int
        $note->setContenue(true); // boolean
        $note->setContenue([]); // array
        
        // Content should not change
        $this->assertEquals($originalContenue, $note->getContenue());
    }

    /**
     * Test setDate setter
     */
    public function testSetDate()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $note = new Note([]);
        
        // Test with string
        $note->setDate('2024-01-15 14:30:00');
        $this->assertEquals('2024-01-15 14:30:00', $note->getDate());
        
        // Test with timestamp
        $note->setDate(1642251000);
        $this->assertEquals(1642251000, $note->getDate());
        
        // Test with null
        $note->setDate(null);
        $this->assertNull($note->getDate());
    }

    /**
     * Test getters with null values
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
     * Test hydration with ucfirst logic on keys
     */
    public function testHydrationUcfirstLogic()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        // Test ucfirst logic
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
     * Test method_exists in hydration
     */
    public function testMethodExistsInHydration()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $note = new Note([]);
        
        // Methods that should exist
        $this->assertTrue(method_exists($note, 'setTitre'));
        $this->assertTrue(method_exists($note, 'setContenue'));
        $this->assertTrue(method_exists($note, 'setDate'));
        
        // Methods that should not exist
        $this->assertFalse(method_exists($note, 'setInvalidMethod'));
        $this->assertFalse(method_exists($note, 'setNonExistent'));
    }

    /**
     * Test with realistic database data
     */
    public function testWithRealisticDatabaseData()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        // Simulate data coming from DB
        $dbData = [
            'id' => 15,
            'titre' => 'Réunion équipe projet',
            'contenue' => 'Points à aborder : budget, planning, ressources humaines',
            'date' => '2024-01-15 09:30:00',
            'user_id' => 42
        ];
        
        $note = new Note($dbData);
        
        // Only properties defined in the class should be affected
        $this->assertEquals('Réunion équipe projet', $note->getTitre());
        $this->assertEquals('Points à aborder : budget, planning, ressources humaines', $note->getContenue());
        $this->assertEquals('2024-01-15 09:30:00', $note->getDate());
    }

    /**
     * Test data type validation
     */
    public function testDataTypeValidation()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $note = new Note([
            'titre' => 'Test titre',
            'contenue' => 'Test contenu',
            'date' => '2024-01-15'
        ]);
        
        // Check that getters return the right types
        $this->assertIsString($note->getTitre());
        $this->assertIsString($note->getContenue());
        $this->assertIsString($note->getDate());
    }

    /**
     * Test with empty strings
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
     * Test with strings containing special characters
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
     * Test modification after creation
     */
    public function testModificationAfterCreation()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        $initialData = [
            'titre' => 'Titre initial',
            'contenue' => 'Contenu initial'
        ];
        
        $note = new Note($initialData);
        
        // Modify after creation
        $note->setTitre('Titre modifié');
        $note->setContenue('Contenu modifié');
        $note->setDate('2024-01-16 15:45:00');
        
        $this->assertEquals('Titre modifié', $note->getTitre());
        $this->assertEquals('Contenu modifié', $note->getContenue());
        $this->assertEquals('2024-01-16 15:45:00', $note->getDate());
    }

    /**
     * Test edge cases for hydration
     */
    public function testHydrationEdgeCases()
    {
        require_once __DIR__ . '/../../app/models/Note.php';
        
        // Test with uppercase keys (should not work)
        $data = [
            'TITRE' => 'TITRE MAJUSCULE',
            'titre' => 'titre minuscule'
        ];
        
        $note = new Note($data);
        
        // Only the lowercase key should work
        $this->assertEquals('titre minuscule', $note->getTitre());
    }

    /**
     * Test performance with large data
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