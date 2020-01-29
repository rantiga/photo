<?php

namespace app\validators;

use app\entities\User;
use app\httpSenders\ExceptionResponse;

class UserValidator implements ValidatorsInterface
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function check(): void
    {
        if (strlen($this->user->getLogin()) > 32 || strlen($this->user->getPassword()) > 32) {
            throw new ExceptionResponse('Invalid argument', '500');
        }

        return;
    }
}