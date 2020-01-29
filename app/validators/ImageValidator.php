<?php

namespace app\validators;

use app\entities\Image;

class ImageValidator implements ValidatorsInterface
{
    protected $image;
    protected $imageTypes = [
        'image/gif',
        'image/jpeg',
        'image/png',
        'image/svg+xml'
    ];

    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    public function check(): bool
    {
        if (!in_array($this->image->getMimeType(), $this->imageTypes)) {
            return false;
        }

        $this->checkImageOption();

        return true;
    }

    protected function checkImageOption(): void
    {
        $imageName = $this->image->getName();
        $imageDescription = $this->image->getDescription();

        if (strlen($imageName) > 32) {
            $imageName = substr($imageName, 0, 31);
        }

        if (strlen($imageName) == 0) {
            $imageName = 'image_' . rand(1, 9999);
        }

        if (strlen($imageDescription) > 180) {
            $imageDescription = substr($imageDescription, 0, 179);
        }

        $this->image->setName($imageName);
        $this->image->setDescription($imageDescription);

        return;
    }
}