<?php

namespace app\models;

use app\helpers\DatabaseConnection;
use app\helpers\EntityFactory\EntityFactory;
use app\helpers\ModelsFactory\ModelsFactory;
use app\httpSenders\ExceptionResponse;

class UserAuthorization
{
    public function authorization($userId, $login, $password)
    {
        if (empty($login) || empty($password)) {
            throw new ExceptionResponse('Access denied', '401');
        }

        $entityFactory = new EntityFactory();
        $modelsFactory = new ModelsFactory();
        $dataBase = new DatabaseConnection();
        $connection = $dataBase->getConnection();

        $user = $entityFactory->getUserEntity();

        $user->setId($userId)->setLogin($login)->setPassword(md5($password));

        $userMapper = $modelsFactory->getUserMapper($connection, $user);

        if (empty($userMapper->authorization())) {
            throw new ExceptionResponse('Access denied', '401');
        }
    }
}