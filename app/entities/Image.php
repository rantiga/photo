<?php

namespace app\entities;

class Image implements Entity
{
    protected $id;
    protected $userId;
    protected $path;
    protected $cast;
    protected $name;
    protected $description;
    protected $mimeType;
    protected $hash;

    public function setId(string $id): Image
    {
        $this->id = $id;
        return $this;
    }

    public function setUserId(string $userId): Image
    {
        $this->userId = $userId;
        return $this;
    }

    public function setPath(string $path): Image
    {
        $this->path = $path;
        return $this;
    }

    public function setCast(string $cast): Image
    {
        $this->cast = $cast;
        return $this;
    }

    public function setName(string $name): Image
    {
        $this->name = $name;
        return $this;
    }

    public function setDescription(string $description): Image
    {
        $this->description = $description;
        return $this;
    }

    public function setMimeType(string $mimeType): Image
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function setHash(string $hash): Image
    {
        $this->hash = $hash;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getCast()
    {
        return $this->cast;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function getHash()
    {
        return $this->hash;
    }
}