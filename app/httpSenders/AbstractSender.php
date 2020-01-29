<?php

namespace app\httpSenders;

abstract class AbstractSender
{
    abstract function response(string $httpCode, array $data = [], array $headers = []);
}