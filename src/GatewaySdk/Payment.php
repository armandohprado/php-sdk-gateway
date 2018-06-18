<?php

namespace GatewaySdk;

use GatewaySdk\GatewayClient;
use GatewaySdk\Response;

class Payment
{
    /**
     * @var GatewaySdk\GatewayClient
     */
    protected $client;

    /**
     * @param GatewaySdk\GatewayClient $client
     */
    public function __construct(GatewayClient $client = null)
    {
        $this->client = !empty($client) ? $client : new GatewayClient;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token)
    {
        $this->client->setToken($token);
    }

    /**
     * @param array $payment
     * @return GatewaySdk\Response
     */
    public function authorize(array $payment)
    {
        $this->client->setHeaders([
            'Content-Type' => 'application/json'
        ]);

        return $this->client->sendRequest('POST', 'payments', $payment);
    }

    /**
     * @param string $id
     * @return GatewaySdk\Response
     */
    public function get(string $id)
    {
        return $this->client->sendRequest('GET', sprintf('payments/%s', $id));
    }

    /**
     * @param string $id
     * @return GatewaySdk\Response
     */
    public function capture(string $id)
    {
        return $this->client->sendRequest('PUT', sprintf('payments/%s/capture', $id));
    }

    /**
     * @param string $id
     * @return GatewaySdk\Response
     */
    public function refund(string $id)
    {
        return $this->client->sendRequest('PUT', sprintf('payments/%s/refund', $id));
    }

    /**
     * @param array $data
     * @return GatewaySdk\Response
     */
    public function expressCheckout(array $data)
    {
        return $this->client->sendRequest('POST', sprintf('paypal/express-checkout'), $data);
    }

    /**
     * @param array $data
     * @return GatewaySdk\Response
     */
    public function billingAgreement(array $data)
    {
        return $this->client->sendRequest('POST', sprintf('paypal/billing-agreement'), $data);
    }

    /**
     * @param array $data
     * @return GatewaySdk\Response
     */
    public function installments(array $data)
    {
        return $this->client->sendRequest('POST', sprintf('paypal/installments'), $data);
    }
}
