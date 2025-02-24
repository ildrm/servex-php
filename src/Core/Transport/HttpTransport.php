<?php

namespace Servex\Core\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class HttpTransport implements TransportInterface
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 2.0]);
    }

    /**
     * @throws GuzzleException
     */
    public function call(string $endpoint, string $method, array $params = []): mixed
    {
        $response = $this->client->post($endpoint, [
            'json' => [
                'method' => $method,
                'params' => $params,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
}