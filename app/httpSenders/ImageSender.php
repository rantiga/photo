<?php

namespace app\httpSenders;

class ImageSender extends AbstractSender
{
    public function response(string $httpCode, array $data = [], array $headers = [])
    {
        $response = [];

        foreach ($data as $key => $value) {
            $response[] = [
                'imageId' => $value['id'],
                'userId' => $value['user_id'],
                'imagePath' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['REMOTE_ADDR'] . $value['path'],
                'imageName' => $value['header'],
                'imageDescription' => $value['description'],
                'imageType' => $value['img_type']
            ];
        }

        http_response_code($httpCode);

        if (empty($response)) {
            $response = ['Message: Image not fond'];
            http_response_code('404');
        }

        foreach ($headers as $key => $header) {
            header($header);
        }

        echo json_encode($response);
        die();
    }
}