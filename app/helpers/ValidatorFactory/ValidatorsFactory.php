<?php

namespace app\helpers\ValidatorFactory;

use app\entities\Image;
use app\entities\User;
use app\helpers\FactoryInterface;
use app\validators\ImageValidator;
use app\validators\UserValidator;

class ValidatorsFactory implements FactoryInterface
{
    public function getImageValidator(Image $entity)
    {
        return new ImageValidator($entity);
    }

    public function getUserValidator(User $entity)
    {
        return new UserValidator($entity);
    }
}