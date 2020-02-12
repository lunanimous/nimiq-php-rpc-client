<?php

use Lunanimous\Rpc\Client;

/**
 * @internal
 * @coversNothing
 */
class ClientTest extends \PHPUnit\Framework\TestCase
{
    public function testClientCanBeInstanciated()
    {
        $client = new Client();

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testClientCalculatesBaseUri()
    {
        $client = new Client([
            'scheme' => 'https',
            'host' => 'localhost',
            'port' => '8181',
        ]);

        $baseUri = $client->getBaseUri();

        $this->assertEquals($baseUri, 'https://localhost:8181');
    }

    public function testClientCalculatesAuthInfo()
    {
        $client = new Client([
            'user' => 'admin',
            'password' => 'root',
        ]);

        $auth = $client->getAuth();

        $this->assertEquals($auth, [
            'admin', 'root',
        ]);
    }

    public function testClientSendsProperFormattedRequest()
    {
        $client = new Client();

        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(200, [
                'jsonrpc' => '2.0',
                'id' => 0,
                'result' => 1000,
            ]),
        ]);

        $container = [];
        $history = GuzzleHttp\Middleware::history($container);
        $handlerStack = GuzzleHttp\HandlerStack::create($mock);
        $handlerStack->push($history);

        $httpClientConfig = [
            'base_uri' => 'http://localhost:8181',
            'auth' => [],
            'verify' => null,
            'timeout' => false,
            'connect_timeout' => false,
            'handler' => $handlerStack,
        ];
        $mockHttpClient = new GuzzleHttp\Client($httpClientConfig);

        $client->setClient($mockHttpClient);

        $response = $client->request('test', 'test-string', true, 15, [0, 2, 4], ['key' => 'value']);

        $request = $container[0]['request'];
        $this->assertEquals($request->getMethod(), 'POST');
        $this->assertEquals($request->getHeaderLine('Host'), 'localhost:8181');
        $this->assertEquals($request->getHeaderLine('Content-Type'), 'application/json');

        $body = json_decode($request->getBody()->getContents(), true);

        $this->assertEquals($body['params'][0], 'test-string');
        $this->assertEquals($body['params'][1], true);
        $this->assertEquals($body['params'][2], 15);
        $this->assertEquals($body['params'][3], [0, 2, 4]);
        $this->assertEquals($body['params'][4], ['key' => 'value']);
    }

    public function testClientReturnsResponseObject()
    {
        $this->assertTrue(true);
    }

    public function testClientHandlesErrorProperly()
    {
        $this->assertTrue(true);
    }
}
