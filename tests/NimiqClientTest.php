<?php

use Lunanimous\Rpc\Constants\AddressState;
use Lunanimous\Rpc\Constants\ConnectionState;
use Lunanimous\Rpc\Constants\ConsensusState;
use Lunanimous\Rpc\Models\Peer;
use Lunanimous\Rpc\NimiqClient;

/**
 * @internal
 * @coversNothing
 */
class NimiqClientTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var NimiqClient
     */
    protected $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = new NimiqClient();
        $this->mock = new \GuzzleHttp\Handler\MockHandler();

        $httpClient = new \GuzzleHttp\Client([
            'handler' => $this->mock,
        ]);

        $this->client->setClient($httpClient);
    }

    public function testGetPeerCount()
    {
        $this->appendNextResponse('peerCount/count.json');

        $result = $this->client->getPeerCount();

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'peerCount');

        $this->assertEquals($result, 6);
    }

    public function testGetSyncingStateWhenSyncing()
    {
        $this->appendNextResponse('syncing/syncing.json');

        $result = $this->client->getSyncingState();

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'syncing');

        $this->assertEquals($result->startingBlock, 578430);
        $this->assertEquals($result->currentBlock, 586493);
        $this->assertEquals($result->highestBlock, 586493);
    }

    public function testGetSyncingStateWhenNotSyncing()
    {
        $this->appendNextResponse('syncing/not-syncing.json');

        $result = $this->client->getSyncingState();

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'syncing');

        $this->assertEquals($result, false);
    }

    public function testGetConsensusState()
    {
        $this->appendNextResponse('consensus/syncing.json');

        $result = $this->client->getConsensusState();

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'consensus');

        $this->assertEquals($result, ConsensusState::Syncing);
    }

    public function testGetPeerListWithPeers()
    {
        $this->appendNextResponse('peerList/list.json');

        $result = $this->client->getPeerList();

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'peerList');

        $this->assertEquals(count($result), 2);
        $this->assertInstanceOf(Peer::class, $result[0]);
        $this->assertEquals($result[0]->id, 'b99034c552e9c0fd34eb95c1cdf17f5e');
        $this->assertEquals($result[0]->address, 'wss://seed1.nimiq-testnet.com:8080/b99034c552e9c0fd34eb95c1cdf17f5e');
        $this->assertEquals($result[0]->addressState, AddressState::Established);
        $this->assertEquals($result[0]->connectionState, ConnectionState::Established);

        $this->assertInstanceOf(Peer::class, $result[1]);
        $this->assertEquals($result[1]->id, 'e37dca72802c972d45b37735e9595cf0');
        $this->assertEquals($result[1]->address, 'wss://seed4.nimiq-testnet.com:8080/e37dca72802c972d45b37735e9595cf0');
        $this->assertEquals($result[1]->addressState, AddressState::Failed);
        $this->assertEquals($result[1]->connectionState, null);
    }

    public function testGetPeerListWhenEmpty()
    {
        $this->appendNextResponse('peerList/empty-list.json');

        $result = $this->client->getPeerList();

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'peerList');

        $this->assertEquals(count($result), 0);
    }

    private function appendNextResponse($fixture)
    {
        $jsonResponse = file_get_contents(dirname(__FILE__).'/fixtures/'.$fixture);

        $this->mock->append(new \GuzzleHttp\Psr7\Response(200, [], $jsonResponse));
    }

    private function getLastRequestBody()
    {
        $request = $this->mock->getLastRequest();

        return json_decode($request->getBody()->getContents(), true);
    }
}
