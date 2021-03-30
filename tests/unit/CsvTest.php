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
        $this->tester->amConnectedToDatabase('lb');
    }
    public function testIfWorldCitiesTableEmpty()
    {
        $this->tester->seeNumRecords(0,$this->parser->getCsvTableName());
    }

    public function testIfCsvCanBeLoadedInDb()
    {
        $dbConf = array(
            'dsn' => 'mysql:host=lb-db;dbname=lb',
            'user' => 'root',
            'password' => 'root_pass'
        );
        $query = "  LOAD DATA LOCAL INFILE '".$this->parser->getStoragePath().$this->parser->getCsvName()."' INTO TABLE ".$this->parser->getCsvTableName()."
                    FIELDS TERMINATED BY '\,'
                    ENCLOSED BY '\''
                    LINES TERMINATED BY '\n'
                    IGNORE 1 LINES
                    ( ".str_replace('"','',$this->parser->getCsvHeader()).");";

        $db = $this->getModule("Db");
        $options = array(\PDO::MYSQL_ATTR_LOCAL_INFILE => 1);
        $db->drivers['lb'] = Codeception\Lib\Driver\Db::create($dbConf['dsn'],$dbConf['user'],$dbConf['password'],$options);
        $db->drivers['lb']->load(array($query));

        $this->tester->seeNumRecords($this->parser->getRowCount(),$this->parser->getCsvTableName());
    }

    public function testIfAnyAseanCitiesFoundInDB()
    {
        foreach ($this->parser->getAseanCountries() as $country) {
            $id = $this->tester->grabFromDatabase($this->parser->getCsvTableName(),'id',array('country'=>$country));
            if($id > 0){
                break;
            }
        }
        $this->assertGreaterThan(0,$id);
    }

}