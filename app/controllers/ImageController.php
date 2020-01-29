<?php

namespace app\controllers;

use app\helpers\Container\Container;

class ImageController extends AbstractController
{
    //Слишком много логики в контроллерах...
    public function get($values)
    {
        $image = $this->entitiesFactory->getImageEntity();
        $user = $this->entitiesFactory->getUserEntity();

        $user->setId($values['uriValues']['user_id']);

        $searchName = $values['requestValues']['name'] ?? '';
        $searchDescription = $values['requestValues']['description'] ?? '';

        $imageMapper = $this->modelsFactory->getImageMapper($this->pdo->getConnection(), $image);

        if (!empty($values['uriValues']['image_id'])) {
            $image->setId($values['uriValues']['image_id']);
            $result = $imageMapper->getById();
        } else {
            $image->setUserId($values['uriValues']['user_id']);
            $result = $imageMapper->getByUserId($searchName, $searchDescription);
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
            setMimeType($imageInfo['mime'])->
            setPath($fileData['tmp_name']);

            if (!$imageValidator->check()) {
                continue;
            }

            $imageCaster->cast();

            $result = $imageMapper->searchCast();

            if ($result) {
                $image->setPath($result['path']);
                $imageMapper->save();

                continue;
            }

            $imageFiler->saveImage();
            $imageMapper->save();
        }

        $sender = $this->sendersFactory->getMessageSender();
        $sender->response('201', ['Status' => 'OK'], ['Content-Type: application/json']);
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
                $sender->response('404', ['Message: Image not found'], ['Content-Type: application/json']);
            }

            $image->setPath($foundImage[0]['path']);

            if (count($imageMapper->searchPath()) < 2) {
                $imageFiler->deleteImage();
            }

            $imageMapper->delete();
            $sender->response('200', ['Status' => 'OK'], ['Content-Type: application/json']);
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

        $sender->response('200', ['Status' => 'OK'], ['Content-Type: application/json']);
    }
}
