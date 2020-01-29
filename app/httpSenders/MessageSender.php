<?php

namespace app\httpSenders;

class MessageSender extends AbstractSender
{
    public function response(string $httpCode, array $data = [], array $headers = [])
    {
        http_response_code($httpCode);

        foreach ($headers as $key => $header) {
            header($header);
        }

        echo json_encode($data);
        die();
    }
}