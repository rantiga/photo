<?php

namespace app\models;

use PDO;

class HashMapper
{
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getImage($hash)
    {
        $state = $this->pdo->prepare('SELECT * FROM images WHERE hash = :hash');

        $executeValues = [
            ':hash' => $hash
        ];

        $state->execute($executeValues);

        $result = $state->fetch(PDO::FETCH_ASSOC);

        return $result;
    }
}