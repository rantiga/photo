<?php

namespace app\helpers;

use PDO;

class DatabaseConnection
{
    protected $connection;

    public function __construct()
    {
        $this->connection = new PDO("mysql:host=35.158.89.24;dbname=develop", 'user', 'password');
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
