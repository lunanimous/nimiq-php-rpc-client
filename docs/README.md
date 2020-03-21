# Nimiq RPC Client for PHP

## Table of Contents

* [NimiqClient](#nimiqclient)
    * [getPeerCount](#getpeercount)
    * [getSyncingState](#getsyncingstate)
    * [getConsensusState](#getconsensusstate)
    * [getPeerList](#getpeerlist)
    * [getPeer](#getpeer)
    * [setPeerState](#setpeerstate)
    * [sendRawTransaction](#sendrawtransaction)
    * [createRawTransaction](#createrawtransaction)
    * [sendTransaction](#sendtransaction)
    * [getRawTransactionInfo](#getrawtransactioninfo)
    * [getTransactionByBlockHashAndIndex](#gettransactionbyblockhashandindex)
    * [getTransactionByBlockNumberAndIndex](#gettransactionbyblocknumberandindex)
    * [getTransactionByHash](#gettransactionbyhash)
    * [getTransactionReceipt](#gettransactionreceipt)
    * [getTransactionsByAddress](#gettransactionsbyaddress)
    * [getMempoolContent](#getmempoolcontent)
    * [getMempool](#getmempool)
    * [getMinFeePerByte](#getminfeeperbyte)
    * [setMinFeePerByte](#setminfeeperbyte)
    * [getMiningState](#getminingstate)
    * [setMiningState](#setminingstate)
    * [getHashrate](#gethashrate)
    * [getMinerThreads](#getminerthreads)
    * [setMinerThreads](#setminerthreads)
    * [getMinerAddress](#getmineraddress)
    * [getPool](#getpool)
    * [setPool](#setpool)
    * [getPoolConnectionState](#getpoolconnectionstate)
    * [getPoolConfirmedBalance](#getpoolconfirmedbalance)
    * [getWork](#getwork)
    * [getBlockTemplate](#getblocktemplate)
    * [submitBlock](#submitblock)
    * [getAccounts](#getaccounts)
    * [createAccount](#createaccount)
    * [getBalance](#getbalance)
    * [getAccount](#getaccount)
    * [getBlockNumber](#getblocknumber)
    * [getBlockTransactionCountByHash](#getblocktransactioncountbyhash)
    * [getBlockTransactionCountByNumber](#getblocktransactioncountbynumber)
    * [getBlockByHash](#getblockbyhash)
    * [getBlockByNumber](#getblockbynumber)
    * [getConstant](#getconstant)
    * [setConstant](#setconstant)
    * [resetConstant](#resetconstant)
    * [setLogLevel](#setloglevel)

## NimiqClient

RPC Client to communicate with a Nimiq Node.



* Full name: \Lunanimous\Rpc\NimiqClient
* Parent class: 


### getPeerCount

Returns number of peers currently connected to the client.

```php
NimiqClient::getPeerCount(  ): integer
```





**Return Value:**

number of connected peers



---

### getSyncingState

Returns an object with data about the sync status or false.

```php
NimiqClient::getSyncingState(  ): boolean|\Lunanimous\Rpc\Models\SyncingStatus
```





**Return Value:**

object with sync status data or false, when not syncing



---

### getConsensusState

Returns information on the current consensus state.

```php
NimiqClient::getConsensusState(  ): string
```





**Return Value:**

string describing the consensus state. ConsensusState::Established is the value for a good state, other values indicate bad state.



---

### getPeerList

Returns list of peers known to the client.

```php
NimiqClient::getPeerList(  ): array&lt;mixed,\Lunanimous\Rpc\Models\Peer&gt;
```





**Return Value:**

list of peers



---

### getPeer

Returns the state of the peer.

```php
NimiqClient::getPeer( string $peer ): \Lunanimous\Rpc\Models\Peer
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$peer` | **string** | address of the peer |


**Return Value:**

current state of the peer



---

### setPeerState

Sets the state of the peer.

```php
NimiqClient::setPeerState( string $peer, string $command ): \Lunanimous\Rpc\Models\Peer
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$peer` | **string** | address of the peer |
| `$command` | **string** | command to perform (one of PeerStateCommand::Connect, PeerStateCommand::Disconnect, PeerStateCommand::Ban, PeerStateCommand::Unban) |


**Return Value:**

new state of the peer



---

### sendRawTransaction

Sends a signed message call transaction or a contract creation, if the data field contains code.

```php
NimiqClient::sendRawTransaction( string $txHex ): string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$txHex` | **string** | hex-encoded signed transaction |


**Return Value:**

hex-encoded transaction hash



---

### createRawTransaction

Creates and signs a transaction without sending it. The transaction can then be send via sendRawTransaction
without accidentally replaying it.

```php
NimiqClient::createRawTransaction( \Lunanimous\Rpc\Models\OutgoingTransaction $tx ): string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tx` | **\Lunanimous\Rpc\Models\OutgoingTransaction** | transaction object |


**Return Value:**

hex-encoded transaction



---

### sendTransaction

Creates new message call transaction or a contract creation, if the data field contains code.

```php
NimiqClient::sendTransaction( \Lunanimous\Rpc\Models\OutgoingTransaction $tx ): string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tx` | **\Lunanimous\Rpc\Models\OutgoingTransaction** | transaction object |


**Return Value:**

hex-encoded transaction hash



---

### getRawTransactionInfo

Deserializes hex-encoded transaction and returns a transaction object.

```php
NimiqClient::getRawTransactionInfo( string $txHex ): \Lunanimous\Rpc\Models\Transaction
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$txHex` | **string** | hex-encoded transaction |


**Return Value:**

transaction object



---

### getTransactionByBlockHashAndIndex

Returns information about a transaction by block hash and transaction index position.

```php
NimiqClient::getTransactionByBlockHashAndIndex( string $blockHash, integer $txIndex ): null|\Lunanimous\Rpc\Models\Transaction
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$blockHash` | **string** | hash of the block containing the transaction |
| `$txIndex` | **integer** | index of the transaction in the block |


**Return Value:**

transaction object, or null when no transaction was found



---

### getTransactionByBlockNumberAndIndex

Returns information about a transaction by block number and transaction index position.

```php
NimiqClient::getTransactionByBlockNumberAndIndex( integer $blockNumber, integer $txIndex ): null|\Lunanimous\Rpc\Models\Transaction
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$blockNumber` | **integer** | height of the block containing the transaction |
| `$txIndex` | **integer** | index of the transaction in the block |


**Return Value:**

transaction object, or null when no transaction was found



---

### getTransactionByHash

Returns the information about a transaction requested by transaction hash.

```php
NimiqClient::getTransactionByHash( string $hash ): null|\Lunanimous\Rpc\Models\Transaction
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$hash` | **string** | hash of the transaction |


**Return Value:**

transaction object, or null when no transaction was found



---

### getTransactionReceipt

Returns the receipt of a transaction by transaction hash. The receipt is not available for pending transactions.

```php
NimiqClient::getTransactionReceipt( string $hash ): \Lunanimous\Rpc\Models\TransactionReceipt
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$hash` | **string** | hash of the transaction |


**Return Value:**

transaction receipt



---

### getTransactionsByAddress

Returns the latest transactions successfully performed by or for an address. This information might change
when blocks are rewinded on the local state due to forks.

```php
NimiqClient::getTransactionsByAddress( string $address, integer $limit = 1000 ): array&lt;mixed,\Lunanimous\Rpc\Models\Transaction&gt;
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$address` | **string** | account address |
| `$limit` | **integer** | (optional, default 1000) number of transactions to return |


**Return Value:**

array of transactions linked to the requested address



---

### getMempoolContent

Returns transactions that are currently in the mempool.

```php
NimiqClient::getMempoolContent( boolean $includeTransactions = false ): array&lt;mixed,string&gt;|array&lt;mixed,\Lunanimous\Rpc\Models\Transaction&gt;
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$includeTransactions` | **boolean** | if true includes full transactions, if false includes only transaction hashes |


**Return Value:**

array of transactions (either represented by the transaction hash or a transaction object)



---

### getMempool

Returns information on the current mempool situation. This will provide an overview of the number of
transactions sorted into buckets based on their fee per byte (in smallest unit).

```php
NimiqClient::getMempool(  ): \Lunanimous\Rpc\Models\Mempool
```





**Return Value:**

mempool information



---

### getMinFeePerByte

Returns the current minimum fee per byte.

```php
NimiqClient::getMinFeePerByte(  ): integer
```





**Return Value:**

current minimum fee per byte



---

### setMinFeePerByte

Sets the minimum fee per byte.

```php
NimiqClient::setMinFeePerByte( integer $minFee ): integer
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$minFee` | **integer** | minimum fee per byte |


**Return Value:**

new minimum fee per byte



---

### getMiningState

Returns true if client is actively mining new blocks.

```php
NimiqClient::getMiningState(  ): boolean
```





**Return Value:**

true if the client is mining, otherwise false



---

### setMiningState

Enables or disables the miner.

```php
NimiqClient::setMiningState( boolean $enabled ): boolean
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$enabled` | **boolean** | true to start the miner, false to stop |


**Return Value:**

true if the client is mining, otherwise false



---

### getHashrate

Returns the number of hashes per second that the node is mining with.

```php
NimiqClient::getHashrate(  ): float
```





**Return Value:**

number of hashes per second



---

### getMinerThreads

Returns the number of CPU threads the miner is using.

```php
NimiqClient::getMinerThreads(  ): integer
```





**Return Value:**

current number of miner threads



---

### setMinerThreads

Sets the number of CPU threads the miner can use.

```php
NimiqClient::setMinerThreads( integer $threads ): integer
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$threads` | **integer** | number of threads to allocate |


**Return Value:**

new number of miner threads



---

### getMinerAddress

Returns the miner address.

```php
NimiqClient::getMinerAddress(  ): string
```





**Return Value:**

miner address



---

### getPool

Returns the current pool.

```php
NimiqClient::getPool(  ): null|string
```





**Return Value:**

current pool, or null if not set



---

### setPool

Sets the mining pool.

```php
NimiqClient::setPool( boolean|string $pool ): null|string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$pool` | **boolean&#124;string** | mining pool string (url:port) or boolean to enable/disable pool mining |


**Return Value:**

new mining pool, or null if not enabled



---

### getPoolConnectionState

Returns the connection state to mining pool.

```php
NimiqClient::getPoolConnectionState(  ): integer
```





**Return Value:**

mining pool connection state (0: connected, 1: connecting, 2: closed)



---

### getPoolConfirmedBalance

Returns the confirmed mining pool balance.

```php
NimiqClient::getPoolConfirmedBalance(  ): float
```





**Return Value:**

confirmed mining pool balance (in smallest unit)



---

### getWork

Returns instructions to mine the next block. This will consider pool instructions when connected to a pool.

```php
NimiqClient::getWork( string $address = null, string $extraDataHex = null ): array
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$address` | **string** | address to use as a miner for this block. this overrides the address provided during startup or from the pool. |
| `$extraDataHex` | **string** | hex-encoded value for the extra data field. this overrides the address provided during startup or from the pool. |


**Return Value:**

mining work instructions



---

### getBlockTemplate

Returns a template to build the next block for mining. This will consider pool instructions when connected
to a pool.

```php
NimiqClient::getBlockTemplate( string $address = null, string $extraDataHex = null ): array
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$address` | **string** | address to use as a miner for this block. this overrides the address provided during startup or from the pool. |
| `$extraDataHex` | **string** | hex-encoded value for the extra data field. this overrides the address provided during startup or from the pool. |


**Return Value:**

mining block template



---

### submitBlock

Submits a block to the node. When the block is valid, the node will forward it to other nodes in the network.

```php
NimiqClient::submitBlock( string $blockHex )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$blockHex` | **string** | hex-encoded full block (including header, interlink and body). when submitting work from getWork, remember to include the suffix. |




---

### getAccounts

Returns a list of addresses owned by client.

```php
NimiqClient::getAccounts(  ): array&lt;mixed,\Lunanimous\Rpc\Models\Account&gt;
```





**Return Value:**

array of accounts owned by the client



---

### createAccount

Creates a new account and stores its private key in the client store.

```php
NimiqClient::createAccount(  ): \Lunanimous\Rpc\Models\Wallet
```





**Return Value:**

information on the wallet that was created using the command



---

### getBalance

Returns the balance of the account of given address.

```php
NimiqClient::getBalance( string $address ): float
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$address` | **string** | address to check for balance |


**Return Value:**

the current balance at the specified address (in smallest unit)



---

### getAccount

Returns details for the account of given address.

```php
NimiqClient::getAccount( string $address ): \Lunanimous\Rpc\Models\Account
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$address` | **string** | address for which to get account details |


**Return Value:**

details about the account. returns the default empty basic account for non-existing accounts.



---

### getBlockNumber

Returns the height of most recent block.

```php
NimiqClient::getBlockNumber(  ): integer
```





**Return Value:**

current block height the client is on



---

### getBlockTransactionCountByHash

Returns the number of transactions in a block from a block matching the given block hash.

```php
NimiqClient::getBlockTransactionCountByHash( string $blockHash ): null|integer
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$blockHash` | **string** | hash of the block |


**Return Value:**

number of transactions in the block found, or null when no block was found



---

### getBlockTransactionCountByNumber

Returns the number of transactions in a block matching the given block number.

```php
NimiqClient::getBlockTransactionCountByNumber( integer $blockNumber ): null|integer
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$blockNumber` | **integer** | height of the block |


**Return Value:**

number of transactions in the block found, or null when no block was found



---

### getBlockByHash

Returns information about a block by hash.

```php
NimiqClient::getBlockByHash( string $blockHash, boolean $includeTransactions = false ): null|\Lunanimous\Rpc\Models\Block
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$blockHash` | **string** | hash of the block to gather information on |
| `$includeTransactions` | **boolean** | if true includes full transactions, if false (default) includes only transaction hashes |


**Return Value:**

block object, or null when no block was found



---

### getBlockByNumber

Returns information about a block by block number.

```php
NimiqClient::getBlockByNumber( integer $blockNumber, boolean $includeTransactions = false ): null|\Lunanimous\Rpc\Models\Block
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$blockNumber` | **integer** | height of the block to gather information on |
| `$includeTransactions` | **boolean** | if true includes full transactions, if false (default) includes only transaction hashes |


**Return Value:**

block object, or null when no block was found



---

### getConstant

Returns the value of a constant.

```php
NimiqClient::getConstant( string $constant ): integer
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$constant` | **string** | name of the constant |


**Return Value:**

current value of the constant



---

### setConstant

Sets the value of a constants.

```php
NimiqClient::setConstant( string $constant, integer $value ): integer
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$constant` | **string** | name of the constant |
| `$value` | **integer** | value to set |


**Return Value:**

new value of the constant



---

### resetConstant

Resets the constant to default value.

```php
NimiqClient::resetConstant( string $constant ): integer
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$constant` | **string** | name of the constant |


**Return Value:**

new value of the constant



---

### setLogLevel

Sets the log level of the node.

```php
NimiqClient::setLogLevel( string $tag, string $level ): boolean
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tag` | **string** | if '*' the log level is set globally, otherwise the log level is applied only on this tag |
| `$level` | **string** | minimum log level to display (trace, verbose, debug, info, warn, error, assert) |


**Return Value:**

true if set successfully, otherwise false



---



--------
> This document was automatically generated from source code comments on 2020-03-21 using [phpDocumentor](http://www.phpdoc.org/) and [cvuorinen/phpdoc-markdown-public](https://github.com/cvuorinen/phpdoc-markdown-public)
