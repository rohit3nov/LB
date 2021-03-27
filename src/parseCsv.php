<?php

require __DIR__.'/../bootstrap.php';

use App\services\CsvParser;
use App\services\CsvUpdator;
use App\system\DatabaseConnector;

// storage/worldcities.csv parser
$parser  = new CsvParser();

// proceed only if csv exists
if (!$parser->checkIfCsvExists()) {
    echo 'Error: '.$parser->getCsvName()." doesn't exist \n";
    exit;
}

// proceed only if csv is modified
if (!$parser->checkIfCsvIsModified()) {
    echo 'Error: No Changes detected in '.$parser->getCsvName()."\n";
    exit;
}

$start_time = microtime(true);
echo 'Processing '.$parser->getCsvName().'...';

// store worldcities.csv in database
$dbConnector = new DatabaseConnector();
if (!$parser->loadCsvInDb($dbConnector)) {
    echo "\nError: Could not parse ".$parser->getCsvName().".Something went wrong. \n";
    exit;
}

// get asean city data from db
$parser->getAseanCitiesFromWorldCities($dbConnector);

// storage/cities.csv updator
$updator = new CsvUpdator($parser);

$bus = \League\Tactician\Setup\QuickStart::create([
    CsvParser::class => $updator
]);
echo "Done!\n";

echo "Now Updating ".$updator->getCsvName()." with data from ASEAN cities...";

/** UPDATING storage/cities.csv */
if (!empty($parser->getAseanCities())) {
    $bus->handle($parser);
}


// create a csv hash once fully processed, to prevent processing same csv
$parser->createCsvHashFile();

echo "Done!\n";

$end_time = microtime(true);
echo round( $end_time - $start_time,4)." seconds \n";