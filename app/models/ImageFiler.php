<?php

namespace app\models;

use app\entities\Image;
use app\httpSenders\ExceptionResponse;

class ImageFiler
{
    protected $image;

    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    public function saveImage(): void
    {
        $dirPath = $_SERVER['DOCUMENT_ROOT'] . '/images';

        if (!dir($dirPath)) {
            throw new ExceptionResponse('Something broke', '500');
        }

        $imageName = 'image_' . rand(1000, 99999) . date('dmY') . '.' . basename($this->image->getMimeType());
        $imagePath = '/images/' . $imageName;

        if (!move_uploaded_file($this->image->getPath(), $dirPath . '/' . $imageName)) {
            return;
        }

        $this->image->setPath($imagePath);

        return;
    }

    public function deleteImage(): void
    {
        unlink($_SERVER['DOCUMENT_ROOT'] . '/' .$this->image->getPath());
    }
}