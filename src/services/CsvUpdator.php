<?php

namespace App\services;

use League\Csv\Writer;

class CsvUpdator
{
    private $storagePath     = __DIR__.'/../../storage/';
    private $csvName         = 'cities.csv';

    private $csv;
    private $csvStream;

    public function getCsvName() : string
    {
        return $this->csvName;
    }
    public function handle(CsvParser $parser)
    {
        if (!file_exists($this->storagePath.$this->csvName)) {
            touch($this->storagePath.$this->csvName);
        }

        $this->csvStream  = fopen($this->storagePath.$this->csvName, 'w');
        $this->csv        = Writer::createFromStream($this->csvStream);

        //insert the header
        $this->csv->insertOne(explode(',',str_replace('"','',$parser->getCsvHeader())));

        //insert all the records
        $this->csv->insertAll($parser->getAseanCities());
    }
}