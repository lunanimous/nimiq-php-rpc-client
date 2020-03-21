<?php

use Lunanimous\Rpc\Constants\AccountType;
use Lunanimous\Rpc\Constants\AddressState;
use Lunanimous\Rpc\Constants\ConnectionState;
use Lunanimous\Rpc\Constants\ConsensusState;
use Lunanimous\Rpc\Constants\PeerStateCommand;
use Lunanimous\Rpc\Constants\PoolConnectionState;
use Lunanimous\Rpc\Models\Account;
use Lunanimous\Rpc\Models\Block;
use Lunanimous\Rpc\Models\Mempool;
use Lunanimous\Rpc\Models\OutgoingTransaction;
use Lunanimous\Rpc\Models\Peer;
use Lunanimous\Rpc\Models\Transaction;
use Lunanimous\Rpc\Models\TransactionReceipt;
use Lunanimous\Rpc\Models\Wallet;
use Lunanimous\Rpc\NimiqClient;

/**
 * @internal
 * @coversDefaultClass \Lunanimous\Rpc\NimiqClient
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

    public function testNimiqClientCanBeInstanciated()
    {
        $client = new NimiqClient();

        $this->assertInstanceOf(NimiqClient::class, $client);
    }

    public function testGetPeerCount()
    {
        $this->appendNextResponse('peerCount/count.json');

        $result = $this->client->getPeerCount();

        $body = $this->getLastRequestBody();
        $this->assertEquals('peerCount', $body['method']);

        $this->assertEquals(6, $result);
    }

    public function testGetSyncingStateWhenSyncing()
    {
        $this->appendNextResponse('syncing/syncing.json');

        $result = $this->client->getSyncingState();

        $body = $this->getLastRequestBody();
        $this->assertEquals('syncing', $body['method']);

        $this->assertEquals(578430, $result->startingBlock);
        $this->assertEquals(586493, $result->currentBlock);
        $this->assertEquals(586493, $result->highestBlock);
    }

    public function testGetSyncingStateWhenNotSyncing()
    {
        $this->appendNextResponse('syncing/not-syncing.json');

        $result = $this->client->getSyncingState();

        $body = $this->getLastRequestBody();
        $this->assertEquals('syncing', $body['method']);

        $this->assertEquals(false, $result);
    }

    public function testGetConsensusState()
    {
        $this->appendNextResponse('consensus/syncing.json');

        $result = $this->client->getConsensusState();

        $body = $this->getLastRequestBody();
        $this->assertEquals('consensus', $body['method']);

        $this->assertEquals(ConsensusState::Syncing, $result);
    }

    public function testGetPeerListWithPeers()
    {
        $this->appendNextResponse('peerList/list.json');

        $result = $this->client->getPeerList();

        $body = $this->getLastRequestBody();
        $this->assertEquals('peerList', $body['method']);

        $this->assertEquals(count($result), 2);
        $this->assertInstanceOf(Peer::class, $result[0]);
        $this->assertEquals('b99034c552e9c0fd34eb95c1cdf17f5e', $result[0]->id);
        $this->assertEquals('wss://seed1.nimiq-testnet.com:8080/b99034c552e9c0fd34eb95c1cdf17f5e', $result[0]->address);
        $this->assertEquals(AddressState::Established, $result[0]->addressState);
        $this->assertEquals(ConnectionState::Established, $result[0]->connectionState);

        $this->assertInstanceOf(Peer::class, $result[1]);
        $this->assertEquals('e37dca72802c972d45b37735e9595cf0', $result[1]->id);
        $this->assertEquals('wss://seed4.nimiq-testnet.com:8080/e37dca72802c972d45b37735e9595cf0', $result[1]->address);
        $this->assertEquals(AddressState::Failed, $result[1]->addressState);
        $this->assertEquals(null, $result[1]->connectionState);
    }

    public function testGetPeerListWhenEmpty()
    {
        $this->appendNextResponse('peerList/empty-list.json');

        $result = $this->client->getPeerList();

        $body = $this->getLastRequestBody();
        $this->assertEquals('peerList', $body['method']);

        $this->assertEquals(count($result), 0);
    }

    public function testGetPeerNormal()
    {
        $this->appendNextResponse('peerState/normal.json');

        $result = $this->client->getPeer('wss://seed1.nimiq-testnet.com:8080/b99034c552e9c0fd34eb95c1cdf17f5e');

        $body = $this->getLastRequestBody();
        $this->assertEquals('peerState', $body['method']);
        $this->assertEquals('wss://seed1.nimiq-testnet.com:8080/b99034c552e9c0fd34eb95c1cdf17f5e', $body['params'][0]);

        $this->assertInstanceOf(Peer::class, $result);
        $this->assertEquals('b99034c552e9c0fd34eb95c1cdf17f5e', $result->id);
        $this->assertEquals('wss://seed1.nimiq-testnet.com:8080/b99034c552e9c0fd34eb95c1cdf17f5e', $result->address);
        $this->assertEquals(AddressState::Established, $result->addressState);
        $this->assertEquals(ConnectionState::Established, $result->connectionState);
    }

    public function testGetPeerFailed()
    {
        $this->appendNextResponse('peerState/failed.json');

        $result = $this->client->getPeer('wss://seed4.nimiq-testnet.com:8080/e37dca72802c972d45b37735e9595cf0');

        $body = $this->getLastRequestBody();
        $this->assertEquals('peerState', $body['method']);
        $this->assertEquals('wss://seed4.nimiq-testnet.com:8080/e37dca72802c972d45b37735e9595cf0', $body['params'][0]);

        $this->assertInstanceOf(Peer::class, $result);
        $this->assertEquals('e37dca72802c972d45b37735e9595cf0', $result->id);
        $this->assertEquals('wss://seed4.nimiq-testnet.com:8080/e37dca72802c972d45b37735e9595cf0', $result->address);
        $this->assertEquals(AddressState::Failed, $result->addressState);
        $this->assertEquals(null, $result->connectionState);
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
        $this->assertEquals('peerState', $body['method']);
        $this->assertEquals('wss://seed1.nimiq-testnet.com:8080/b99034c552e9c0fd34eb95c1cdf17f5e', $body['params'][0]);
        $this->assertEquals('connect', $body['params'][1]);

        $this->assertInstanceOf(Peer::class, $result);
        $this->assertEquals('b99034c552e9c0fd34eb95c1cdf17f5e', $result->id);
        $this->assertEquals('wss://seed1.nimiq-testnet.com:8080/b99034c552e9c0fd34eb95c1cdf17f5e', $result->address);
        $this->assertEquals(AddressState::Established, $result->addressState);
        $this->assertEquals(ConnectionState::Established, $result->connectionState);
    }

    public function testSendRawTransaction()
    {
        $this->appendNextResponse('sendTransaction/transaction.json');

        $result = $this->client->sendRawTransaction('00c3c0d1af80b84c3b3de4e3d79d5c8cc950e044098c969953d68bf9cee68d7b53305dbaac7514a06dae935e40d599caf1bd8a243c00000000000000010000000000000001000dc2e201b5a1755aec80aa4227d5afc6b0de0fcfede8541f31b3c07b9a85449ea9926c1c958628d85a2b481556034ab3d67ff7de28772520813c84aaaf8108f6297c580c');

        $body = $this->getLastRequestBody();
        $this->assertEquals('sendRawTransaction', $body['method']);
        $this->assertEquals('00c3c0d1af80b84c3b3de4e3d79d5c8cc950e044098c969953d68bf9cee68d7b53305dbaac7514a06dae935e40d599caf1bd8a243c00000000000000010000000000000001000dc2e201b5a1755aec80aa4227d5afc6b0de0fcfede8541f31b3c07b9a85449ea9926c1c958628d85a2b481556034ab3d67ff7de28772520813c84aaaf8108f6297c580c', $body['params'][0]);

        $this->assertIsString($result);
        $this->assertEquals('81cf3f07b6b0646bb16833d57cda801ad5957e264b64705edeef6191fea0ad63', $result);
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
        $this->assertEquals('createRawTransaction', $body['method']);

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

        $this->assertEquals('00c3c0d1af80b84c3b3de4e3d79d5c8cc950e044098c969953d68bf9cee68d7b53305dbaac7514a06dae935e40d599caf1bd8a243c00000000000186a00000000000000001000af84c01239b16cee089836c2af5c7b1dbb22cdc0b4864349f7f3805909aa8cf24e4c1ff0461832e86f3624778a867d5f2ba318f92918ada7ae28d70d40c4ef1d6413802', $result);
    }

    public function testSendTransaction()
    {
        $this->appendNextResponse('sendTransaction/transaction.json');

        $transaction = new OutgoingTransaction();
        $transaction->from = 'NQ39 NY67 X0F0 UTQE 0YER 4JEU B67L UPP8 G0FM';
        $transaction->fromType = AccountType::Basic;
        $transaction->to = 'NQ16 61ET MB3M 2JG6 TBLK BR0D B6EA X6XQ L91U';
        $transaction->toType = AccountType::Basic;
        $transaction->value = 1;
        $transaction->fee = 1;

        $result = $this->client->sendTransaction($transaction);

        $body = $this->getLastRequestBody();
        $this->assertEquals('sendTransaction', $body['method']);

        $payload = $body['params'][0];
        $this->assertEquals([
            'from' => 'NQ39 NY67 X0F0 UTQE 0YER 4JEU B67L UPP8 G0FM',
            'fromType' => 0,
            'to' => 'NQ16 61ET MB3M 2JG6 TBLK BR0D B6EA X6XQ L91U',
            'toType' => 0,
            'value' => 1,
            'fee' => 1,
            'data' => null,
        ], $payload);

        $this->assertIsString($result);
        $this->assertEquals('81cf3f07b6b0646bb16833d57cda801ad5957e264b64705edeef6191fea0ad63', $result);
    }

    public function testGetRawTransactionInfo()
    {
        $this->appendNextResponse('getRawTransactionInfo/basic-transaction.json');

        $result = $this->client->getRawTransactionInfo('00c3c0d1af80b84c3b3de4e3d79d5c8cc950e044098c969953d68bf9cee68d7b53305dbaac7514a06dae935e40d599caf1bd8a243c00000000000186a00000000000000001000af84c01239b16cee089836c2af5c7b1dbb22cdc0b4864349f7f3805909aa8cf24e4c1ff0461832e86f3624778a867d5f2ba318f92918ada7ae28d70d40c4ef1d6413802');

        $body = $this->getLastRequestBody();
        $this->assertEquals('getRawTransactionInfo', $body['method']);
        $this->assertEquals('00c3c0d1af80b84c3b3de4e3d79d5c8cc950e044098c969953d68bf9cee68d7b53305dbaac7514a06dae935e40d599caf1bd8a243c00000000000186a00000000000000001000af84c01239b16cee089836c2af5c7b1dbb22cdc0b4864349f7f3805909aa8cf24e4c1ff0461832e86f3624778a867d5f2ba318f92918ada7ae28d70d40c4ef1d6413802', $body['params'][0]);

        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals('7784f2f6eaa076fa5cf0e4d06311ad204b2f485de622231785451181e8129091', $result->hash);
        $this->assertEquals('b7cc7f01e0e6f0e07dd9249dc598f4e5ee8801f5', $result->from);
        $this->assertEquals('NQ39 NY67 X0F0 UTQE 0YER 4JEU B67L UPP8 G0FM', $result->fromAddress);
        $this->assertEquals('305dbaac7514a06dae935e40d599caf1bd8a243c', $result->to);
        $this->assertEquals('NQ16 61ET MB3M 2JG6 TBLK BR0D B6EA X6XQ L91U', $result->toAddress);
        $this->assertEquals(100000, $result->value);
        $this->assertEquals(1, $result->fee);
    }

    public function testGetTransactionByBlockHashAndIndex()
    {
        $this->appendNextResponse('getTransaction/full-transaction.json');

        $result = $this->client->getTransactionByBlockHashAndIndex('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', 0);

        $body = $this->getLastRequestBody();
        $this->assertEquals('getTransactionByBlockHashAndIndex', $body['method']);
        $this->assertEquals('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', $body['params'][0]);
        $this->assertEquals(0, $body['params'][1]);

        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals('78957b87ab5546e11e9540ce5a37ebbf93a0ebd73c0ce05f137288f30ee9f430', $result->hash);
        $this->assertEquals('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', $result->blockHash);
        $this->assertEquals(0, $result->transactionIndex);
        $this->assertEquals('355b4fe2304a9c818b9f0c3c1aaaf4ad4f6a0279', $result->from);
        $this->assertEquals('NQ16 6MDL YQHG 9AE8 32UY 1GX1 MAPL MM7N L0KR', $result->fromAddress);
        $this->assertEquals('4f61c06feeb7971af6997125fe40d629c01af92f', $result->to);
        $this->assertEquals('NQ05 9VGU 0TYE NXBH MVLR E4JY UG6N 5701 MX9F', $result->toAddress);
        $this->assertEquals(2636710000, $result->value);
        $this->assertEquals(0, $result->fee);
    }

    public function testGetTransactionByBlockHashAndIndexWhenNotFound()
    {
        $this->appendNextResponse('getTransaction/not-found.json');

        $result = $this->client->getTransactionByBlockHashAndIndex('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', 5);

        $body = $this->getLastRequestBody();
        $this->assertEquals('getTransactionByBlockHashAndIndex', $body['method']);
        $this->assertEquals('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', $body['params'][0]);
        $this->assertEquals(5, $body['params'][1]);

        $this->assertEquals(null, $result);
    }

    public function testGetTransactionByBlockNumberAndIndex()
    {
        $this->appendNextResponse('getTransaction/full-transaction.json');

        $result = $this->client->getTransactionByBlockNumberAndIndex(11608, 0);

        $body = $this->getLastRequestBody();
        $this->assertEquals('getTransactionByBlockNumberAndIndex', $body['method']);
        $this->assertEquals(11608, $body['params'][0]);
        $this->assertEquals(0, $body['params'][1]);

        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals('78957b87ab5546e11e9540ce5a37ebbf93a0ebd73c0ce05f137288f30ee9f430', $result->hash);
        $this->assertEquals(11608, $result->blockNumber);
        $this->assertEquals(0, $result->transactionIndex);
        $this->assertEquals('355b4fe2304a9c818b9f0c3c1aaaf4ad4f6a0279', $result->from);
        $this->assertEquals('NQ16 6MDL YQHG 9AE8 32UY 1GX1 MAPL MM7N L0KR', $result->fromAddress);
        $this->assertEquals('4f61c06feeb7971af6997125fe40d629c01af92f', $result->to);
        $this->assertEquals('NQ05 9VGU 0TYE NXBH MVLR E4JY UG6N 5701 MX9F', $result->toAddress);
        $this->assertEquals(2636710000, $result->value);
        $this->assertEquals(0, $result->fee);
    }

    public function testGetTransactionByBlockNumberAndIndexWhenNotFound()
    {
        $this->appendNextResponse('getTransaction/not-found.json');

        $result = $this->client->getTransactionByBlockNumberAndIndex(11608, 0);

        $body = $this->getLastRequestBody();
        $this->assertEquals('getTransactionByBlockNumberAndIndex', $body['method']);
        $this->assertEquals(11608, $body['params'][0]);
        $this->assertEquals(0, $body['params'][1]);

        $this->assertEquals(null, $result);
    }

    public function testGetTransactionByHash()
    {
        $this->appendNextResponse('getTransaction/full-transaction.json');

        $result = $this->client->getTransactionByHash('78957b87ab5546e11e9540ce5a37ebbf93a0ebd73c0ce05f137288f30ee9f430');

        $body = $this->getLastRequestBody();
        $this->assertEquals('getTransactionByHash', $body['method']);
        $this->assertEquals('78957b87ab5546e11e9540ce5a37ebbf93a0ebd73c0ce05f137288f30ee9f430', $body['params'][0]);

        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals('78957b87ab5546e11e9540ce5a37ebbf93a0ebd73c0ce05f137288f30ee9f430', $result->hash);
        $this->assertEquals(11608, $result->blockNumber);
        $this->assertEquals(0, $result->transactionIndex);
        $this->assertEquals('355b4fe2304a9c818b9f0c3c1aaaf4ad4f6a0279', $result->from);
        $this->assertEquals('NQ16 6MDL YQHG 9AE8 32UY 1GX1 MAPL MM7N L0KR', $result->fromAddress);
        $this->assertEquals('4f61c06feeb7971af6997125fe40d629c01af92f', $result->to);
        $this->assertEquals('NQ05 9VGU 0TYE NXBH MVLR E4JY UG6N 5701 MX9F', $result->toAddress);
        $this->assertEquals(2636710000, $result->value);
        $this->assertEquals(0, $result->fee);
    }

    public function testGetTransactionByHashWhenNotFound()
    {
        $this->appendNextResponse('getTransaction/not-found.json');

        $result = $this->client->getTransactionByHash('78957b87ab5546e11e9540ce5a37ebbf93a0ebd73c0ce05f137288f30ee9f430');

        $body = $this->getLastRequestBody();
        $this->assertEquals('getTransactionByHash', $body['method']);
        $this->assertEquals('78957b87ab5546e11e9540ce5a37ebbf93a0ebd73c0ce05f137288f30ee9f430', $body['params'][0]);

        $this->assertEquals(null, $result);
    }

    public function testGetTransactionReceipt()
    {
        $this->appendNextResponse('getTransactionReceipt/receipt.json');

        $result = $this->client->getTransactionReceipt('fd8e46ae55c5b8cd7cb086cf8d6c81f941a516d6148021d55f912fb2ca75cc8e');

        $body = $this->getLastRequestBody();
        $this->assertEquals('getTransactionReceipt', $body['method']);
        $this->assertEquals('fd8e46ae55c5b8cd7cb086cf8d6c81f941a516d6148021d55f912fb2ca75cc8e', $body['params'][0]);

        $this->assertInstanceOf(TransactionReceipt::class, $result);
        $this->assertEquals('fd8e46ae55c5b8cd7cb086cf8d6c81f941a516d6148021d55f912fb2ca75cc8e', $result->transactionHash);
        $this->assertEquals(-1, $result->transactionIndex);
        $this->assertEquals(11608, $result->blockNumber);
        $this->assertEquals('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', $result->blockHash);
        $this->assertEquals(1523412456, $result->timestamp);
        $this->assertEquals(718846, $result->confirmations);
    }

    public function testGetTransactionReceiptWhenNotFound()
    {
        $this->appendNextResponse('getTransactionReceipt/not-found.json');

        $result = $this->client->getTransactionReceipt('unknown');

        $body = $this->getLastRequestBody();
        $this->assertEquals('getTransactionReceipt', $body['method']);
        $this->assertEquals('unknown', $body['params'][0]);

        $this->assertNull($result);
    }

    public function testGetTransactionsByAddress()
    {
        $this->appendNextResponse('getTransactions/transactions-found.json');

        $result = $this->client->getTransactionsByAddress('NQ05 9VGU 0TYE NXBH MVLR E4JY UG6N 5701 MX9F');

        $body = $this->getLastRequestBody();
        $this->assertEquals('getTransactionsByAddress', $body['method']);
        $this->assertEquals('NQ05 9VGU 0TYE NXBH MVLR E4JY UG6N 5701 MX9F', $body['params'][0]);

        $this->assertCount(3, $result);
        $this->assertInstanceOf(Transaction::class, $result[0]);
        $this->assertEquals('a514abb3ee4d3fbedf8a91156fb9ec4fdaf32f0d3d3da3c1dbc5fd1ee48db43e', $result[0]->hash);
        $this->assertInstanceOf(Transaction::class, $result[1]);
        $this->assertEquals('c8c0f586b11c7f39873c3de08610d63e8bec1ceaeba5e8a3bb13c709b2935f73', $result[1]->hash);
        $this->assertInstanceOf(Transaction::class, $result[2]);
        $this->assertEquals('fd8e46ae55c5b8cd7cb086cf8d6c81f941a516d6148021d55f912fb2ca75cc8e', $result[2]->hash);
    }

    public function testGetTransactionsByAddressWhenNoFound()
    {
        $this->appendNextResponse('getTransactions/no-transactions-found.json');

        $result = $this->client->getTransactionsByAddress('NQ10 9VGU 0TYE NXBH MVLR E4JY UG6N 5701 MX9F');

        $body = $this->getLastRequestBody();
        $this->assertEquals('getTransactionsByAddress', $body['method']);
        $this->assertEquals('NQ10 9VGU 0TYE NXBH MVLR E4JY UG6N 5701 MX9F', $body['params'][0]);

        $this->assertEquals([], $result);
    }

    public function testGetMempoolContentHashesOnly()
    {
        $this->appendNextResponse('mempoolContent/hashes-only.json');

        $result = $this->client->getMempoolContent();

        $body = $this->getLastRequestBody();
        $this->assertEquals('mempoolContent', $body['method']);
        $this->assertEquals(false, $body['params'][0]);

        $this->assertCount(3, $result);
        $this->assertIsString($result[0]);
        $this->assertEquals('5bb722c2afe25c18ba33d453b3ac2c90ac278c595cc92f6188c8b699e8fb006a', $result[0]);
        $this->assertIsString($result[1]);
        $this->assertEquals('f59a30e0a7e3348ef569225db1f4c29026aeac4350f8c6e751f669eddce0c718', $result[1]);
        $this->assertIsString($result[2]);
        $this->assertEquals('9cd9c1d0ffcaebfcfe86bc2ae73b4e82a488de99c8e3faef92b05432bb94519c', $result[2]);
    }

    public function testGetMempoolContentFullTransactions()
    {
        $this->appendNextResponse('mempoolContent/full-transactions.json');

        $result = $this->client->getMempoolContent(true);

        $body = $this->getLastRequestBody();
        $this->assertEquals('mempoolContent', $body['method']);
        $this->assertEquals(true, $body['params'][0]);

        $this->assertCount(3, $result);
        $this->assertInstanceOf(Transaction::class, $result[0]);
        $this->assertEquals('5bb722c2afe25c18ba33d453b3ac2c90ac278c595cc92f6188c8b699e8fb006a', $result[0]->hash);
        $this->assertInstanceOf(Transaction::class, $result[1]);
        $this->assertEquals('f59a30e0a7e3348ef569225db1f4c29026aeac4350f8c6e751f669eddce0c718', $result[1]->hash);
        $this->assertInstanceOf(Transaction::class, $result[2]);
        $this->assertEquals('9cd9c1d0ffcaebfcfe86bc2ae73b4e82a488de99c8e3faef92b05432bb94519c', $result[2]->hash);
    }

    public function testGetMempoolWhenFull()
    {
        $this->appendNextResponse('mempool/mempool.json');

        $result = $this->client->getMempool();

        $body = $this->getLastRequestBody();
        $this->assertEquals('mempool', $body['method']);

        $this->assertInstanceOf(Mempool::class, $result);
        $this->assertEquals(3, $result->total);
        $this->assertEquals([1], $result->buckets);
        $this->assertEquals(3, $result->transactionsPerBucket[1]);
    }

    public function testGetMempoolWhenEmpty()
    {
        $this->appendNextResponse('mempool/mempool-empty.json');

        $result = $this->client->getMempool();

        $body = $this->getLastRequestBody();
        $this->assertEquals('mempool', $body['method']);

        $this->assertInstanceOf(Mempool::class, $result);
        $this->assertEquals(0, $result->total);
        $this->assertEquals([], $result->buckets);
        $this->assertEquals([], $result->transactionsPerBucket);
    }

    public function testGetMinFeePerByte()
    {
        $this->appendNextResponse('minFeePerByte/fee.json');

        $result = $this->client->getMinFeePerByte();

        $body = $this->getLastRequestBody();
        $this->assertEquals('minFeePerByte', $body['method']);

        $this->assertIsInt($result);
        $this->assertEquals(0, $result);
    }

    public function testSetMinFeePerByte()
    {
        $this->appendNextResponse('minFeePerByte/fee.json');

        $result = $this->client->setMinFeePerByte(0);

        $body = $this->getLastRequestBody();
        $this->assertEquals('minFeePerByte', $body['method']);
        $this->assertEquals(0, $body['params'][0]);

        $this->assertIsInt($result);
        $this->assertEquals(0, $result);
    }

    public function testGetMiningState()
    {
        $this->appendNextResponse('miningState/mining.json');

        $result = $this->client->getMiningState();

        $body = $this->getLastRequestBody();
        $this->assertEquals('mining', $body['method']);

        $this->assertIsBool($result);
        $this->assertEquals(false, $result);
    }

    public function testSetMiningState()
    {
        $this->appendNextResponse('miningState/mining.json');

        $result = $this->client->setMiningState(false);

        $body = $this->getLastRequestBody();
        $this->assertEquals('mining', $body['method']);
        $this->assertEquals(false, $body['params'][0]);

        $this->assertIsBool($result);
        $this->assertEquals(false, $result);
    }

    public function testGetHashrate()
    {
        $this->appendNextResponse('hashrate/hashrate.json');

        $result = $this->client->getHashrate();

        $body = $this->getLastRequestBody();
        $this->assertEquals('hashrate', $body['method']);

        $this->assertIsFloat($result);
        $this->assertEquals(52982.2731, $result);
    }

    public function testGetMinerThreads()
    {
        $this->appendNextResponse('minerThreads/threads.json');

        $result = $this->client->getMinerThreads();

        $body = $this->getLastRequestBody();
        $this->assertEquals('minerThreads', $body['method']);

        $this->assertIsInt($result);
        $this->assertEquals(2, $result);
    }

    public function testSetMinerThreads()
    {
        $this->appendNextResponse('minerThreads/threads.json');

        $result = $this->client->setMinerThreads(2);

        $body = $this->getLastRequestBody();
        $this->assertEquals('minerThreads', $body['method']);
        $this->assertEquals(2, $body['params'][0]);

        $this->assertIsInt($result);
        $this->assertEquals(2, $result);
    }

    public function testGetMinerAddress()
    {
        $this->appendNextResponse('minerAddress/address.json');

        $result = $this->client->getMinerAddress();

        $body = $this->getLastRequestBody();
        $this->assertEquals('minerAddress', $body['method']);

        $this->assertIsString($result);
        $this->assertEquals('NQ39 NY67 X0F0 UTQE 0YER 4JEU B67L UPP8 G0FM', $result);
    }

    public function testGetPool()
    {
        $this->appendNextResponse('pool/sushipool.json');

        $result = $this->client->getPool();

        $body = $this->getLastRequestBody();
        $this->assertEquals('pool', $body['method']);

        $this->assertIsString($result);
        $this->assertEquals('us.sushipool.com:443', $result);
    }

    public function testSetPool()
    {
        $this->appendNextResponse('pool/sushipool.json');

        $result = $this->client->setPool('us.sushipool.com:443');

        $body = $this->getLastRequestBody();
        $this->assertEquals('pool', $body['method']);
        $this->assertEquals('us.sushipool.com:443', $body['params'][0]);

        $this->assertIsString($result);
        $this->assertEquals('us.sushipool.com:443', $result);
    }

    public function testGetPoolWhenNoPool()
    {
        $this->appendNextResponse('pool/no-pool.json');

        $result = $this->client->getPool();

        $body = $this->getLastRequestBody();
        $this->assertEquals('pool', $body['method']);

        $this->assertEquals(null, $result);
    }

    public function testGetPoolConnectionState()
    {
        $this->appendNextResponse('pool/connection-state.json');

        $result = $this->client->getPoolConnectionState();

        $body = $this->getLastRequestBody();
        $this->assertEquals('poolConnectionState', $body['method']);

        $this->assertIsInt($result);
        $this->assertEquals(PoolConnectionState::Closed, $result);
    }

    public function testGetPoolConfirmedBalance()
    {
        $this->appendNextResponse('pool/confirmed-balance.json');

        $result = $this->client->getPoolConfirmedBalance();

        $body = $this->getLastRequestBody();
        $this->assertEquals('poolConfirmedBalance', $body['method']);

        $this->assertIsInt($result);
        $this->assertEquals(12000, $result);
    }

    public function testGetWork()
    {
        $this->appendNextResponse('getWork/work.json');

        $result = $this->client->getWork();

        $body = $this->getLastRequestBody();
        $this->assertEquals('getWork', $body['method']);

        $this->assertEquals('00015a7d47ddf5152a7d06a14ea291831c3fc7af20b88240c5ae839683021bcee3e279877b3de0da8ce8878bf225f6782a2663eff9a03478c15ba839fde9f1dc3dd9e5f0cd4dbc96a30130de130eb52d8160e9197e2ccf435d8d24a09b518a5e05da87a8658ed8c02531f66a7d31757b08c88d283654ed477e5e2fec21a7ca8449241e00d620000dc2fa5e763bda00000000', $result['data']);
        $this->assertEquals('11fad9806b8b4167517c162fa113c09606b44d24f8020804a0f756db085546ff585adfdedad9085d36527a8485b497728446c35b9b6c3db263c07dd0a1f487b1639aa37ff60ba3cf6ed8ab5146fee50a23ebd84ea37dca8c49b31e57d05c9e6c57f09a3b282b71ec2be66c1bc8268b5326bb222b11a0d0a4acd2a93c9e8a8713fe4383e9d5df3b1bf008c535281086b2bcc20e494393aea1475a5c3f13673de2cf7314d201b7cc7f01e0e6f0e07dd9249dc598f4e5ee8801f50000000000', $result['suffix']);
        $this->assertEquals(503371296, $result['target']);
        $this->assertEquals('nimiq-argon2', $result['algorithm']);
    }

    public function testGetWorkWithOverride()
    {
        $this->appendNextResponse('getWork/work.json');

        $result = $this->client->getWork('NQ46 NTNU QX94 MVD0 BBT0 GXAR QUHK VGNF 39ET', '');

        $body = $this->getLastRequestBody();
        $this->assertEquals('getWork', $body['method']);
        $this->assertEquals('NQ46 NTNU QX94 MVD0 BBT0 GXAR QUHK VGNF 39ET', $body['params'][0]);
        $this->assertEquals('', $body['params'][1]);

        $this->assertEquals('00015a7d47ddf5152a7d06a14ea291831c3fc7af20b88240c5ae839683021bcee3e279877b3de0da8ce8878bf225f6782a2663eff9a03478c15ba839fde9f1dc3dd9e5f0cd4dbc96a30130de130eb52d8160e9197e2ccf435d8d24a09b518a5e05da87a8658ed8c02531f66a7d31757b08c88d283654ed477e5e2fec21a7ca8449241e00d620000dc2fa5e763bda00000000', $result['data']);
        $this->assertEquals('11fad9806b8b4167517c162fa113c09606b44d24f8020804a0f756db085546ff585adfdedad9085d36527a8485b497728446c35b9b6c3db263c07dd0a1f487b1639aa37ff60ba3cf6ed8ab5146fee50a23ebd84ea37dca8c49b31e57d05c9e6c57f09a3b282b71ec2be66c1bc8268b5326bb222b11a0d0a4acd2a93c9e8a8713fe4383e9d5df3b1bf008c535281086b2bcc20e494393aea1475a5c3f13673de2cf7314d201b7cc7f01e0e6f0e07dd9249dc598f4e5ee8801f50000000000', $result['suffix']);
        $this->assertEquals(503371296, $result['target']);
        $this->assertEquals('nimiq-argon2', $result['algorithm']);
    }

    public function testGetBlockTemplate()
    {
        $this->appendNextResponse('getWork/block-template.json');

        $result = $this->client->getBlockTemplate();

        $body = $this->getLastRequestBody();
        $this->assertEquals('getBlockTemplate', $body['method']);

        $this->assertEquals(901883, $result['header']['height']);
        $this->assertEquals(503371226, $result['target']);
        $this->assertEquals('17e250f1977ae85bdbe09468efef83587885419ee1074ddae54d3fb5a96e1f54', $result['body']['hash']);
    }

    public function testGetBlockTemplateWithOverride()
    {
        $this->appendNextResponse('getWork/block-template.json');

        $result = $this->client->getBlockTemplate('NQ46 NTNU QX94 MVD0 BBT0 GXAR QUHK VGNF 39ET', '');

        $body = $this->getLastRequestBody();
        $this->assertEquals('getBlockTemplate', $body['method']);
        $this->assertEquals('NQ46 NTNU QX94 MVD0 BBT0 GXAR QUHK VGNF 39ET', $body['params'][0]);
        $this->assertEquals('', $body['params'][1]);

        $this->assertEquals(901883, $result['header']['height']);
        $this->assertEquals(503371226, $result['target']);
        $this->assertEquals('17e250f1977ae85bdbe09468efef83587885419ee1074ddae54d3fb5a96e1f54', $result['body']['hash']);
    }

    public function testSubmitBlock()
    {
        $this->appendNextResponse('submitBlock/submit.json');

        $blockHex = '0001000000000000000000000000000000000000000000'
            .'00000000000000000000000000000000000000000000000000000000000000000000000000000000'
            .'000000f6ba2bbf7e1478a209057000471d73fbdc28df0b717747d929cfde829c4120f62e02da3d16'
            .'2e20fa982029dbde9cc20f6b431ab05df1764f34af4c62a4f2b33f1f010000000000015ac3185f00'
            .'0134990001000000000000000000000000000000000000000007546573744e657400000000';

        $this->client->submitBlock($blockHex);

        $body = $this->getLastRequestBody();
        $this->assertEquals('submitBlock', $body['method']);
        $this->assertEquals($blockHex, $body['params'][0]);
    }

    public function testGetAccounts()
    {
        $this->appendNextResponse('accounts/accounts.json');

        $result = $this->client->getAccounts();

        $body = $this->getLastRequestBody();
        $this->assertEquals('accounts', $body['method']);

        $this->assertCount(3, $result);
        $this->assertInstanceOf(Account::class, $result[0]);
        $this->assertEquals('NQ33 Y4JH 0UTN 10DX 88FM 5MJB VHTM RGFU 4219', $result[0]->address);
        $this->assertInstanceOf(Account::class, $result[1]);
        $this->assertEquals('NQ82 4557 U5KC 98S8 X6HG GPHK 65VU 5YJ0 3BAV', $result[1]->address);
        $this->assertInstanceOf(Account::class, $result[2]);
        $this->assertEquals('NQ39 NY67 X0F0 UTQE 0YER 4JEU B67L UPP8 G0FM', $result[2]->address);
    }

    public function testCreateAccount()
    {
        $this->appendNextResponse('createAccount/new-account.json');

        $result = $this->client->createAccount();

        $body = $this->getLastRequestBody();
        $this->assertEquals('createAccount', $body['method']);

        $this->assertInstanceOf(Wallet::class, $result);
        $this->assertEquals('b6edcc7924af5a05af6087959c7233ec2cf1a5db', $result->id);
        $this->assertEquals('NQ46 NTNU QX94 MVD0 BBT0 GXAR QUHK VGNF 39ET', $result->address);
        $this->assertEquals('4f6d35cc47b77bf696b6cce72217e52edff972855bd17396b004a8453b020747', $result->publicKey);
    }

    public function testGetBalance()
    {
        $this->appendNextResponse('getBalance/balance.json');

        $result = $this->client->getBalance('NQ46 NTNU QX94 MVD0 BBT0 GXAR QUHK VGNF 39ET');

        $body = $this->getLastRequestBody();
        $this->assertEquals('getBalance', $body['method']);
        $this->assertEquals('NQ46 NTNU QX94 MVD0 BBT0 GXAR QUHK VGNF 39ET', $body['params'][0]);

        $this->assertIsInt($result);
        $this->assertEquals(1200000, $result);
    }

    public function testGetAccount()
    {
        $this->appendNextResponse('getAccount/account.json');

        $result = $this->client->getAccount('NQ46 NTNU QX94 MVD0 BBT0 GXAR QUHK VGNF 39ET');

        $body = $this->getLastRequestBody();
        $this->assertEquals('getAccount', $body['method']);
        $this->assertEquals('NQ46 NTNU QX94 MVD0 BBT0 GXAR QUHK VGNF 39ET', $body['params'][0]);

        $this->assertInstanceOf(Account::class, $result);
        $this->assertEquals('b6edcc7924af5a05af6087959c7233ec2cf1a5db', $result->id);
        $this->assertEquals('NQ46 NTNU QX94 MVD0 BBT0 GXAR QUHK VGNF 39ET', $result->address);
        $this->assertEquals(1200000, $result->balance);
        $this->assertEquals(AccountType::Basic, $result->type);
    }

    public function testGetBlockNumber()
    {
        $this->appendNextResponse('blockNumber/block-number.json');

        $result = $this->client->getBlockNumber();

        $body = $this->getLastRequestBody();
        $this->assertEquals('blockNumber', $body['method']);

        $this->assertIsInt($result);
        $this->assertEquals(748883, $result);
    }

    public function testGetBlockTransactionCountByHash()
    {
        $this->appendNextResponse('blockTransactionCount/found.json');

        $result = $this->client->getBlockTransactionCountByHash('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786');

        $body = $this->getLastRequestBody();
        $this->assertEquals('getBlockTransactionCountByHash', $body['method']);
        $this->assertEquals('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', $body['params'][0]);

        $this->assertIsInt($result);
        $this->assertEquals(2, $result);
    }

    public function testGetBlockTransactionCountByHashWhenNotFound()
    {
        $this->appendNextResponse('blockTransactionCount/not-found.json');

        $result = $this->client->getBlockTransactionCountByHash('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786');

        $body = $this->getLastRequestBody();
        $this->assertEquals('getBlockTransactionCountByHash', $body['method']);
        $this->assertEquals('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', $body['params'][0]);

        $this->assertEquals(null, $result);
    }

    public function testGetBlockTransactionCountByNumber()
    {
        $this->appendNextResponse('blockTransactionCount/found.json');

        $result = $this->client->getBlockTransactionCountByNumber(11608);

        $body = $this->getLastRequestBody();
        $this->assertEquals('getBlockTransactionCountByNumber', $body['method']);
        $this->assertEquals(11608, $body['params'][0]);

        $this->assertIsInt($result);
        $this->assertEquals(2, $result);
    }

    public function testGetBlockTransactionCountByNumberWhenNotFound()
    {
        $this->appendNextResponse('blockTransactionCount/not-found.json');

        $result = $this->client->getBlockTransactionCountByNumber(11608);

        $body = $this->getLastRequestBody();
        $this->assertEquals('getBlockTransactionCountByNumber', $body['method']);
        $this->assertEquals(11608, $body['params'][0]);

        $this->assertEquals(null, $result);
    }

    public function testGetBlockByHash()
    {
        $this->appendNextResponse('getBlock/block-found.json');

        $result = $this->client->getBlockByHash('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786');

        $body = $this->getLastRequestBody();
        $this->assertEquals('getBlockByHash', $body['method']);
        $this->assertEquals('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', $body['params'][0]);
        $this->assertEquals(false, $body['params'][1]);

        $this->assertInstanceOf(Block::class, $result);
        $this->assertEquals(11608, $result->number);
        $this->assertEquals('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', $result->hash);
        $this->assertEquals(739224, $result->confirmations);
        $this->assertEquals([
            '78957b87ab5546e11e9540ce5a37ebbf93a0ebd73c0ce05f137288f30ee9f430',
            'fd8e46ae55c5b8cd7cb086cf8d6c81f941a516d6148021d55f912fb2ca75cc8e',
        ], $result->transactions);
    }

    public function testGetBlockByHashWithTransactions()
    {
        $this->appendNextResponse('getBlock/block-with-transactions.json');

        $result = $this->client->getBlockByHash('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', true);

        $body = $this->getLastRequestBody();
        $this->assertEquals('getBlockByHash', $body['method']);
        $this->assertEquals('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', $body['params'][0]);
        $this->assertEquals(true, $body['params'][1]);

        $this->assertInstanceOf(Block::class, $result);
        $this->assertEquals(11608, $result->number);
        $this->assertEquals('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', $result->hash);
        $this->assertEquals(739501, $result->confirmations);

        $this->assertCount(2, $result->transactions);
        $this->assertInstanceOf(Transaction::class, $result->transactions[0]);
        $this->assertInstanceOf(Transaction::class, $result->transactions[1]);
    }

    public function testGetBlockByHashNotFound()
    {
        $this->appendNextResponse('getBlock/block-not-found.json');

        $result = $this->client->getBlockByHash('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786');

        $body = $this->getLastRequestBody();
        $this->assertEquals('getBlockByHash', $body['method']);
        $this->assertEquals('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', $body['params'][0]);
        $this->assertEquals(false, $body['params'][1]);

        $this->assertNull($result);
    }

    public function testGetBlockByNumber()
    {
        $this->appendNextResponse('getBlock/block-found.json');

        $result = $this->client->getBlockByNumber(11608);

        $body = $this->getLastRequestBody();
        $this->assertEquals('getBlockByNumber', $body['method']);
        $this->assertEquals(11608, $body['params'][0]);
        $this->assertEquals(false, $body['params'][1]);

        $this->assertInstanceOf(Block::class, $result);
        $this->assertEquals(11608, $result->number);
        $this->assertEquals('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', $result->hash);
        $this->assertEquals(739224, $result->confirmations);
        $this->assertEquals([
            '78957b87ab5546e11e9540ce5a37ebbf93a0ebd73c0ce05f137288f30ee9f430',
            'fd8e46ae55c5b8cd7cb086cf8d6c81f941a516d6148021d55f912fb2ca75cc8e',
        ], $result->transactions);
    }

    public function testGetBlockByNumberWithTransactions()
    {
        $this->appendNextResponse('getBlock/block-with-transactions.json');

        $result = $this->client->getBlockByNumber(11608, true);

        $body = $this->getLastRequestBody();
        $this->assertEquals('getBlockByNumber', $body['method']);
        $this->assertEquals(11608, $body['params'][0]);
        $this->assertEquals(true, $body['params'][1]);

        $this->assertInstanceOf(Block::class, $result);
        $this->assertEquals(11608, $result->number);
        $this->assertEquals('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', $result->hash);
        $this->assertEquals(739501, $result->confirmations);

        $this->assertCount(2, $result->transactions);
        $this->assertInstanceOf(Transaction::class, $result->transactions[0]);
        $this->assertInstanceOf(Transaction::class, $result->transactions[1]);
    }

    public function testGetBlockByNumberNotFound()
    {
        $this->appendNextResponse('getBlock/block-not-found.json');

        $result = $this->client->getBlockByNumber(11608);

        $body = $this->getLastRequestBody();
        $this->assertEquals('getBlockByNumber', $body['method']);
        $this->assertEquals(11608, $body['params'][0]);
        $this->assertEquals(false, $body['params'][1]);

        $this->assertNull($result);
    }

    public function testGetConstant()
    {
        $this->appendNextResponse('constant/constant.json');

        $result = $this->client->getConstant('BaseConsensus.MAX_ATTEMPTS_TO_FETCH');

        $body = $this->getLastRequestBody();
        $this->assertEquals('constant', $body['method']);
        $this->assertEquals('BaseConsensus.MAX_ATTEMPTS_TO_FETCH', $body['params'][0]);

        $this->assertIsInt($result);
        $this->assertEquals(5, $result);
    }

    public function testSetConstant()
    {
        $this->appendNextResponse('constant/constant.json');

        $result = $this->client->setConstant('BaseConsensus.MAX_ATTEMPTS_TO_FETCH', 10);

        $body = $this->getLastRequestBody();
        $this->assertEquals('constant', $body['method']);
        $this->assertEquals('BaseConsensus.MAX_ATTEMPTS_TO_FETCH', $body['params'][0]);
        $this->assertEquals(10, $body['params'][1]);

        $this->assertIsInt($result);
        $this->assertEquals(5, $result);
    }

    public function testResetConstant()
    {
        $this->appendNextResponse('constant/constant.json');

        $result = $this->client->resetConstant('BaseConsensus.MAX_ATTEMPTS_TO_FETCH');

        $body = $this->getLastRequestBody();
        $this->assertEquals('constant', $body['method']);
        $this->assertEquals('BaseConsensus.MAX_ATTEMPTS_TO_FETCH', $body['params'][0]);
        $this->assertEquals('reset', $body['params'][1]);

        $this->assertIsInt($result);
        $this->assertEquals(5, $result);
    }

    public function testSetLogLevel()
    {
        $this->appendNextResponse('log/log.json');

        $result = $this->client->setLogLevel('*', 'verbose');

        $body = $this->getLastRequestBody();
        $this->assertEquals('log', $body['method']);
        $this->assertEquals('*', $body['params'][0]);
        $this->assertEquals('verbose', $body['params'][1]);

        $this->assertTrue($result);
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
