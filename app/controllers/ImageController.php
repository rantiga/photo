<?php

namespace app\controllers;

class ImageController extends AbstractController
{
    //Слишком много логики в контроллерах...
    public function get($values)
    {
        $image = $this->entitiesFactory->getImageEntity();
        $user = $this->entitiesFactory->getUserEntity();

        $user->setId($values['uriValues']['user_id']);

        $imageMapper = $this->modelsFactory->getImageMapper($this->pdo->getConnection(), $image);

        if (!empty($values['uriValues']['image_id'])) {
            $image->setId($values['uriValues']['image_id']);
            $image->setUserId($values['uriValues']['user_id']);
            $result = $imageMapper->getById();
        } else {
            $image->setUserId($values['uriValues']['user_id']);
            $result = $imageMapper->getByUserId(
                $values['requestValues']['name'],
                $values['requestValues']['description'],
                $values['requestValues']['sort'],
                $values['requestValues']['startLimit'],
                $values['requestValues']['endLimit']
            );
        }

        $sender = $this->sendersFactory->getImageSender();
        $sender->response('200', $result, ['Content-Type: application/json']);
    }

    public function post($values)
    {
        $image = $this->entitiesFactory->getImageEntity();

        $user = $this->entitiesFactory->getUserEntity();
        $user->setId($values['uriValues']['user_id']);

        $image->setUserId($user->getId());

        $imageValidator = $this->validatorsFactory->getImageValidator($image);
        $imageCaster = $this->modelsFactory->getImageCaster($image);

        $imageMapper = $this->modelsFactory->getImageMapper($this->pdo->getConnection(), $image);
        $imageFiler = $this->modelsFactory->getImageFiler($image);

        foreach ($values['files'] as $key => $fileData) {
            $imageInfo = getimagesize($fileData['tmp_name']);
            $imageName = $values['requestValues']['fileInfo'][$key]['name'] ?? '';
	        $imageDescription = $values['requestValues']['fileInfo'][$key]['description'] ?? '';

            $image->
            setName($imageName)->
            setDescription($imageDescription)->
            setMimeType((string)$imageInfo['mime'])->
            setPath($fileData['tmp_name']);

            if (!$imageValidator->check()) {
                continue;
            }

            $imageCaster->cast();

            $result = $imageMapper->searchCast();

            $image->setHash(hash('sha256', $image->getPath() . $image->getCast() . $image->getUserId()));

            if ($result) {
                $image->setPath($result['path']);
                $imageMapper->save();

                continue;
            }

            $imageFiler->saveImage();
            $imageMapper->save();
        }

        $sender = $this->sendersFactory->getMessageSender();
        $sender->response('201', ['message' => 'OK'], ['Content-Type: application/json']);
    }

    public function put($values)
    {
        $sender = $this->sendersFactory->getMessageSender();

        if (empty($values['requestValues']['name']) && empty($values['requestValues']['description'])) {
            $sender->response('500', ['message' => 'Invalid arguments'], ['Content-Type: application/json']);
        }

        $image = $this->entitiesFactory->getImageEntity();
        $image->setUserId($values['uriValues']['user_id'])->setId($values['uriValues']['image_id']);

        $imageMapper = $this->modelsFactory->getImageMapper($this->pdo->getConnection(), $image);

        $foundImage = $imageMapper->getById();

        if (empty($foundImage)) {
            $sender->response('404', ['message' => 'Image not found'], ['Content-Type: application/json']);
        }

        $image->setId($foundImage[0]['id'])->
        setUserId($foundImage[0]['user_id'])->
        setName($foundImage[0]['header'])->
        setDescription($foundImage[0]['description'])->
        setMimeType($foundImage[0]['img_type']);

        if (!empty($values['requestValues']['name'])) {
            $image->setName($values['requestValues']['name']);
        }

        if (!empty($values['requestValues']['description'])) {
            $image->setDescription($values['requestValues']['description']);
        }

        $imageValidator = $this->validatorsFactory->getImageValidator($image);
        $imageValidator->check();

        $imageMapper->changeImage();
        $sender->response('200', ['message' => 'OK'], ['Content-Type: application/json']);
    }

    public function delete($values)
    {
        $user = $this->entitiesFactory->getUserEntity();

        $user->setId($values['uriValues']['user_id']);

        $image = $this->entitiesFactory->getImageEntity();
        $imageMapper = $this->modelsFactory->getImageMapper($this->pdo->getConnection(), $image);
        $imageFiler = $this->modelsFactory->getImageFiler($image);
        $sender = $this->sendersFactory->getMessageSender();

        if (!empty($values['uriValues']['image_id'])) {
            $image->setId($values['uriValues']['image_id']);
            $image->setUserId($values['uriValues']['user_id']);

            $foundImage = $imageMapper->getById();

            if (empty($foundImage)) {
                $sender->response('404', ['message' => 'Image not found'], ['Content-Type: application/json']);
            }

            $image->setPath($foundImage[0]['path']);

            if (count($imageMapper->searchPath()) < 2) {
                $imageFiler->deleteImage();
            }

            $imageMapper->delete();
            $sender->response('200', ['message' => 'OK'], ['Content-Type: application/json']);
        }

        if (!empty($values['requestValues']['delete_id'])) {
            $image->setUserId($values['uriValues']['user_id']);

            foreach ($values['requestValues']['delete_id'] as $index => $id) {
                $image->setId($id);

                $foundImage = $imageMapper->getById();

                if (empty($foundImage)) {
                    continue;
                }

                $image->setPath($foundImage[0]['path']);

                if (count($imageMapper->searchPath()) < 2) {
                    $imageFiler->deleteImage();
                }

                $imageMapper->delete();
            }
        }

        $sender->response('200', ['message' => 'OK'], ['Content-Type: application/json']);
    }
}
