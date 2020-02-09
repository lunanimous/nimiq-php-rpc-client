<?php

namespace Lunanimous\Rpc;

class NimiqClient extends Client
{
    /**
     * Gets the peer count.
     *
     * @return int Number of connected peers
     */
    public function getPeerCount()
    {
        return $this->request('peerCount');
    }

    /**
     * Gets the syncing status.
     *
     * @return array Sync status
     */
    public function getSyncingStatus()
    {
        return $this->request('syncing');
    }

    /**
     * Gets the consensus status.
     *
     * @return string Consensus status
     */
    public function getConsensusStatus()
    {
        return $this->request('consensus');
    }

    /**
     * Gets the list of peers.
     *
     * @return array List of peers
     */
    public function getPeerList()
    {
        return $this->request('peerList');
    }

    /**
     * Gets the peer.
     *
     * @param string $peer Peer name
     *
     * @return array Peer info
     */
    public function getPeer($peer)
    {
        return $this->request('peerState', $peer);
    }

    /**
     * Sets the state of the peer.
     *
     * @param string $peer    Peer name
     * @param string $command Command to perform (connect, disconnect, ban, unban)
     *
     * @return array Peer info
     */
    public function setPeerState($peer, $command)
    {
        return $this->request('peerState', $peer, $command);
    }

    /**
     * Sends a raw transaction.
     *
     * @param string $txHex Hex-encoded transaction
     *
     * @return string Transaction hash
     */
    public function sendRawTransaction($txHex)
    {
        return $this->request('sendRawTransaction', $txHex);
    }

    /**
     * Creates a raw transaction.
     *
     * @param Transaction $tx Transaction object
     *
     * @return string Hex-encoded transaction
     */
    public function createRawTransaction($tx)
    {
        return $this->request('createRawTransaction', $tx);
    }

    /**
     * Sends a transaction.
     *
     * @param Transaction $tx Transaction object
     *
     * @return string Transaction hash
     */
    public function sendTransaction($tx)
    {
        return $this->request('sendTransaction', $tx);
    }

    /**
     * Gets raw transaction info.
     *
     * @param string $txHex Hex-encoded transaction
     *
     * @return array Transaction info
     */
    public function getRawTransactionInfo($txHex)
    {
        return $this->request('getRawTransactionInfo', $txHex);
    }

    /**
     * Gets the transaction by block hash and transaction index.
     *
     * @param string $blockHash Hash of the block
     * @param int    $txIndex   Index of the transaction
     *
     * @return array Transaction info
     */
    public function getTransactionByBlockHashAndIndex($blockHash, $txIndex): Response
    {
        return $this->request('getTransactionByBlockHashAndIndex', $blockHash, $txIndex);
    }

    /**
     * Gets the transaction by block number and transaction index.
     *
     * @param int $blockNumber Number of the block
     * @param int $txIndex     Index of the transaction
     *
     * @return array Transaction info
     */
    public function getTransactionByBlockNumberAndIndex($blockNumber, $txIndex)
    {
        return $this->request('getTransactionByBlockNumberAndIndex', $blockNumber, $txIndex);
    }

    /**
     * Gets the transaction by hash.
     *
     * @param string $hash Hash of the transaction
     *
     * @return array Transaction info
     */
    public function getTransactionByHash($hash)
    {
        return $this->request('getTransactionByHash', $hash);
    }

    /**
     * Gets the transaction receipt.
     *
     * @param string $hash Hash of the transaction
     *
     * @return array Transaction receipt
     */
    public function getTransactionReceipt($hash)
    {
        return $this->request('getTransactionReceipt', $hash);
    }

    /**
     * Gets the transactions for an address.
     *
     * @param string $address Account address
     * @param int    $limit   Number of transactions to get
     *
     * @return array List of transactions
     */
    public function getTransactionsByAddress($address, $limit = 1000)
    {
        return $this->request('getTransactionsByAddress', $address, $limit);
    }

    /**
     * Gets the content of the mempool.
     *
     * @param bool $includeTransactions If true includes full transactions,
     *                                  if false includes only transaction hashes
     *
     * @return array List of transactions in mempool
     */
    public function getMempoolContent($includeTransactions = false)
    {
        return $this->request('mempoolContent', $includeTransactions);
    }

    /**
     * Gets mempool statistics.
     *
     * @return array Mempool stats
     */
    public function getMempool()
    {
        return $this->request('mempool');
    }

    /**
     * Gets the min fee per byte.
     *
     * @return int Current min fee per byte
     */
    public function getMinFeePerByte()
    {
        return $this->request('minFeePerByte');
    }

    /**
     * Sets the min fee per byte.
     *
     * @param int $minFee Min fee per byte
     *
     * @return int New min fee per byte
     */
    public function setMinFeePerByte($minFee)
    {
        return $this->request('minFeePerByte', $minFee);
    }

    /**
     * Gets the mining status.
     *
     * @return bool Mining status
     */
    public function getMiningStatus()
    {
        return $this->request('mining');
    }

    /**
     * Sets the mining status.
     *
     * @param bool $enabled Mining status
     *
     * @return bool Mining status
     */
    public function setMiningStatus($enabled)
    {
        return $this->request('minig', $enabled);
    }

    /**
     * Gets current hashrate.
     *
     * @return int Current hashrate
     */
    public function getHashrate()
    {
        return $this->request('hashrate');
    }

