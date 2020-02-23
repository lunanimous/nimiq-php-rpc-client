<?php

namespace Lunanimous\Rpc;

class NimiqClient extends Client
{
    /**
     * Returns number of peers currently connected to the client.
     *
     * @return int number of connected peers
     */
    public function getPeerCount()
    {
        return $this->request('peerCount');
    }

    /**
     * Returns an object with data about the sync status or false.
     *
     * @return array|bool object with sync status data or false, when not syncing
     */
    public function getSyncingState()
    {
        // TODO: create class for sync status data
        return $this->request('syncing');
    }

    /**
     * Returns information on the current consensus state.
     *
     * @return string string describing the consensus state. "established" is the value for a good state, other
     *                values indicate bad state.
     */
    public function getConsensusState()
    {
        return $this->request('consensus');
    }

    /**
     * Returns list of peers known to the client.
     *
     * @return array list of peers
     */
    public function getPeerList()
    {
        return $this->request('peerList');
    }

    /**
     * Returns the state of the peer.
     *
     * @param string $peer address of the peer
     *
     * @return array current state of the peer
     */
    public function getPeer($peer)
    {
        return $this->request('peerState', $peer);
    }

    /**
     * Sets the state of the peer.
     *
     * @param string $peer    address of the peer
     * @param string $command command to perform (on of "connect", "disconnect", "ban", "unban")
     *
     * @return array new state of the peer
     */
    public function setPeerState($peer, $command)
    {
        return $this->request('peerState', $peer, $command);
    }

    /**
     * Sends a signed message call transaction or a contract creation, if the data field contains code.
     *
     * @param string $txHex hex-encoded signed transaction
     *
     * @return string hex-encoded transaction hash
     */
    public function sendRawTransaction($txHex)
    {
        return $this->request('sendRawTransaction', $txHex);
    }

    /**
     * Creates and signs a transaction without sending it. The transaction can then be send via sendRawTransaction
     * without accidentally replaying it.
     *
     * @param OutgoingTransaction $tx transaction object
     *
     * @return string hex-encoded transaction
     */
    public function createRawTransaction($tx)
    {
        // TODO: create transaction class
        return $this->request('createRawTransaction', $tx);
    }

    /**
     * Creates new message call transaction or a contract creation, if the data field contains code.
     *
     * @param OutgoingTransaction $tx transaction object
     *
     * @return string hex-encoded transaction hash
     */
    public function sendTransaction($tx)
    {
        // TODO: create transaction class
        return $this->request('sendTransaction', $tx);
    }

    /**
     * Deserializes hex-encoded transaction and returns a transaction object.
     *
     * @param string $txHex hex-encoded transaction
     *
     * @return Transaction transaction object
     */
    public function getRawTransactionInfo($txHex)
    {
        // TODO: create transaction class
        return $this->request('getRawTransactionInfo', $txHex);
    }

    /**
     * Returns information about a transaction by block hash and transaction index position.
     *
     * @param string $blockHash hash of the block containing the transaction
     * @param int    $txIndex   index of the transaction in the block
     *
     * @return null|Transaction transaction object, or null when no transaction was found
     */
    public function getTransactionByBlockHashAndIndex($blockHash, $txIndex)
    {
        // TODO: create transaction class
        return $this->request('getTransactionByBlockHashAndIndex', $blockHash, $txIndex);
    }

    /**
     * Returns information about a transaction by block number and transaction index position.
     *
     * @param int $blockNumber height of the block containing the transaction
     * @param int $txIndex     index of the transaction in the block
     *
     * @return null|Transaction transaction object, or null when no transaction was found
     */
    public function getTransactionByBlockNumberAndIndex($blockNumber, $txIndex)
    {
        // TODO: create transaction class
        return $this->request('getTransactionByBlockNumberAndIndex', $blockNumber, $txIndex);
    }

    /**
     * Returns the information about a transaction requested by transaction hash.
     *
     * @param string $hash hash of the transaction
     *
     * @return null|Transaction transaction object, or null when no transaction was found
     */
    public function getTransactionByHash($hash)
    {
        // TODO: create transaction class
        return $this->request('getTransactionByHash', $hash);
    }

    /**
     * Returns the receipt of a transaction by transaction hash. The receipt is not available for pending transactions.
     *
     * @param string $hash hash of the transaction
     *
     * @return array transaction receipt
     */
    public function getTransactionReceipt($hash)
    {
        return $this->request('getTransactionReceipt', $hash);
    }

