<?php

namespace Lunanimous\Rpc\Models;

class SyncingStatus extends Model
{
    /**
     * @var int block height when the node was started
     */
    public $startingBlock;

    /**
     * @var int current block height of the node
     */
    public $currentBlock;

    /**
     * @var int block height of the network
     */
    public $highestBlock;
}
