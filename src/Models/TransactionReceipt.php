<?php

namespace Lunanimous\Rpc\Models;

class TransactionReceipt extends Model
{
    /**
     * @var string hex-encoded transaction hash
     */
    public $transactionHash;

    /**
     * @var int index of the transaction in the block
     */
    public $transactionIndex;

    /**
     * @var int height of the block containing the transaction
     */
    public $blockNumber;

    /**
     * @var string hex-encoded hash of the block containing the transaction
     */
    public $blockHash;

    /**
     * @var int UNIX timestamp of the block containing the transaction
     */
    public $timestamp;

    /**
     * @var int number of confirmations of the block containing the transaction
     */
    public $confirmations;
}
