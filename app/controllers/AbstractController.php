<?php

namespace app\controllers;

use app\helpers\DatabaseConnection;
use app\helpers\EntityFactory\EntityFactory;
use app\helpers\ModelsFactory\ModelsFactory;
use app\helpers\SendersFactory\SendersFactory;
use app\helpers\ValidatorFactory\ValidatorsFactory;

class AbstractController
{
    protected $entitiesFactory;
    protected $validatorsFactory;
    protected $modelsFactory;
    protected $sendersFactory;
    protected $pdo;

    public function __construct()
    {
        //Это не должно находится тут
        $this->entitiesFactory = new EntityFactory();
        $this->validatorsFactory = new ValidatorsFactory();
        $this->modelsFactory = new ModelsFactory();
        $this->sendersFactory = new SendersFactory();

        $this->pdo = new DatabaseConnection();
    }
}