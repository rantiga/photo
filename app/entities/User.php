<?php

namespace app\entities;

class User implements Entity
{
    protected $id;
    protected $login;
    protected $password;

    public function setId(string $id): Entity
    {
        $this->id = $id;

        return $this;
    }

    public function setLogin(string $login): Entity
    {
        $this->login = $login;

        return $this;
    }

    public function setPassword(string $password): Entity
    {
        $this->password = $password;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}