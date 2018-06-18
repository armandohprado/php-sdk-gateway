<?php

namespace GatewaySdk;

use GatewaySdk\Payment;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    private $client;

    private $payment;

    private $response;

    public function setUp()
    {
        $this->client = $this->getMockBuilder('GatewaySdk\GatewayClient')
            ->disableOriginalConstructor()
            ->getMock();

        $this->response = $this->getMockBuilder('GatewaySdk\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $this->payment = new Payment($this->client);
    }

    public function testSetToken()
    {
        $token = 'ABCDEF';

        $this->client->expects($this->once())
            ->method('setToken')
            ->with($token);

        $this->payment->setToken($token);
    }

    public function testGetPayment()
    {
        $paymentId = '12345';

        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with('GET', 'payments/12345')
            ->will($this->returnValue($this->response));

        $this->assertInstanceOf('GatewaySdk\Response', $this->payment->get($paymentId));
    }

    public function testAuthorizePayment()
    {
        $payment = [
            "externalId" => "23524234322523-32523",
            "capture" => true,
            "amount" => 100,
            "interest" => 0,
            "creditCard" => [
                "installments" => 1,
                "number" => "4111.1111.1111.1111",
                "holder" => "John Doe",
                "expiration" => "12/2020",
                "cvv" => "123",
                "brand" => "visa"
            ],
            "customer" => [
                "externalId" => "1234",
                "name" => "John Doe",
                "document" => "112.452.662-12",
                "address" => [
                    "street" => "Rua Joaquim Floriano",
                    "number" => "820",
                    "complement" => "20 andar",
                    "zipcode" => "04534-003",
                    "city" => "São Paulo",
                    "state" => "SP",
                    "country" => "BR"
                ]
            ],
            "items" => [
                [
                    "externalId" => "123",
                    "type" => "ticket",
                    "name" => "My VIP ticket",
                    "unitPrice" => 100,
                    "quantity" => 1
                ]
            ]
        ];

        $this->client->expects($this->once())
            ->method('setHeaders')
            ->with(['Content-Type' => 'application/json']);

        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with('POST', 'payments', $payment)
            ->will($this->returnValue($this->response));

        $this->assertInstanceOf('GatewaySdk\Response', $this->payment->authorize($payment));
    }

    public function testCapturePayment()
    {
        $paymentId = '12345';

        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with('PUT', 'payments/12345/capture')
            ->will($this->returnValue($this->response));

        $this->assertInstanceOf('GatewaySdk\Response', $this->payment->capture($paymentId));
    }

    public function testRefundPayment()
    {
        $paymentId = '12345';

        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with('PUT', 'payments/12345/refund')
            ->will($this->returnValue($this->response));

        $this->assertInstanceOf('GatewaySdk\Response', $this->payment->refund($paymentId));
    }
}
