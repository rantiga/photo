<?php

namespace app\helpers\SendersFactory;

use app\helpers\FactoryInterface;
use app\httpSenders\ImageSender;
use app\httpSenders\MessageSender;

class SendersFactory implements FactoryInterface
{
    public function getImageSender()
    {
        return new ImageSender();
    }

    public function getMessageSender()
    {
        return new MessageSender();
    }
}