<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Message\AbstractRequest;
use GlobalPayments\Api\ServicesConfig;
use GlobalPayments\Api\HostedPaymentConfig;
use GlobalPayments\Api\Entities\Enums\HppVersion;
use GlobalPayments\Api\Entities\Enums\AddressType;
use GlobalPayments\Api\Services\HostedService;
use GlobalPayments\Api\Entities\HostedPaymentData;
use GlobalPayments\Api\Entities\Address;

class GenerateTokenRequest extends AbstractRequest
{
    protected $prodEndpoint = "https://pay.realexpayments.com/pay";
    protected $testEndpoint = "https://pay.sandbox.realexpayments.com/pay";

    private $data;

    public function setAmount($value)
    {
        $this->setParameter('amount', $value);
    }

    public function getAmount()
    {
        return $this->getParameter('amount');
    }

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

    public function setCustomerEmail($value)
    {
        $this->setParameter('customerEmail', $value);
    }

    public function getCustomerEmail()
    {
        return $this->getParameter('customerEmail');
    }

    public function setCustomerPhoneMobile($value)
    {
        $this->setParameter('customerPhoneMobile', $value);
    }

    public function getCustomerPhoneMobile()
    {
        return $this->getParameter('customerPhoneMobile');
    }

    public function setBillingAddressStreet1($value)
    {
        $this->setParameter('billingAddressStreet1', $value);
    }

    public function getBillingAddressStreet1()
    {
        return $this->getParameter('billingAddressStreet1');
    }

    public function setBillingAddressStreet2($value)
    {
        $this->setParameter('billingAddressStreet2', $value);
    }

    public function getBillingAddressStreet2()
    {
        return $this->getParameter('billingAddressStreet2');
    }

    public function setBillingAddressCity($value)
    {
        $this->setParameter('billingAddressCity', $value);
    }

    public function getBillingAddressCity()
    {
        return $this->getParameter('billingAddressCity');
    }

    public function setBillingAddressPostalCode($value)
    {
        $this->setParameter('billingAddressPostalCode', $value);
    }

    public function getBillingAddressPostalCode()
    {
        return $this->getParameter('billingAddressPostalCode');
    }

    public function setBillingAddressCountry($value)
    {
        $this->setParameter('billingAddressCountry', $value);
    }

    public function getBillingAddressCountry()
    {
        return $this->getParameter('billingAddressCountry');
    }

    public function setShippingAddressStreet1($value)
    {
        $this->setParameter('shippingAddressStreet1', $value);
    }

    public function getShippingAddressStreet1()
    {
        return $this->getParameter('shippingAddressStreet1');
    }

    public function setShippingAddressStreet2($value)
    {
        $this->setParameter('shippingAddressStreet2', $value);
    }

    public function getShippingAddressStreet2()
    {
        return $this->getParameter('shippingAddressStreet2');
    }

    public function setShippingAddressCity($value)
    {
        $this->setParameter('shippingAddressCity', $value);
    }

    public function getShippingAddressCity()
    {
        return $this->getParameter('shippingAddressCity');
    }

    public function setShippingAddressPostalCode($value)
    {
        $this->setParameter('shippingAddressPostalCode', $value);
    }

    public function getShippingAddressPostalCode()
    {
        return $this->getParameter('shippingAddressPostalCode');
    }

    public function setShippingAddressCountry($value)
    {
        $this->setParameter('shippingAddressCountry', $value);
    }

    public function getShippingAddressCountry()
    {
        return $this->getParameter('shippingAddressCountry');
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

        $config->hostedPaymentConfig = new HostedPaymentConfig();
        $config->hostedPaymentConfig->version = HppVersion::VERSION_2;
        $config->hostedPaymentConfig->cardStorageEnabled = "1";

        $hostedPaymentData = new HostedPaymentData();
        $hostedPaymentData->offerToSaveCard = true; // display the save card tick box
        $hostedPaymentData->customerExists = false;
        $hostedPaymentData->customerEmail = $this->getCustomerEmail();
        $hostedPaymentData->customerPhoneMobile = $this->getCustomerPhoneMobile();
        $hostedPaymentData->addressesMatch = false;

        $billingAddress = new Address();
        $billingAddress->streetAddress1 = $this->getBillingAddressStreet1();
        $billingAddress->streetAddress2 = $this->getBillingAddressStreet2();
        $billingAddress->streetAddress3 = "";
        $billingAddress->city = $this->getBillingAddressCity();
        $billingAddress->postalCode = $this->getBillingAddressPostalCode();
        $billingAddress->country = 826; //$this->getBillingAddressCountry();

        $shippingAddress = new Address();
        $shippingAddress->streetAddress1 = $this->getShippingAddressStreet1();
        $shippingAddress->streetAddress2 = $this->getShippingAddressStreet2();
        $shippingAddress->streetAddress3 = "";
        $shippingAddress->city = $this->getShippingAddressCity();
        $shippingAddress->postalCode = $this->getShippingAddressPostalCode();
        $shippingAddress->country = 826; //$this->getShippingAddressCountry();

        $data['config'] = $config;
        $data['hosted_payment_data'] = $hostedPaymentData;
        $data['billing_address'] = $billingAddress;
        $data['shipping_address'] = $shippingAddress;

        $data['amount'] = $this->getAmount();

        return $data;
    }

    public function sendData($data)
    {
        try {
            $service = new HostedService($data['config']);
            $hppJson = $service->charge($data['amount'])
                           ->withCurrency("GBP")
                           ->withAddress($data['billing_address'], AddressType::BILLING)
                           ->withAddress($data['shipping_address'], AddressType::SHIPPING)
                           ->withHostedPaymentData($data['hosted_payment_data'])
                           ->serialize();

            $this->data = $hppJson;
        } catch (ApiException $ex) {
            dd($ex->getMessage());
        }

        return $this->createResponse($this->data);
    }

    public function createResponse($data)
    {
        return $this->response = new GenerateTokenResponse($data);
    }
}
