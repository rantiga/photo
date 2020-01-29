<?php

namespace app\helpers\EntityFactory;

use app\entities\Image;
use app\entities\User;
use app\helpers\FactoryInterface;

class EntityFactory implements FactoryInterface
{
    public function getImageEntity()
    {
        return new Image();
    }

    public function getUserEntity()
    {
        return new User();
    }
}