    /**
     * Returns the latest transactions successfully performed by or for an address. This information might change
     * when blocks are rewinded on the local state due to forks.
     *
     * @param string $address account address
     * @param int    $limit   (optional, default 1000) number of transactions to return
     *
     * @return Transaction[] array of transactions linked to the requested address
     */
    public function getTransactionsByAddress($address, $limit = 1000)
    {
        return $this->request('getTransactionsByAddress', $address, $limit);
    }

    /**
     * Returns transactions that are currently in the mempool.
     *
     * @param bool $includeTransactions if true includes full transactions,
     *                                  if false includes only transaction hashes
     *
     * @return Transaction[] array of transactions (either represented by the transaction hash or a transaction object)
     */
    public function getMempoolContent($includeTransactions = false)
    {
        return $this->request('mempoolContent', $includeTransactions);
    }

    /**
     * Returns information on the current mempool situation. This will provide an overview of the number of
     * transactions sorted into buckets based on their fee per byte (in smallest unit).
     *
     * @return array mempool information
     */
    public function getMempool()
    {
        return $this->request('mempool');
    }

    /**
     * Returns the current minimum fee per byte.
     *
     * @return int current minimum fee per byte
     */
    public function getMinFeePerByte()
    {
        return $this->request('minFeePerByte');
    }

    /**
     * Sets the minimum fee per byte.
     *
     * @param int $minFee minimum fee per byte
     *
     * @return int new minimum fee per byte
     */
    public function setMinFeePerByte($minFee)
    {
        return $this->request('minFeePerByte', $minFee);
    }

    /**
     * Returns true if client is actively mining new blocks.
     *
     * @return bool true if the client is mining, otherwise false
     */
    public function getMiningState()
    {
        return $this->request('mining');
    }

    /**
     * Enables or disables the miner.
     *
     * @param bool $enabled true to start the miner, false to stop
     *
     * @return bool true if the client is mining, otherwise false
     */
    public function setMiningState($enabled)
    {
        return $this->request('mining', $enabled);
    }

    /**
     * Returns the number of hashes per second that the node is mining with.
     *
     * @return float number of hashes per second
     */
    public function getHashrate()
    {
        return $this->request('hashrate');
    }

    /**
     * Returns the number of CPU threads the miner is using.
     *
     * @return int current number of miner threads
     */
    public function getMinerThreads()
    {
        return $this->request('minerThreads');
    }

    /**
     * Sets the number of CPU threads the miner can use.
     *
     * @param int $threads number of threads to allocate
     *
     * @return int new number of miner threads
     */
    public function setMinerThreads($threads)
    {
        return $this->request('minerThreads', $threads);
    }

    /**
     * Returns the miner address.
     *
     * @return string miner address
     */
    public function getMinerAddress()
    {
        return $this->request('minerAddress');
    }

    /**
     * Returns the current pool.
     *
     * @return null|string current pool, or null if not set
     */
    public function getPool()
    {
        return $this->request('pool');
    }

    /**
     * Sets the mining pool.
     *
     * @param bool|string $pool mining pool string (url:port) or boolean to enable/disable pool mining
     *
     * @return null|string new mining pool, or null if not enabled
     */
    public function setPool($pool)
    {
        return $this->request('pool', $pool);
    }

    /**
     * Returns the connection state to mining pool.
     *
     * @return int mining pool connection state (0: connected, 1: connecting, 2: closed)
     */
    public function getPoolConnectionState()
    {
        return $this->request('poolConnectionState');
    }

    /**
     * Returns the confirmed mining pool balance.
     *
     * @return float confirmed mining pool balance (in smallest unit)
     */
    public function getPoolConfirmedBalance()
    {
        return $this->request('poolConfirmedBalance');
    }

    /**
     * Returns instructions to mine the next block. This will consider pool instructions when connected to a pool.
     *
     * @param string $address      address to use as a miner for this block. this overrides the address provided
     *                             during startup or from the pool.
     * @param string $extraDataHex hex-encoded value for the extra data field. this overrides the address provided
     *                             during startup or from the pool.
     *
     * @return array mining work instructions
     */
    public function getWork($address, $extraDataHex)
    {
        return $this->request('getWork', $address, $extraDataHex);
    }

