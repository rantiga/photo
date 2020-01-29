<?php

namespace app\models;

use app\entities\User;
use PDO;

class UserMapper
{
    protected $pdo;
    protected $user;

    public function __construct(PDO $pdo, User $user)
    {
        $this->pdo = $pdo;
        $this->user = $user;
    }

    public function getUserData()
    {
        $state = $this->pdo->prepare('SELECT * FROM users WHERE login = :login AND password = :password');

        $executeValues = [
            ':login' => $this->user->getLogin(),
            ':password' => $this->user->getPassword()
        ];

        if (!$this->checkEmptiness($executeValues)) {
            return [];
        }

        $state->execute($executeValues);

        $result = $state->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    public function authorization(): array
    {
        $state = $this->pdo->prepare('SELECT * FROM users WHERE id = :id AND login = :login AND password = :password');

        $executeValues = [
            ':id' => $this->user->getId(),
            ':login' => $this->user->getLogin(),
            ':password' => $this->user->getPassword()
        ];

        if (!$this->checkEmptiness($executeValues)) {
            return [];
        }

        $state->execute($executeValues);

        $result = $state->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function getByLogin(): array
    {
        $state = $this->pdo->prepare('SELECT * FROM users WHERE login = :login');

        $executeValues = [
            ':login' => $this->user->getLogin()
        ];

        if (!$this->checkEmptiness($executeValues)) {
            return [];
        }

        $state->execute($executeValues);

        $result = $state->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function create(): string
    {
        $state = $this->pdo->prepare(
            'INSERT INTO users (login, password) VALUES (:login, :password)');

        $executeValues = [
            ':login' => $this->user->getLogin(),
            ':password' => $this->user->getPassword()
        ];

        if (!$this->checkEmptiness($executeValues)) {
            return '';
        }

        $state->execute($executeValues);
        $state->fetch();

        return $this->pdo->lastInsertId();
    }

    protected function checkEmptiness(array $executeValues): bool
    {
        foreach ($executeValues as $row => $value) {
            if (empty($value)) {
                return false;
            }
        }

        return true;
    }
}