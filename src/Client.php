<?php

namespace Lunanimous\Rpc;

use BadMethodCallException;
use GuzzleHttp\Client as GuzzleHttp;

class Client
{
    /**
     * Http Client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Client configuration.
     */
    protected $config = [
        'scheme' => 'http',
        'host' => '127.0.0.1',
        'port' => 8648,
        'user' => null,
        'password' => null,
        'ca' => null,
        'timeout' => false,
    ];

    /**
     * URL path.
     *
     * @var string
     */
    protected $path = '/';

    /**
     * JSON-RPC Id.
     *
     * @var int
     */
    protected $rpcId = 0;

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);

        // construct client
        $this->client = new GuzzleHttp([
            'base_uri' => $this->getBaseUri(),
            'auth' => $this->getAuth(),
            'verify' => $this->getCa(),
            'timeout' => (float) $this->config['timeout'],
            'connect_timeout' => (float) $this->config['timeout'],
        ]);
    }

    /**
     * Makes request to Nimiq Node.
     */
    public function __call(string $method, array $params = []): array
    {
        return $this->request($method, ...$params);
    }

    /**
     * Makes request to Nimiq Node.
     *
     * @param mixed $params
     */
    public function request(string $method, ...$params): array
    {
        $response = $this->client->post($this->path, $this->makeJson($method, $params));

        $body = json_decode((string) $response->getBody(), true);
        if (isset($body['error'])) {
            throw new BadMethodCallException($body['error']['message'], $body['error']['code']);
        }

        return $body;
    }

    /**
     * Gets authentication array.
     */
    public function getAuth(): array
    {
        return [
            $this->config['user'],
            $this->config['password'],
        ];
    }

    /**
     * Gets CA file from config.
     */
    public function getCa(): ?string
    {
        if (isset($this->config['ca']) && is_file($this->config['ca'])) {
            return $this->config['ca'];
        }

        return null;
    }

    /**
     * Gets the base uri.
     */
    public function getBaseUri(): string
    {
        return $this->config['scheme'].'://'.$this->config['host'].':'.$this->config['port'];
    }

    /**
     * Construct json request.
     *
     * @param mixed $params
     */
    protected function makeJson(string $method, $params = []): array
    {
        return [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => $method,
                'params' => (array) $params,
                'id' => $this->rpcId++,
            ],
        ];
    }
}
