<?php

namespace Lunanimous\Rpc\Models;

class Peer extends Model
{
    /**
     * @var string peer id
     */
    public $id;

    /**
     * @var string peer address
     */
    public $address;

    /**
     * @var int peer address state ()
     */
    public $addressState;

    /**
     * @var int peer connection state ()
     */
    public $connectionState;

    /**
     * @var int node version the peer is running
     */
    public $version;

    /**
     * @var int time offset with the peer (in miliseconds)
     */
    public $timeOffset;

    /**
     * @var string hash of the head block of the peer
     */
    public $headHash;

    /**
     * @var int latency to the peer
     */
    public $latency;

    /**
     * @var int
     */
    public $rx;

    /**
     * @var int
     */
    public $tx;
}
