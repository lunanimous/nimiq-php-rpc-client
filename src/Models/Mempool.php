<?php

namespace Lunanimous\Rpc\Models;

class Mempool extends Model
{
    /**
     * @var int total number of pending transactions in mempool
     */
    public $total;

    /**
     * @var int[] array containing a subset of fee per byte buckets from [10000, 5000,
     *            2000, 1000, 500, 200, 100, 50, 20, 10, 5, 2, 1, 0] that currently
     *            have more than one transaction
     */
    public $buckets;

    /**
     * @var array array where key is the fee per byte and value the number of transactions
     */
    public $transactionsPerBucket;

    public function __construct($attributes = [])
    {
        $normalized = [];
        $normalized['total'] = $attributes['total'];
        $normalized['buckets'] = $attributes['buckets'];
        $normalized['transactionsPerBucket'] = [];

        // create a map for bucket and associated number of transactions
        foreach ($normalized['buckets'] as $bucket) {
            $normalized['transactionsPerBucket'][$bucket] = $attributes[$bucket];
        }

        parent::__construct($normalized);
    }
}
