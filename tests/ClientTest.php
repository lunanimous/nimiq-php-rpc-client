<?php

use Lunanimous\Rpc\Client;

/**
 * @internal
 * @coversNothing
 */
class ClientTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->client = new Client();
        $this->mock = new \GuzzleHttp\Handler\MockHandler();

        $httpClient = new \GuzzleHttp\Client([
            'handler' => $this->mock,
        ]);

        $this->client->setClient($httpClient);
    }

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
        $this->mock->append(new \GuzzleHttp\Psr7\Response(200, [], json_encode([
            'jsonrpc' => '2.0',
            'id' => 0,
            'result' => 1000,
        ])));

        $this->client->request('test', 'test-string', true, 15, [0, 2, 4], ['key' => 'value']);
        $request = $this->mock->getLastRequest();

        $this->assertEquals($request->getMethod(), 'POST');

        $body = json_decode($request->getBody()->getContents(), true);

        $this->assertEquals($body['id'], 0);
        $this->assertEquals($body['method'], 'test');
        $this->assertEquals($body['params'][0], 'test-string');
        $this->assertEquals($body['params'][1], true);
        $this->assertEquals($body['params'][2], 15);
        $this->assertEquals($body['params'][3], [0, 2, 4]);
        $this->assertEquals($body['params'][4], ['key' => 'value']);
    }

    public function testClientReturnsResponseResult()
    {
        $this->mock->append(new \GuzzleHttp\Psr7\Response(200, [], json_encode([
            'jsonrpc' => '2.0',
            'id' => 0,
            'result' => 1000,
        ])));

        $response = $this->client->request('test', 1000);

        $this->assertEquals($response, 1000);
    }

    public function testClientHandlesErrorProperly()
    {
        $this->mock->append(new \GuzzleHttp\Psr7\Response(200, [], json_encode([
            'jsonrpc' => '2.0',
            'id' => 0,
            'error' => [
                'code' => -32601,
                'message' => 'Method not found',
            ],
        ])));

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionCode(-32601);
        $this->expectExceptionMessage('Method not found');

        $response = $this->client->request('test', 1000);
    }
}
