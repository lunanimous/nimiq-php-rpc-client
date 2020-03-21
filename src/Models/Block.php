<?php

namespace Lunanimous\Rpc\Models;

class Block extends Model
{
    /**
     * @var int height of the block
     */
    public $number;

    /**
     * @var string hex-encoded 32-byte hash of the block
     */
    public $hash;

    /**
     * @var string hex-encoded 32-byte Proof-of-Work hash of the block
     */
    public $pow;

    /**
     * @var string hex-encoded 32-byte hash of the predecessor block
     */
    public $parentHash;

    /**
     * @var int nonce of the block used to fulfill the Proof-of-Work
     */
    public $nonce;

    /**
     * @var string hex-encoded 32-byte hash of the block body Merkle root
     */
    public $bodyHash;

    /**
     * @var string hex-encoded 32-byte hash of the accounts tree root
     */
    public $accountsHash;

    /**
     * @var string block difficulty, encoded as decimal number in string
     */
    public $difficulty;

    /**
     * @var int UNIX timestamp of the block
     */
    public $timestamp;

    /**
     * @var int number of confirmations
     */
    public $confirmations;

    /**
     * @var string hex-encoded 20 byte address of the miner of the block
     */
    public $miner;

    /**
     * @var string user friendly address (NQ-address) of the miner of the block
     */
    public $minerAddress;

    /**
     * @var string hex-encoded value of the extra data field, maximum of 255 bytes
     */
    public $extraData;

    /**
     * @var int block size in byte
     */
    public $size;

    /**
     * @var string[]|Transaction[] array of transactions, either represented by the transaction hash or a
     *                             Transaction object
     */
    public $transactions;

    public function __construct($attributes = [])
    {
        // create a copy to prevent side effects
        $normalized = unserialize(serialize($attributes));

        // if full transactions are included, they should be converted to Transaction objects
        $hasFullTransactions = isset($attributes['transactions'])
            && count($attributes['transactions']) > 0
            && is_array($attributes['transactions'][0]);

        if ($hasFullTransactions) {
            $normalized['transactions'] = array_map(function ($rawTransaction) {
                return new Transaction($rawTransaction);
            }, $normalized['transactions']);
        }

        parent::__construct($normalized);
    }
}
