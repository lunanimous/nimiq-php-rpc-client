<?php

use Lunanimous\Rpc\Constants\AccountType;
use Lunanimous\Rpc\Constants\AddressState;
use Lunanimous\Rpc\Constants\ConnectionState;
use Lunanimous\Rpc\Constants\ConsensusState;
use Lunanimous\Rpc\Constants\PeerStateCommand;
use Lunanimous\Rpc\Models\OutgoingTransaction;
use Lunanimous\Rpc\Models\Peer;
use Lunanimous\Rpc\Models\Transaction;
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

    public function testGetPeerNormal()
    {
        $this->appendNextResponse('peerState/normal.json');

        $result = $this->client->getPeer('wss://seed1.nimiq-testnet.com:8080/b99034c552e9c0fd34eb95c1cdf17f5e');

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'peerState');
        $this->assertEquals($body['params'][0], 'wss://seed1.nimiq-testnet.com:8080/b99034c552e9c0fd34eb95c1cdf17f5e');

        $this->assertInstanceOf(Peer::class, $result);
        $this->assertEquals($result->id, 'b99034c552e9c0fd34eb95c1cdf17f5e');
        $this->assertEquals($result->address, 'wss://seed1.nimiq-testnet.com:8080/b99034c552e9c0fd34eb95c1cdf17f5e');
        $this->assertEquals($result->addressState, AddressState::Established);
        $this->assertEquals($result->connectionState, ConnectionState::Established);
    }

    public function testGetPeerFailed()
    {
        $this->appendNextResponse('peerState/failed.json');

        $result = $this->client->getPeer('wss://seed4.nimiq-testnet.com:8080/e37dca72802c972d45b37735e9595cf0');

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'peerState');
        $this->assertEquals($body['params'][0], 'wss://seed4.nimiq-testnet.com:8080/e37dca72802c972d45b37735e9595cf0');

        $this->assertInstanceOf(Peer::class, $result);
        $this->assertEquals($result->id, 'e37dca72802c972d45b37735e9595cf0');
        $this->assertEquals($result->address, 'wss://seed4.nimiq-testnet.com:8080/e37dca72802c972d45b37735e9595cf0');
        $this->assertEquals($result->addressState, AddressState::Failed);
        $this->assertEquals($result->connectionState, null);
    }

    public function testGetPeerError()
    {
        $this->expectException(BadMethodCallException::class);

        $this->appendNextResponse('peerState/error.json');

        $result = $this->client->getPeer('unknown');
    }

    public function testSetPeerState()
    {
        $this->appendNextResponse('peerState/normal.json');

        $result = $this->client->setPeerState('wss://seed1.nimiq-testnet.com:8080/b99034c552e9c0fd34eb95c1cdf17f5e', PeerStateCommand::Connect);

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'peerState');
        $this->assertEquals($body['params'][0], 'wss://seed1.nimiq-testnet.com:8080/b99034c552e9c0fd34eb95c1cdf17f5e');
        $this->assertEquals($body['params'][1], 'connect');

        $this->assertInstanceOf(Peer::class, $result);
        $this->assertEquals($result->id, 'b99034c552e9c0fd34eb95c1cdf17f5e');
        $this->assertEquals($result->address, 'wss://seed1.nimiq-testnet.com:8080/b99034c552e9c0fd34eb95c1cdf17f5e');
        $this->assertEquals($result->addressState, AddressState::Established);
        $this->assertEquals($result->connectionState, ConnectionState::Established);
    }

    public function testCreateRawTransaction()
    {
        $this->appendNextResponse('createRawTransaction/basic.json');

        $transaction = new OutgoingTransaction();
        $transaction->from = 'NQ39 NY67 X0F0 UTQE 0YER 4JEU B67L UPP8 G0FM';
        $transaction->fromType = AccountType::Basic;
        $transaction->to = 'NQ16 61ET MB3M 2JG6 TBLK BR0D B6EA X6XQ L91U';
        $transaction->toType = AccountType::Basic;
        $transaction->value = 100000;
        $transaction->fee = 1;

        $result = $this->client->createRawTransaction($transaction);

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'createRawTransaction');

        $param = $body['params'][0];
        $this->assertEquals($param, [
            'from' => 'NQ39 NY67 X0F0 UTQE 0YER 4JEU B67L UPP8 G0FM',
            'fromType' => 0,
            'to' => 'NQ16 61ET MB3M 2JG6 TBLK BR0D B6EA X6XQ L91U',
            'toType' => 0,
            'value' => 100000,
            'fee' => 1,
            'data' => null,
        ]);

        $this->assertEquals($result, '00c3c0d1af80b84c3b3de4e3d79d5c8cc950e044098c969953d68bf9cee68d7b53305dbaac7514a06dae935e40d599caf1bd8a243c00000000000186a00000000000000001000af84c01239b16cee089836c2af5c7b1dbb22cdc0b4864349f7f3805909aa8cf24e4c1ff0461832e86f3624778a867d5f2ba318f92918ada7ae28d70d40c4ef1d6413802');
    }

    public function testGetRawTransactionInfo()
    {
        $this->appendNextResponse('getRawTransactionInfo/basic-transaction.json');

        $result = $this->client->getRawTransactionInfo('00c3c0d1af80b84c3b3de4e3d79d5c8cc950e044098c969953d68bf9cee68d7b53305dbaac7514a06dae935e40d599caf1bd8a243c00000000000186a00000000000000001000af84c01239b16cee089836c2af5c7b1dbb22cdc0b4864349f7f3805909aa8cf24e4c1ff0461832e86f3624778a867d5f2ba318f92918ada7ae28d70d40c4ef1d6413802');

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'getRawTransactionInfo');
        $this->assertEquals($body['params'][0], '00c3c0d1af80b84c3b3de4e3d79d5c8cc950e044098c969953d68bf9cee68d7b53305dbaac7514a06dae935e40d599caf1bd8a243c00000000000186a00000000000000001000af84c01239b16cee089836c2af5c7b1dbb22cdc0b4864349f7f3805909aa8cf24e4c1ff0461832e86f3624778a867d5f2ba318f92918ada7ae28d70d40c4ef1d6413802');

        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals($result->hash, '7784f2f6eaa076fa5cf0e4d06311ad204b2f485de622231785451181e8129091');
        $this->assertEquals($result->from, 'b7cc7f01e0e6f0e07dd9249dc598f4e5ee8801f5');
        $this->assertEquals($result->fromAddress, 'NQ39 NY67 X0F0 UTQE 0YER 4JEU B67L UPP8 G0FM');
        $this->assertEquals($result->to, '305dbaac7514a06dae935e40d599caf1bd8a243c');
        $this->assertEquals($result->toAddress, 'NQ16 61ET MB3M 2JG6 TBLK BR0D B6EA X6XQ L91U');
        $this->assertEquals($result->value, 100000);
        $this->assertEquals($result->fee, 1);
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