    /**
     * Returns a template to build the next block for mining. This will consider pool instructions when connected
     * to a pool.
     *
     * @param string $address      address to use as a miner for this block. this overrides the address provided
     *                             during startup or from the pool.
     * @param string $extraDataHex hex-encoded value for the extra data field. this overrides the address provided
     *                             during startup or from the pool.
     *
     * @return array mining block template
     */
    public function getBlockTemplate($address, $extraDataHex)
    {
        return $this->request('getBlockTemplate', $address, $extraDataHex);
    }

    /**
     * Submits a block to the node. When the block is valid, the node will forward it to other nodes in the network.
     *
     * @param string $blockHex hex-encoded full block (including header, interlink and body). when submitting work
     *                         from getWork, remember to include the suffix.
     */
    public function submitBlock($blockHex)
    {
        return $this->request('submitBlock', $blockHex);
    }

    /**
     * Returns a list of addresses owned by client.
     *
     * @return array array of accounts owned by the client
     */
    public function getAccounts()
    {
        return $this->request('accounts');
    }

    /**
     * Creates a new account and stores its private key in the client store.
     *
     * @return array information on the wallet that was created using the command
     */
    public function createAccount()
    {
        return $this->request('createAccount');
    }

    /**
     * Returns the balance of the account of given address.
     *
     * @param string $address address to check for balance
     *
     * @return float the current balance at the specified address (in smallest unit)
     */
    public function getBalance($address)
    {
        return $this->request('getBalance', $address);
    }

    /**
     * Returns details for the account of given address.
     *
     * @param string $address address for which to get account details
     *
     * @return array details about the account. returns the default empty basic account for non-existing accounts.
     */
    public function getAccount($address)
    {
        return $this->request('getAccount', $address);
    }

    /**
     * Returns the height of most recent block.
     *
     * @return int current block height the client is on
     */
    public function getBlockNumber()
    {
        return $this->request('blockNumber');
    }

    /**
     * Returns the number of transactions in a block from a block matching the given block hash.
     *
     * @param string $blockHash hash of the block
     *
     * @return null|int number of transactions in the block found, or null when no block was found
     */
    public function getBlockTransactionCountByHash($blockHash)
    {
        return $this->request('getBlockTransactionCountByHash');
    }

    /**
     * Returns the number of transactions in a block matching the given block number.
     *
     * @param int $blockNumber height of the block
     *
     * @return null|int number of transactions in the block found, or null when no block was found
     */
    public function getBlockTransactionCountByNumber($blockNumber)
    {
        return $this->request('getBlockTransactionCountByNumber');
    }

    /**
     * Returns information about a block by hash.
     *
     * @param string $blockHash           hash of the block to gather information on
     * @param bool   $includeTransactions if true includes full transactions,
     *                                    if false (default) includes only transaction hashes
     *
     * @return null|array block object, or null when no block was found
     */
    public function getBlockByHash($blockHash, $includeTransactions = false)
    {
        return $this->request('getBlockByNumber', $blockHash, $includeTransactions);
    }

    /**
     * Returns information about a block by block number.
     *
     * @param int  $blockNumber         height of the block to gather information on
     * @param bool $includeTransactions if true includes full transactions,
     *                                  if false (default) includes only transaction hashes
     *
     * @return null|array block object, or null when no block was found
     */
    public function getBlockByNumber($blockNumber, $includeTransactions = false)
    {
        return $this->request('getBlockByNumber', $blockNumber, $includeTransactions);
    }

    /**
     * Returns the value of a constant.
     *
     * @param string $constant name of the constant
     *
     * @return int current value of the constant
     */
    public function getConstant($constant)
    {
        return $this->request('constant', $constant);
    }

    /**
     * Sets the value of a constants.
     *
     * @param string $constant name of the constant
     * @param int    $value    value to set
     *
     * @return int new value of the constant
     */
    public function setConstant($constant, $value)
    {
        return $this->request('constant', $constant, $value);
    }

    /**
     * Resets the constant to default value.
     *
     * @param string $constant name of the constant
     *
     * @return int new value of the constant
     */
    public function resetConstant($constant)
    {
        return $this->request('constant', $constant, 'reset');
    }

    /**
     * Sets the log level of the node.
     *
     * @param string $tag   if '*' the log level is set globally, otherwise the log level is applied only on this tag
     * @param string $level minimum log level to display (trace, verbose, debug, info, warn, error, assert)
     *
     * @return bool true if set successfully, otherwise false
     */
    public function setLogLevel($tag, $level)
    {
        return $this->request('log', $tag, $level);
    }
}
