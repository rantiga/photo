<?php

namespace app\helpers\ModelsFactory;

use app\entities\Image;
use app\entities\User;
use app\helpers\FactoryInterface;
use app\models\HashMapper;
use app\models\ImageCaster;
use app\models\ImageMapper;
use app\models\ImageFiler;
use app\models\UserMapper;
use PDO;

class ModelsFactory implements FactoryInterface
{
    public function getImageCaster(Image $image)
    {
        return new ImageCaster($image);
    }

    public function getImageMapper(PDO $pdo, Image $image)
    {
        return new ImageMapper($pdo, $image);
    }

    public function getImageFiler(Image $image)
    {
        return new ImageFiler($image);
    }

    public function getUserMapper(PDO $pdo, User $user)
    {
        return new UserMapper($pdo, $user);
    }

    public function getHashMapper(PDO $pdo)
    {
        return new HashMapper($pdo);
    }
}