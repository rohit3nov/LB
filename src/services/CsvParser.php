<?php

namespace App\services;

use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\TabularDataReader;
use App\system\DatabaseConnector;

require __DIR__ . '/../helpers/commonHelper.php';

class CsvParser
{
    private $storagePath     = __DIR__.'/../../storage/';
    private $csvName         = 'worldcities.csv';
    private $tableName       = 'worldCities';
    private $csvHashFolder   = 'Hash';

    private $csv;
    private $csvStream;

    private $aseanCountries = array('brunei','cambodia','indonesia','laos','malaysia','myanmar','philippines','singapore','thailand','vietnam');
    private $aseanCities    = array();
    private $allCities      = array();

    public function __construct()
    {
        if (file_exists($this->storagePath.$this->csvName)) {
            $this->init();
        }
    }
    private function init()
    {
        $this->csvStream  = fopen($this->storagePath.$this->csvName, 'r');
        $this->csv        = Reader::createFromStream($this->csvStream);

        $this->csv->setDelimiter(';');
        $this->csv->setHeaderOffset(0);
    }
    public function getRowCount() : int
    {
        return count($this->csv);
    }

    public function parse() :void
    {
        // parse csv in chunks and collect updates for asean cities
        $count = 0; $offset = 0;$limit = 1000;
        do {
            $chunk   = $this->getCsvRowsInChunk($offset,$limit);
            $count   = $chunk->count();
            $offset += $count;
            if ($count > 0) {
                $this->collectCities($chunk);
            }
        } while ($count > 0);
    }

    public function loadCsvInDb(DatabaseConnector $dbConnector): bool
    {
        $connection = $dbConnector->getConnection();
        if ($this->cleanDbData($connection)) {
            if ($connection) {
                $query = "  LOAD DATA LOCAL INFILE '$this->storagePath$this->csvName' INTO TABLE $this->tableName
                            FIELDS TERMINATED BY '\,'
                            ENCLOSED BY '\''
                            LINES TERMINATED BY '\n'
                            IGNORE 1 LINES
                            ( ".str_replace('"','',$this->getCsvHeader()).");";
                return $connection->query($query);
            }
        }
        return false;
    }

    private function cleanDbData(\mysqli $connection) : bool
    {
        return $connection->query("TRUNCATE TABLE $this->tableName;");
    }

    public function getAseanCitiesFromWorldCities(DatabaseConnector $dbConnector): void
    {
        $connection = $dbConnector->getConnection();
        if ($connection) {
            $query  = 'SELECT * FROM '.$this->tableName.' WHERE country IN ("'.implode('","',$this->aseanCountries).'")';
            $result = $connection->query($query);

            if ($result->num_rows > 0 ) {
                foreach ($result as $city) {
                    unset($city['uid']);
                    $this->aseanCities[] = $city;
                }
            }
        }
        return;
    }

    public function getCityDataFromDb(DatabaseConnector $dbConnector,array $cityIds) : array
    {
        $cityData = array();
        $connection = $dbConnector->getConnection();
        if ($connection) {
            $query    = 'SELECT * FROM '.$this->tableName.' WHERE id IN ("'.implode('","',$cityIds).'")';
            $result = $connection->query($query);

            if ($result->num_rows > 0 ) {
                foreach ($result as $city) {
                    unset($city['uid']);
                    $cityData[$city['id']] = $city;
                }
            }

        }
        return $cityData;
    }

    public function updateCityInDb(DatabaseConnector $dbConnector,array $cityData) : bool
    {
        $connection = $dbConnector->getConnection();
        if ($connection) {
            $columns = $this->getTableColumns($connection);
            if (!empty($columns)) {
                $query  = 'INSERT INTO '.$this->tableName.'('.implode(',',$columns).') VALUES ("'.implode('","',$cityData).'") ON DUPLICATE KEY UPDATE ';
                foreach ($columns as $column) {
                    if ($column !== 'id') {
                        $query .= $column.' = VALUES('.$column.'),';
                    }
                }
                return $connection->query(rtrim($query,','));
            }
        }
        return false;
    }

    private function getTableColumns(\mysqli $connection) : array
    {
        $columns  = array();
        $colQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$this->tableName' AND COLUMN_NAME != 'uid' ORDER BY ORDINAL_POSITION";
        $res = $connection->query($colQuery);
        if ($res->num_rows > 0) {
            foreach ($res as $col) {
                $columns[] = $col['COLUMN_NAME'];
            }
        }
        return $columns;
    }

    public function loadDbInCsv(DatabaseConnector $dbConnector) : bool
    {
        $connection = $dbConnector->getConnection();
        if ($connection) {
             $cities = array();
            // (SELECT ".str_replace('"',''',$this->getCsvHeader()).") UNION
            // $query = "SELECT * FROM $this->tableName INTO OUTFILE '/var/lib/mysql/tmp/$this->csvName' FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\r\n';";

            $query  = 'SELECT * FROM '.$this->tableName;
            $result = $connection->query($query);
            if ($result->num_rows > 0 ) {
                foreach ($result as $city) {
                    unset($city['uid']);
                    $cities[] = $city;
                }
            }
            if (!empty($cities)) {

                if (!file_exists($this->storagePath.$this->csvName)) {
                    touch($this->storagePath.$this->csvName);
                    $this->init();
                }

                $csvWriteStream  = fopen($this->storagePath.$this->csvName, 'w');
                $csv             = \League\Csv\Writer::createFromStream($csvWriteStream);

                //insert the header
                $csv->insertOne($this->getTableColumns($connection));

                //insert all the records
                $csv->insertAll($cities);

                // creating hash of the csv
                $this->createCsvHashFile();
            }
            return true;
        }
        return false;
    }

    // public function __get($property) {
    //     echo $property;exit;
    //     if (property_exists($this, $property)) {
    //         return $this->$property;
    //     }
    // }

    public function getAttr() : string
    {
        return $this->csvName;
    }

    public function getCsvName() : string
    {
        return $this->csvName;
    }

    public function getStoragePath() : string
    {
        return $this->storagePath;
    }

    public function checkIfCsvExists(): bool
    {
        return file_exists($this->storagePath.$this->csvName);
    }

    public function getAseanCities() : array
    {
        return $this->aseanCities;
    }

    public function getAseanCountries() : array
    {
        return $this->aseanCountries;
    }

    public function getCsvHeader() : string
    {
        return $this->csv->getHeader()[0];
    }

    public function getCsvTableName() : string
    {
        return $this->tableName;
    }

    protected function getCsvHash() : string
    {
        return md5($this->csv->getContent());
    }

    public function checkIfCsvIsModified() : bool
    {
        return !file_exists($this->storagePath.$this->csvHashFolder.'/'.$this->getCsvHash().'.txt');
    }

    protected function getCsvRowsInChunk(int $offset,int $limit) : TabularDataReader
    {
        return Statement::create()->offset($offset)->limit($limit)->process($this->csv);
    }

    protected function collectCities(TabularDataReader  $records) : void
    {
        foreach ($records as $record) {
            $cityData = explode(',',str_replace('"','',$record[$this->getCsvHeader()]));
            $country  = strtolower($cityData[4]);

            $this->allCities[] = $cityData;

            // if city belongs to asean country
            if (in_array($country,$this->aseanCountries)) {
                $this->aseanCities[] = $cityData;
            }
        }
    }

    public function createCsvHashFile() :void
    {
        renameDirFile($this->storagePath.$this->csvHashFolder,$this->getCsvHash());
    }
}