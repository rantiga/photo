<?php

namespace app\controllers;

class HashController extends AbstractController
{
    public function get($values)
    {
        $hashMapper = $this->modelsFactory->getHashMapper($this->pdo->getConnection());

        $result = $hashMapper->getImage($values['uriValues']['hash']);
        header('X-Accel-Redirect: ' . $result['path']);
        header('Content-Type: ' . $result['img_type']);
        header('Content-Disposition: attachment; filename=' . basename($result['path']));
    }
}