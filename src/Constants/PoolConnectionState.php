<?php

namespace Lunanimous\Rpc\Constants;

class PoolConnectionState
{
    /**
     * @var string client is connected to the pool
     */
    const Connected = 0;

    /**
     * @var string client is connecting to the pool
     */
    const Connecting = 1;

    /**
     * @var string client's connection to pool is closed
     */
    const Closed = 2;
}
