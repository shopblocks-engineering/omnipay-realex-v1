<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Message\AbstractRequest;
use GlobalPayments\Api\ServicesConfig;
use GlobalPayments\Api\Services\HostedService;
use GlobalPayments\Api\HostedPaymentConfig;

class CompletePurchaseRequest extends AbstractRequest
{
    protected $prodEndpoint = "https://pay.realexpayments.com/pay";
    protected $testEndpoint = "https://pay.sandbox.realexpayments.com/pay";

    public function setMerchantId($value)
    {
        $this->setParameter('merchantId', $value);
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setAccount($value)
    {
        $this->setParameter('account', $value);
    }

    public function getAccount()
    {
        return $this->getParameter('account');
    }

    public function setSecret($value)
    {
        $this->setParameter('secret', $value);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function setResponseJson($value)
    {
        $this->setParameter('response_json', $value);
    }

    public function getResponseJson()
    {
        return $this->getParameter('response_json');
    }

    public function setTestMode($value)
    {
        $this->setParameter('test_mode', $value);
    }

    public function getTestMode()
    {
        return $this->getParameter('test_mode');
    }

    public function getData()
    {
        $config = new ServicesConfig();
        $config->merchantId = $this->getMerchantId();
        $config->accountId = $this->getAccount();
        $config->sharedSecret = $this->getSecret();
        if ($this->getTestMode()) {
            $config->serviceUrl = $this->testEndpoint;
        } else {
            $config->serviceUrl = $this->prodEndpoint;
        }

        $data['config'] = $config;
        $data['response_json'] = $this->getResponseJson();

        return $data;
    }

    public function sendData($data)
    {
        try {
            $service = new HostedService($data['config']);

            $parsedResponse = $service->parseResponse($data['response_json'], true);
        } catch (ApiException $ex) {
            dd($ex->getMessage());
        }

        return $this->createResponse($parsedResponse ?? null);
    }

    public function createResponse($data)
    {
        return $this->response = new CompletePurchaseResponse($data);
    }
}
