<?php

use Lunanimous\Rpc\NimiqResponse;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ResponseTest extends TestCase
{
    public function testResultCanBeRetrievedForSuccessfulResponse()
    {
        $mockResponse = new \GuzzleHttp\Psr7\Response(200, [], '{"jsonrpc":"2.0","result":487233,"id":1}');
        $response = new NimiqResponse($mockResponse);

        $this->assertTrue($response->hasResult());
        $this->assertEquals($response->getResult(), 487233);
    }

    public function testHasErrorIsFalseForSuccessfulResponse()
    {
        $mockResponse = new \GuzzleHttp\Psr7\Response(200, [], '{"jsonrpc":"2.0","result":487233,"id":1}');
        $response = new NimiqResponse($mockResponse);

        $this->assertFalse($response->hasError());
    }

    public function testErrorCanBeRetrievedForErrorResponse()
    {
        $mockResponse = new \GuzzleHttp\Psr7\Response(200, [], '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method not found"},"id":1}');
        $response = new NimiqResponse($mockResponse);

        $this->assertTrue($response->hasError());
        $this->assertEquals($response->getError(), [
            'code' => -32601,
            'message' => 'Method not found',
        ]);
    }

    public function testHasResultIsFalseForSuccessfulResponse()
    {
        $mockResponse = new \GuzzleHttp\Psr7\Response(200, [], '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method not found"},"id":1}');
        $response = new NimiqResponse($mockResponse);

        $this->assertFalse($response->hasResult());
    }

    public function testOriginalResponseObjectCanBeRetrieved()
    {
        $mockResponse = new \GuzzleHttp\Psr7\Response(200, [], '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method not found"},"id":1}');
        $response = new NimiqResponse($mockResponse);

        $this->assertEquals($response->getResponse()->getBody()->__toString(), '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method not found"},"id":1}');
    }

    public function testResponseCanReturnTheRpcId()
    {
        $mockResponse = new \GuzzleHttp\Psr7\Response(200, [], '{"jsonrpc":"2.0","result":487233,"id":1}');
        $response = new NimiqResponse($mockResponse);

        $this->assertEquals($response->getId(), 1);
    }
}
