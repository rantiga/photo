<?php

namespace app\models;

use app\entities\Image;
use app\ExceptionResponse;
use PDO;

class ImageMapper
{
    protected $pdo;
    protected $image;

    public function __construct(PDO $pdo, Image $image)
    {
        $this->pdo = $pdo;
        $this->image = $image;
    }

    public function save(): void
    {
        $state = $this->pdo->prepare(
            'INSERT INTO images (user_id, path, cast, header, description, img_type) 
                      VALUES (:user_id, :path, :cast, :header, :description, :img_type)'
        );

        $executeValues = [
            ':user_id' => $this->image->getUserId(),
            ':path' => $this->image->getPath(),
            ':cast' => $this->image->getCast(),
            ':header' => $this->image->getName(),
            ':description' => $this->image->getDescription(),
            ':img_type' => $this->image->getMimeType()
        ];

        if (!$this->checkEmptiness($executeValues)) {
            return;
        }

        $state->execute($executeValues);

        $state->fetch();

        return;
    }

    public function getById(): array
    {
        $state = $this->pdo->prepare('SELECT * FROM images WHERE id = :id');

        $executeValues = [
            ':id' => $this->image->getId()
        ];

        if (!$this->checkEmptiness($executeValues)) {
            return [];
        }

        $state->execute($executeValues);

        $result = $state->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function getByUserId(string $searchName, string $searchDescription): array
    {
        $state = $this->pdo->prepare('SELECT * FROM images WHERE user_id = :user_id 
        AND header LIKE "%' . $searchName . '%" 
        AND description LIKE "%' . $searchDescription . '%"'
        );

        $executeValues = [
            ':user_id' => $this->image->getUserId()
        ];

        if (!$this->checkEmptiness($executeValues)) {
            return [];
        }

        $state->execute($executeValues);

        $result = $state->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function searchCast()
    {
        $state = $this->pdo->prepare('SELECT * FROM images WHERE cast = :cast');

        $executeValues = [
            ':cast' => $this->image->getCast()
        ];

        if (!$this->checkEmptiness($executeValues)) {
            return;
        }

        $state->execute($executeValues);

        return $state->fetch(PDO::FETCH_ASSOC);
    }

    public function searchPath()
    {
        $state = $this->pdo->prepare('SELECT * FROM images WHERE path = :path');

        $executeValues = [
            ':path' => $this->image->getPath()
        ];

        if (!$this->checkEmptiness($executeValues)) {
            return;
        }

        $state->execute($executeValues);

        $result = $state->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function delete(): void
    {
        $state = $this->pdo->prepare('DELETE FROM images WHERE user_id = :user_id AND id = :id');

        $executeParams = [
            ':id' => $this->image->getId(),
            ':user_id' => $this->image->getUserId()
        ];

        if (!$this->checkEmptiness($executeParams)) {
            return;
        }

        $state->execute($executeParams);

        $state->fetch(PDO::FETCH_ASSOC);

        return;
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