<?php

namespace app\models;

use app\entities\Image;

class ImageCaster
{
    protected $image;

    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    public function cast()
    {
        $imageHash = hash_file('md5', $this->image->getPath());
        $this->image->setCast($imageHash);
    }
}