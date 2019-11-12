<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Message\AbstractRequest;

class CompletePurchaseResponse extends AbstractRequest
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

    public function isSuccessful()
    {
        return $this->data->responseCode == "00";
    }
}
