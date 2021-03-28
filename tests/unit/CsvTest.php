<?php

use App\services\CsvParser;
use App\services\CsvUpdator;
use App\system\DatabaseConnector;

class CsvTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $parser;
    protected $db;

    protected function _before()
    {
        $this->parser = new CsvParser();
        $this->db     = new DatabaseConnector();
    }

    protected function _after()
    {
    }

    // tests
    public function testIfWorldCitiesCsvExists()
    {
        $this->assertTrue($this->parser->checkIfCsvExists());
    }

    public function testIfDatabaseConnected()
    {
        // $this->assertNotNull($this->db->getConnection());
    }

    public function testIfCsvLoadedToDb()
    {

    }

}