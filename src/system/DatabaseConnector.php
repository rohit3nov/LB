<?php

namespace App\system;

// use mysqli;

class DatabaseConnector {

    private $connection = null;

    public function __construct()
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $db   = getenv('DB_DATABASE');
        $user = getenv('DB_USERNAME');
        $pass = getenv('DB_PASSWORD');

        $conn = new \mysqli($host,$user,$pass,$db,$port);
        $conn->options(MYSQLI_OPT_LOCAL_INFILE,true);
        if ($conn->connect_error) {
            die("Error: Could not connect to database : " . $conn->connect_error);
        }
        $this->connection = $conn;
    }

    public function getConnection()
    {
        return $this->connection;
    }

}