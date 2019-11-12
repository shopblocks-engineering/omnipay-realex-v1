<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Message\AbstractRequest;

class GenerateTokenResponse extends AbstractRequest
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function sendData($data)
    {

    }
}
