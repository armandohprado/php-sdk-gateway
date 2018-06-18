<?php

namespace GatewaySdk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GatewaySdk\GatewayClient;
use PHPUnit\Framework\TestCase;

class GatewayClientTest extends TestCase
{
    private static $guzzle;

    private $client;

    public static function setUpBeforeClass()
    {
        $mock = new MockHandler([
            new Response(200, ['content-type' => 'application/json'], '{"data": {"body": "body"}}'),
            new Response(
                400,
                ['content-type' => 'application/json'],
                '{"error": {"status": 400,"info": {"amount": ["can\'t be blank"]}}}'
            ),
            new Response(401),
            new Response(500)
        ]);

        $handler = HandlerStack::create($mock);
        self::$guzzle  = new Client(['handler' => $handler]);
    }

    public function setUp()
    {
        $this->client = new GatewayClient(self::$guzzle);
    }

    public function testSetToken()
    {
        $this->client->setToken('ABCDEF');

        $this->assertEquals(['Authorization' => 'Bearer ABCDEF'], $this->client->getHeaders());
    }

    public function testSetHeaders()
    {
        $this->client->setHeaders(['Content-Type'  => 'application/json']);
        $this->client->setToken('ABCDEF');

        $this->assertEquals([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ABCDEF'
        ], $this->client->getHeaders());
    }

    public function testRequest()
    {
        $response = $this->client->sendRequest('GET', '');

        $this->assertTrue($response->isSuccess());
        $this->assertEquals([
            'body' => 'body'
        ], $response->getData());
        $this->assertNull($response->getError());
    }

    public function testBadRequest()
    {
        $response = $this->client->sendRequest('GET', '');

        $this->assertFalse($response->isSuccess());
        $this->assertEquals([
            'status' => 400,
            'info' => [
                'amount' => [
                    'can\'t be blank'
                ],
            ],
        ], $response->getError());
        $this->assertNull($response->getData());
    }

    /**
     * @expectedException 
     */
    public function testClientErrorRequest()
    {
        $response = $this->client->sendRequest('GET', '');
    }

    /**
     * @expectedException 
     */
    public function testServerErrorRequest()
    {
        $response = $this->client->sendRequest('GET', '');
    }

}
