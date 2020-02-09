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
}
