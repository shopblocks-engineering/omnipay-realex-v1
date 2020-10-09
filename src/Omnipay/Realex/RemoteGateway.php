<?php

namespace Omnipay\Realex;

use Omnipay\Common\AbstractGateway;
use Omnipay\Realex\Message\AuthRequest;
use Omnipay\Realex\Message\AuthResponse;
use Omnipay\Realex\Message\RemoteAbstractResponse;
use Omnipay\Realex\Message\VerifySigRequest;
use Omnipay\Realex\Message\VerifySigResponse;
use Carbon\Carbon;

/**
 * Realex Remote Gateway
 */
class RemoteGateway extends AbstractGateway
{
    public function getName()
    {
        return 'Realex Remote';
    }
    
    public function generatePayByLink($params)
    {
        $paymentMethod = $params['paymentMethod'];

        $paymentMethod['data'] = json_decode($paymentMethod['data']);

        $hashData = [
            'TIMESTAMP' => \Carbon\Carbon::now()->format('Ymdhis'),
            'MERCHANT_ID' => $paymentMethod['data']->testMode == 1 ? $paymentMethod['data']->sandbox_merchantId : $paymentMethod['data']->live_merchantId,
            'ORDER_ID' => $params['order']['id'] . "-". $params['payment_identifier'],
            'AMOUNT' => preg_replace('/[^0-9]/', '', sprintf('%01.2f', $params['amount'])),   
            'CURRENCY' => "GBP",            
        ];

        $secret = $paymentMethod['data']->testMode == 1 ? $paymentMethod['data']->sandbox_secret : $paymentMethod['data']->live_secret;

        $toHash = implode('.', $hashData);
        $toHashFirstPass = sha1($toHash);
        $shaHash = sha1($toHashFirstPass . '.' . $secret);

        $additionalData = [
            'ACCOUNT' => $paymentMethod['data']->testMode == 1 ? $paymentMethod['data']->sandbox_account : $paymentMethod['data']->live_account,
            'AUTO_SETTLE_FLAG' => '1',
            'HPP_VERSION' => '2',
            'HPP_POST_RESPONSE' => "https://".$params['host'],
            'SHA1HASH' => $shaHash
        ];

        $data = array_merge($hashData, $additionalData);

        $data = json_encode($data);

        $headers = ["Content-Type: application/json","Cache-Control: no-cache"];

        $ch = curl_init();

        if ($paymentMethod['data']->testMode == 1) {
            curl_setopt($ch, CURLOPT_URL, "https://pay.sandbox.realexpayments.com/pay");                
        } else {
            curl_setopt($ch, CURLOPT_URL, "https://pay.realexpayments.com/pay"); 
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $r = curl_exec($ch);

        $response = json_decode($r, 1);

        if (isset($response['hppPayByLink'])) {
            $payLink = $response['hppPayByLink'];
            return ['success' => 1, 'data' => ['payLink' => $payLink], 'request' => json_decode($data,1)];            
        } else {
            return ['success' => 0, 'data' => ['message' => 'unable to generate pay link'], 'request' => json_decode($data,1)];
        }
    }

    public function getDefaultParameters()
    {
        return array(
            'merchantId' => '',
            'account'    => '',
            'secret'     => '',
            '3dSecure'   => 0
        );
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getAccount()
    {
        return $this->getParameter('account');
    }

    public function setAccount($value)
    {
        return $this->setParameter('account', $value);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    public function getRefundPassword()
    {
        return $this->getParameter('refundPassword');
    }

    public function setCustomerEmail($value)
    {
        return $this->setParameter('customerEmail', $value);
    }

    public function getCustomerEmail()
    {
        return $this->getParameter('customerEmail');
    }

    /**
     * Although Omnipay terminology deals with 'refunds', you need
     * to actually supply the 'rebate' password that Realex gives you
     * in order for this to work.
     *
     * @param string $value The 'rebate' password supplied by Realex
     *
     * @return $this
     */
    public function setRefundPassword($value)
    {
        return $this->setParameter('refundPassword', $value);
    }

    public function get3dSecure()
    {
        return $this->getParameter('3dSecure');
    }

    public function set3dSecure($value)
    {
        return $this->setParameter('3dSecure', $value);
    }

    public function getAmount()
    {
        return $this->getParameter('amount');
    }

    public function setAmount($value)
    {
        return $this->setParameter('amount', $value);
    }

    public function getCustomerPhoneMobile()
    {
        return $this->getParameter('customerPhoneMobile');
    }

    public function setCustomerPhoneMobile($value)
    {
        return $this->setParameter('customerPhoneMobile', $value);
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

    public function setResponseJson($value)
    {
        $this->setParameter('response_json', $value);
    }

    public function getResponseJson()
    {
        return $this->getParameter('response_json');
    }
    
    public function setOrderId($value)
    {
        $this->setParameter('orderId', $value);
    }

    public function getOrderId()
    {
        return $this->getParameter('orderId');
    }

    public function setTestMode($value)
    {
        $this->setParameter('test_mode', $value);
    }

    public function getTestMode()
    {
        return $this->getParameter('test_mode');
    }

    public function purchase(array $parameters = array())
    {
        if (array_key_exists('mobileType', $parameters)) {
            return $this->createRequest('\Omnipay\Realex\Message\AuthMobileRequest', $parameters);
        } elseif (array_key_exists('cardReference', $parameters)) {
            return $this->createRequest('\Omnipay\Realex\Message\SavedAuthRequest', $parameters);
        } elseif ($this->get3dSecure()) {
            return $this->createRequest('\Omnipay\Realex\Message\EnrolmentRequest', $parameters);
        } else {
            return $this->createRequest('\Omnipay\Realex\Message\AuthRequest', $parameters);
        }
    }

    /**
     * This will always be called as the result of returning from 3D Secure.
     * Verify that the 3D Secure message we've received is legit
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\CompletePurchaseRequest', $parameters);

        return $this->createRequest('\Omnipay\Realex\Message\VerifySigRequest', $parameters);
    }

    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\RefundRequest', $parameters);
    }

    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\VoidRequest', $parameters);
    }

    public function fetchTransaction(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\FetchTransactionRequest', $parameters);
    }

    /**
     * Create/update/delete card/customer details
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\CreateCardRequest', $parameters);
    }

    public function updateCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\UpdateCardRequest', $parameters);
    }

    public function deleteCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\DeleteCardRequest', $parameters);
    }

    public function createCustomer(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\CreateCustomerRequest', $parameters);
    }

    public function updateCustomer(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\UpdateCustomerRequest', $parameters);
    }

    public function generatePaymentToken(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\GenerateTokenRequest', $parameters);
    }
}
