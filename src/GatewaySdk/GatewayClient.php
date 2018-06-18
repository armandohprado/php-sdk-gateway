<?php

namespace GatewaySdk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GatewaySdk\Exception\ApiServerException;

class GatewaySdkClient
{
    /**
     * @var string
     */
    protected $endpoint = 'https://api-homologacao.getnet.com.br/';

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var string
     */
    protected $token;

    /**
     * @var GuzzleHttp\Client
     */
    private $client;

    /**
     * @param GuzzleHttp\Client $guzzleClient
     */
    public function __construct(Client $guzzleClient = null)
    {
        $this->client = $guzzleClient ? '' : new Client();
    }

    /**
     * @param string $token
     */
    public function setToken(string $token)
    {
        $this->token = $token;
    }

    /**
     * @param string $endpoint
     */
    public function setEndpoint(string $endpoint)
    {
        if (substr($endpoint, -1) != '/') {
            $endpoint .= '/';
        }

        $this->endpoint = $endpoint;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array  $query
     * @return array
     * @throws GetnetSdk\Exception\ApiServerException
     */
    public function sendRequest(string $method, string $path, array $body=[])
    {
        try {
            $request = new Request(
                $method,
                $this->endpoint.$path,
                $this->getHeaders(),
                !empty($body) ? json_encode($body) : null
            );

            $response = $this->client->send($request);

            return new Response(
                true,
                json_decode($response->getBody()->getContents(), true)['data']
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() != 400) {
                throw new ApiServerException(
                    sprintf(
                        'The Getnet API returned an error code %s. Details: %s',
                        $e->getResponse()->getStatusCode(),
                        $e->getMessage()
                    )
                );
            }

            return new Response(
                false,
                null,
                json_decode($e->getResponse()->getBody()->getContents(), true)['error']
            );
        } catch (ServerException $e) {
            throw new ApiServerException(
                sprintf(
                    'The Getnet API returned an error code 500. Details: %s',
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return array_merge(
            $this->headers,
            $this->getDefaultHeaders()
        );
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * @return array
     */
    private function getDefaultHeaders()
    {
        return [
            'Authorization' => sprintf('Bearer %s', $this->token),
            'Content-Type' => 'application/json'
        ];
    }
}
