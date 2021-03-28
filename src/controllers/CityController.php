<?php

namespace App\controllers;

use App\services\CsvParser;
use App\services\CsvUpdator;
use App\system\DatabaseConnector;

class CityController
{
    private $cityData;
    private $csvParser;

    public function __construct(CsvParser $parser,array $cityData)
    {
        $this->csvParser = $parser;
        $this->cityData  = $cityData;
    }

    public function addCity() : void
    {
        $dbConnector = new DatabaseConnector();
        $dbData = $this->csvParser->getCityDataFromDb($dbConnector,array($this->cityData['city_id']));

        if (!isset($dbData[$this->cityData['city_id']]) && $this->csvParser->checkIfCsvExists()) {
            // refresh the db from csv before updating the city details
            $this->csvParser->loadCsvInDb($dbConnector);
        }

        // replace the city data into db
        $this->csvParser->updateCityInDb($dbConnector,$this->cityData);

        // refresh the worlcities.csv with latest updates
        $this->csvParser->loadDbInCsv($dbConnector);

        // check if the city belongs to asean country, if yes then refresh the cities.csv as well
        if (in_array(strtolower($this->cityData['country']),$this->csvParser->getAseanCountries())) {
            $this->csvParser->getAseanCitiesFromWorldCities($dbConnector);
            $updator = new CsvUpdator();
            $updator->handle($this->csvParser);
        }
    }


}