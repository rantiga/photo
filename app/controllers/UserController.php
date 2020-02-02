<?php

namespace app\controllers;

use app\httpSenders\ExceptionResponse;

class UserController extends AbstractController
{
    public function get()
    {
        $user = $this->entitiesFactory->getUserEntity();
        $userMapper = $this->modelsFactory->getUserMapper($this->pdo->getConnection(), $user);

        if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
            throw new ExceptionResponse('Invalid authorization', '500');
        }

        $user->setLogin($_SERVER['PHP_AUTH_USER'])->setPassword(md5($_SERVER['PHP_AUTH_PW']));

        $result = $userMapper->getUserData();

        if (empty($result)) {
            throw new ExceptionResponse('User not found', '500');
        }

        $sender = $this->sendersFactory->getMessageSender();
        $sender->response('200', ['message' => 'OK', 'user_id' => $result['id'], 'user_login' => $result['login']], ['Content-Type: application/json']);
    }

    public function post($values)
    {
        if (empty($values['requestValues']['login']) || empty($values['requestValues']['password'])) {
            throw new ExceptionResponse('Invalid parameters', '500');
        }

        $user = $this->entitiesFactory->getUserEntity();
        $user->setLogin($values['requestValues']['login'])->setPassword($values['requestValues']['password']);

        $userValidator = $this->validatorsFactory->getUserValidator($user);
        $userValidator->check();

        $user->setPassword(md5($user->getPassword()));

        $userMapper = $this->modelsFactory->getUserMapper($this->pdo->getConnection(), $user);

        if ($userMapper->getByLogin()) {
            throw new ExceptionResponse('User already exists', '500');
        }

        $result = $userMapper->create();

        if (empty($result)) {
            throw new ExceptionResponse('Invalid arguments', '500');
        }

        $sender = $this->sendersFactory->getMessageSender();
        $sender->response('201', ['message' => 'OK', 'user_id' => $result], ['Content-Type: application/json']);
    }
}