    /**
     * Gets current miner threads.
     *
     * @return int Current number of miner threads
     */
    public function getMinerThreads()
    {
        return $this->request('minerThreads');
    }

    /**
     * Sets miner threads.
     *
     * @param int $threads Number of threads to allocate
     *
     * @return int New number of miner threads
     */
    public function setMinerThreads($threads)
    {
        return $this->request('minerThreads', $threads);
    }

    /**
     * Gets the address used for mining rewards.
     *
     * @return string Address for mining rewards
     */
    public function getMinerAddress()
    {
        return $this->request('minerAddress');
    }

    /**
     * Gets the current mining pool.
     *
     * @return string Current pool URL
     */
    public function getPool()
    {
        return $this->request('pool');
    }

    /**
     * Sets the mining pool.
     *
     * @param bool|string $pool Mining pool string (url:port) or boolean
     *
     * @return string New pool URL
     */
    public function setPool($pool)
    {
        return $this->request('pool', $pool);
    }

    /**
     * Gets connection status to mining pool.
     *
     * @return int Pool connection status
     */
    public function getPoolConnectionState()
    {
        return $this->request('poolConnectionState');
    }

    /**
     * Gets confirmed mining pool balance.
     *
     * @return int Confirmed mining pool balance
     */
    public function getPoolConfirmedBalance()
    {
        return $this->request('poolConfirmedBalance');
    }

    /**
     * Gets work for next block.
     *
     * @param string $address      Mining address to use
     * @param string $extraDataHex Extra Data
     *
     * @return array Mining work
     */
    public function getWork($address, $extraDataHex)
    {
        return $this->request('getWork', $address, $extraDataHex);
    }

    /**
     * Gets block template for next block.
     *
     * @param string $address      Mining address to use
     * @param string $extraDataHex Hex-encoded extra data
     *
     * @return array Mining block template
     */
    public function getBlockTemplate($address, $extraDataHex)
    {
        return $this->request('getBlockTemplate', $address, $extraDataHex);
    }

    /**
     * Submit a mined block.
     *
     * @param string $blockHex Hex-encoded block
     */
    public function submitBlock($blockHex)
    {
        return $this->request('submitBlock', $blockHex);
    }

    /**
     * Gets the accounts stored in node.
     *
     * @return array List of accounts
     */
    public function getAccounts()
    {
        return $this->request('accounts');
    }

    /**
     * Creates a new account and stores it in node.
     *
     * @return array New account
     */
    public function createAccount()
    {
        return $this->request('createAccount');
    }

    /**
     * Gets balance.
     *
     * @param string $address Account address
     *
     * @return int Balance of account
     */
    public function getBalance($address)
    {
        return $this->request('getBalance', $address);
    }

    /**
     * Gets account detail.
     *
     * @param string $address
     *
     * @return array Account info
     */
    public function getAccount($address)
    {
        return $this->request('getAccount', $address);
    }

    /**
     * Gets current block height.
     *
     * @return int Current block height
     */
    public function getBlockNumber()
    {
        return $this->request('blockNumber');
    }

    /**
     * Gets number of transaction in block by block hash.
     *
     * @param string $blockHash Hash of the block
     *
     * @return int Number of transactions in block
     */
    public function getBlockTransactionCountByHash($blockHash)
    {
        return $this->request('getBlockTransactionCountByHash');
    }

    /**
     * Gets number of transactions in block by block number.
     *
     * @param int $blockNumber Number of the block
     *
     * @return int Number of transactions in block
     */
    public function getBlockTransactionCountByNumber($blockNumber)
    {
        return $this->request('getBlockTransactionCountByNumber');
    }

    /**
     * Gets the block detail by block hash.
     *
     * @param string $blockHash           Hash of the block
     * @param bool   $includeTransactions If true includes full transactions,
     *                                    if false includes only transaction hashes
     *
     * @return array Block info
     */
    public function getBlockByHash($blockHash, $includeTransactions = false)
    {
        return $this->request('getBlockByNumber', $blockHash, $includeTransactions);
    }

    /**
     * Gets the block detail by block number.
     *
     * @param int  $blockNumber         Number of the block
     * @param bool $includeTransactions If true includes full transactions,
     *                                  if false includes only transaction hashes
     *
     * @return array Block info
     */
    public function getBlockByNumber($blockNumber, $includeTransactions = false)
    {
        return $this->request('getBlockByNumber', $blockNumber, $includeTransactions);
    }

    /**
     * Gets the value of a constant.
     *
     * @param string $constant Name of the constant
     *
     * @return int Value of the constant
     */
    public function getConstant($constant)
    {
        return $this->request('constant', $constant);
    }

    /**
     * Sets the value of a constants.
     *
     * @param string $constant Name of the constant
     * @param int    $value    Value to set
     *
     * @return int Value of the constant
     */
    public function setConstant($constant, $value)
    {
        return $this->request('constant', $constant, $value);
    }

    /**
     * Resets the constant to default value.
     *
     * * @param string $constant Name of the constant
     *
     * @return int Value of the constant
     */
    public function resetConstant($constant)
    {
        return $this->request('constant', $constant, 'reset');
    }

    /**
     * Sets log level.
     *
     * @param string $tag   Log tag, use '*' to set all
     * @param string $level Log level to set (trace, verbose, debug, info, warn, error, assert)
     *
     * @return bool true if set successfully, otherwise false
     */
    public function setLogLevel($tag, $level)
    {
        return $this->request('log', $tag, $level);
    }
}
