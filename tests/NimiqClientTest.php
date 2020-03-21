<?php

use Lunanimous\Rpc\Constants\AccountType;
use Lunanimous\Rpc\Constants\AddressState;
use Lunanimous\Rpc\Constants\ConnectionState;
use Lunanimous\Rpc\Constants\ConsensusState;
use Lunanimous\Rpc\Constants\PeerStateCommand;
use Lunanimous\Rpc\Models\Mempool;
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

    public function testSendRawTransaction()
    {
        $this->assertTrue(false);
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

    public function testSendTransaction()
    {
        $this->assertTrue(false);
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

    public function testGetTransactionByBlockHashAndIndex()
    {
        $this->appendNextResponse('getTransaction/full-transaction.json');

        $result = $this->client->getTransactionByBlockHashAndIndex('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', 0);

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'getTransactionByBlockHashAndIndex');
        $this->assertEquals($body['params'][0], 'bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786');
        $this->assertEquals($body['params'][1], 0);

        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals($result->hash, '78957b87ab5546e11e9540ce5a37ebbf93a0ebd73c0ce05f137288f30ee9f430');
        $this->assertEquals($result->blockHash, 'bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786');
        $this->assertEquals($result->transactionIndex, 0);
        $this->assertEquals($result->from, '355b4fe2304a9c818b9f0c3c1aaaf4ad4f6a0279');
        $this->assertEquals($result->fromAddress, 'NQ16 6MDL YQHG 9AE8 32UY 1GX1 MAPL MM7N L0KR');
        $this->assertEquals($result->to, '4f61c06feeb7971af6997125fe40d629c01af92f');
        $this->assertEquals($result->toAddress, 'NQ05 9VGU 0TYE NXBH MVLR E4JY UG6N 5701 MX9F');
        $this->assertEquals($result->value, 2636710000);
        $this->assertEquals($result->fee, 0);
    }

    public function testGetTransactionByBlockHashAndIndexWhenNotFound()
    {
        $this->appendNextResponse('getTransaction/not-found.json');

        $result = $this->client->getTransactionByBlockHashAndIndex('bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786', 5);

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'getTransactionByBlockHashAndIndex');
        $this->assertEquals($body['params'][0], 'bc3945d22c9f6441409a6e539728534a4fc97859bda87333071fad9dad942786');
        $this->assertEquals($body['params'][1], 5);

        $this->assertEquals($result, null);
    }

    public function testGetTransactionByBlockNumberAndIndex()
    {
        $this->appendNextResponse('getTransaction/full-transaction.json');

        $result = $this->client->getTransactionByBlockNumberAndIndex(11608, 0);

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'getTransactionByBlockNumberAndIndex');
        $this->assertEquals($body['params'][0], 11608);
        $this->assertEquals($body['params'][1], 0);

        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals($result->hash, '78957b87ab5546e11e9540ce5a37ebbf93a0ebd73c0ce05f137288f30ee9f430');
        $this->assertEquals($result->blockNumber, 11608);
        $this->assertEquals($result->transactionIndex, 0);
        $this->assertEquals($result->from, '355b4fe2304a9c818b9f0c3c1aaaf4ad4f6a0279');
        $this->assertEquals($result->fromAddress, 'NQ16 6MDL YQHG 9AE8 32UY 1GX1 MAPL MM7N L0KR');
        $this->assertEquals($result->to, '4f61c06feeb7971af6997125fe40d629c01af92f');
        $this->assertEquals($result->toAddress, 'NQ05 9VGU 0TYE NXBH MVLR E4JY UG6N 5701 MX9F');
        $this->assertEquals($result->value, 2636710000);
        $this->assertEquals($result->fee, 0);
    }

    public function testGetTransactionByBlockNumberAndIndexWhenNotFound()
    {
        $this->appendNextResponse('getTransaction/not-found.json');

        $result = $this->client->getTransactionByBlockNumberAndIndex(11608, 0);

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'getTransactionByBlockNumberAndIndex');
        $this->assertEquals($body['params'][0], 11608);
        $this->assertEquals($body['params'][1], 0);

        $this->assertEquals($result, null);
    }

    public function testGetTransactionByHash()
    {
        $this->appendNextResponse('getTransaction/full-transaction.json');

        $result = $this->client->getTransactionByHash('78957b87ab5546e11e9540ce5a37ebbf93a0ebd73c0ce05f137288f30ee9f430');

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'getTransactionByHash');
        $this->assertEquals($body['params'][0], '78957b87ab5546e11e9540ce5a37ebbf93a0ebd73c0ce05f137288f30ee9f430');

        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals($result->hash, '78957b87ab5546e11e9540ce5a37ebbf93a0ebd73c0ce05f137288f30ee9f430');
        $this->assertEquals($result->blockNumber, 11608);
        $this->assertEquals($result->transactionIndex, 0);
        $this->assertEquals($result->from, '355b4fe2304a9c818b9f0c3c1aaaf4ad4f6a0279');
        $this->assertEquals($result->fromAddress, 'NQ16 6MDL YQHG 9AE8 32UY 1GX1 MAPL MM7N L0KR');
        $this->assertEquals($result->to, '4f61c06feeb7971af6997125fe40d629c01af92f');
        $this->assertEquals($result->toAddress, 'NQ05 9VGU 0TYE NXBH MVLR E4JY UG6N 5701 MX9F');
        $this->assertEquals($result->value, 2636710000);
        $this->assertEquals($result->fee, 0);
    }

    public function testGetTransactionByHashWhenNotFound()
    {
        $this->appendNextResponse('getTransaction/not-found.json');

        $result = $this->client->getTransactionByHash('78957b87ab5546e11e9540ce5a37ebbf93a0ebd73c0ce05f137288f30ee9f430');

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'getTransactionByHash');
        $this->assertEquals($body['params'][0], '78957b87ab5546e11e9540ce5a37ebbf93a0ebd73c0ce05f137288f30ee9f430');

        $this->assertEquals($result, null);
    }

    public function testGetTransactionsByAddress()
    {
        $this->appendNextResponse('getTransactions/transactions-found.json');

        $result = $this->client->getTransactionsByAddress('NQ05 9VGU 0TYE NXBH MVLR E4JY UG6N 5701 MX9F');

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'getTransactionsByAddress');
        $this->assertEquals($body['params'][0], 'NQ05 9VGU 0TYE NXBH MVLR E4JY UG6N 5701 MX9F');

        $this->assertCount(3, $result);
        $this->assertInstanceOf(Transaction::class, $result[0]);
        $this->assertEquals($result[0]->hash, 'a514abb3ee4d3fbedf8a91156fb9ec4fdaf32f0d3d3da3c1dbc5fd1ee48db43e');
        $this->assertInstanceOf(Transaction::class, $result[1]);
        $this->assertEquals($result[1]->hash, 'c8c0f586b11c7f39873c3de08610d63e8bec1ceaeba5e8a3bb13c709b2935f73');
        $this->assertInstanceOf(Transaction::class, $result[2]);
        $this->assertEquals($result[2]->hash, 'fd8e46ae55c5b8cd7cb086cf8d6c81f941a516d6148021d55f912fb2ca75cc8e');
    }

    public function testGetTransactionsByAddressWhenNoFound()
    {
        $this->appendNextResponse('getTransactions/no-transactions-found.json');

        $result = $this->client->getTransactionsByAddress('NQ10 9VGU 0TYE NXBH MVLR E4JY UG6N 5701 MX9F');

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'getTransactionsByAddress');
        $this->assertEquals($body['params'][0], 'NQ10 9VGU 0TYE NXBH MVLR E4JY UG6N 5701 MX9F');

        $this->assertEquals($result, []);
    }

    public function testGetMempoolContentHashesOnly()
    {
        $this->appendNextResponse('mempoolContent/hashes-only.json');

        $result = $this->client->getMempoolContent();

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'mempoolContent');
        $this->assertEquals($body['params'][0], false);

        $this->assertCount(3, $result);
        $this->assertIsString($result[0]);
        $this->assertEquals($result[0], '5bb722c2afe25c18ba33d453b3ac2c90ac278c595cc92f6188c8b699e8fb006a');
        $this->assertIsString($result[1]);
        $this->assertEquals($result[1], 'f59a30e0a7e3348ef569225db1f4c29026aeac4350f8c6e751f669eddce0c718');
        $this->assertIsString($result[2]);
        $this->assertEquals($result[2], '9cd9c1d0ffcaebfcfe86bc2ae73b4e82a488de99c8e3faef92b05432bb94519c');
    }

    public function testGetMempoolContentFullTransactions()
    {
        $this->appendNextResponse('mempoolContent/full-transactions.json');

        $result = $this->client->getMempoolContent(true);

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'mempoolContent');
        $this->assertEquals($body['params'][0], true);

        $this->assertCount(3, $result);
        $this->assertInstanceOf(Transaction::class, $result[0]);
        $this->assertEquals($result[0]->hash, '5bb722c2afe25c18ba33d453b3ac2c90ac278c595cc92f6188c8b699e8fb006a');
        $this->assertInstanceOf(Transaction::class, $result[1]);
        $this->assertEquals($result[1]->hash, 'f59a30e0a7e3348ef569225db1f4c29026aeac4350f8c6e751f669eddce0c718');
        $this->assertInstanceOf(Transaction::class, $result[2]);
        $this->assertEquals($result[2]->hash, '9cd9c1d0ffcaebfcfe86bc2ae73b4e82a488de99c8e3faef92b05432bb94519c');
    }

    public function testGetMempoolWhenFull()
    {
        $this->appendNextResponse('mempool/mempool.json');

        $result = $this->client->getMempool();

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'mempool');

        $this->assertInstanceOf(Mempool::class, $result);
        $this->assertEquals($result->total, 3);
        $this->assertEquals($result->buckets, [1]);
        $this->assertEquals($result->transactionsPerBucket[1], 3);
    }

    public function testGetMempoolWhenEmpty()
    {
        $this->appendNextResponse('mempool/mempool-empty.json');

        $result = $this->client->getMempool();

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'mempool');

        $this->assertInstanceOf(Mempool::class, $result);
        $this->assertEquals($result->total, 0);
        $this->assertEquals($result->buckets, []);
        $this->assertEquals($result->transactionsPerBucket, []);
    }

    public function testGetMinFeePerByte()
    {
        $this->appendNextResponse('minFeePerByte/fee.json');

        $result = $this->client->getMinFeePerByte();

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'minFeePerByte');

        $this->assertIsInt($result);
        $this->assertEquals($result, 0);
    }

    public function testSetMinFeePerByte()
    {
        $this->appendNextResponse('minFeePerByte/fee.json');

        $result = $this->client->setMinFeePerByte(0);

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'minFeePerByte');
        $this->assertEquals($body['params'][0], 0);

        $this->assertIsInt($result);
        $this->assertEquals($result, 0);
    }

    public function testGetMiningState()
    {
        $this->appendNextResponse('miningState/mining.json');

        $result = $this->client->getMiningState();

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'mining');

        $this->assertIsBool($result);
        $this->assertEquals($result, false);
    }

    public function testSetMiningState()
    {
        $this->appendNextResponse('miningState/mining.json');

        $result = $this->client->setMiningState(false);

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'mining');
        $this->assertEquals($body['params'][0], false);

        $this->assertIsBool($result);
        $this->assertEquals($result, false);
    }

    public function testGetHashrate()
    {
        $this->appendNextResponse('hashrate/hashrate.json');

        $result = $this->client->getHashrate();

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'hashrate');

        $this->assertIsFloat($result);
        $this->assertEquals($result, 52982.2731);
    }

    public function testGetMinerThreads()
    {
        $this->appendNextResponse('minerThreads/threads.json');

        $result = $this->client->getMinerThreads();

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'minerThreads');

        $this->assertIsInt($result);
        $this->assertEquals($result, 2);
    }

    public function testSetMinerThreads()
    {
        $this->appendNextResponse('minerThreads/threads.json');

        $result = $this->client->setMinerThreads(2);

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'minerThreads');
        $this->assertEquals($body['params'][0], 2);

        $this->assertIsInt($result);
        $this->assertEquals($result, 2);
    }

    public function testGetMinerAddress()
    {
        $this->appendNextResponse('minerAddress/address.json');

        $result = $this->client->getMinerAddress();

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'minerAddress');

        $this->assertIsString($result);
        $this->assertEquals($result, 'NQ39 NY67 X0F0 UTQE 0YER 4JEU B67L UPP8 G0FM');
    }

    public function testGetPool()
    {
        $this->assertTrue(false);
    }

    public function testGetPoolWhenNoPool()
    {
        $this->appendNextResponse('pool/no-pool.json');

        $result = $this->client->getPool();

        $body = $this->getLastRequestBody();
        $this->assertEquals($body['method'], 'pool');

        $this->assertEquals($result, null);
    }

    public function testGetPoolConnectionState()
    {
        $this->assertTrue(false);
    }

    public function testGetPoolConfirmedBalance()
    {
        $this->assertTrue(false);
    }

    public function testGetWork()
    {
        $this->assertTrue(false);
    }

    public function testGetBlockTemplate()
    {
        $this->assertTrue(false);
    }

    public function submitBlock()
    {
        $this->assertTrue(false);
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
