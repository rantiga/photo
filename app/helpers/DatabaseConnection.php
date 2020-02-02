<?php

namespace app\helpers;

use PDO;

class DatabaseConnection
{
    protected $connection;

    public function __construct()
    {
        $this->connection = new PDO("mysql:host=192.168.0.6;dbname=develop", 'user', 'password');
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
