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
            'INSERT INTO images (user_id, path, cast, header, description, img_type, hash) 
                      VALUES (:user_id, :path, :cast, :header, :description, :img_type, :hash)'
        );

        $executeValues = [
            ':user_id' => $this->image->getUserId(),
            ':path' => $this->image->getPath(),
            ':cast' => $this->image->getCast(),
            ':header' => $this->image->getName(),
            ':description' => $this->image->getDescription(),
            ':img_type' => $this->image->getMimeType(),
            ':hash' => $this->image->getHash()
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
        $state = $this->pdo->prepare('SELECT * FROM images WHERE id = :id AND user_id = :user_id');

        $executeValues = [
            ':id' => $this->image->getId(),
            ':user_id' => $this->image->getUserId()
        ];

        if (!$this->checkEmptiness($executeValues)) {
            return [];
        }

        $state->execute($executeValues);

        $result = $state->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function getByUserId(
        string $searchName = NULL,
        string $searchDescription = NULL,
        string $sorting = NULL,
        string $startLimit = NULL,
        string $endLimit = NULL
    ): array
    {
        $sql = 'SELECT * FROM images WHERE user_id = :user_id';

        $executeValues = [
            ':user_id' => $this->image->getUserId(),
        ];

        if (!$this->checkEmptiness($executeValues)) {
            return [];
        }

        if (!empty($searchName)) {
            $sql .= ' AND header LIKE "%' . $searchName . '%"';
        }

        if (!empty($searchDescription)) {
            $sql .= ' AND description LIKE "%' . $searchDescription . '%"';
        }

        if (!empty($sorting) && ($sorting == 'asc' || $sorting == 'desc')) {
            $sql .= ' ORDER BY header ' . $sorting;
        }

        if (isset($startLimit) && isset($endLimit)) {
            $sql .= ' LIMIT ' . $startLimit . ',' . $endLimit;
        } else {
            $sql .= ' LIMIT 0,10';
        }

        $state = $this->pdo->prepare($sql);
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

    public function changeImage(): bool
    {
        $state = $this->pdo->prepare('UPDATE images SET header = :header, description = :description WHERE user_id = :user_id AND id = :id');

        $executeValues = [
            ':header' => $this->image->getName(),
            ':description' => $this->image->getDescription(),
            ':user_id' => $this->image->getUserId(),
            ':id' => $this->image->getId()
        ];

        if (!$this->checkEmptiness($executeValues)) {
            return false;
        }

        $state->execute($executeValues);

        $state->fetch();

        return true;
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