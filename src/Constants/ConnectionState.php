<?php

namespace Lunanimous\Rpc\Constants;

class ConnectionState
{
    /**
     * @var int new connection
     */
    const New = 1;

    /**
     * @var int connecting to peer
     */
    const Connecting = 2;

    /**
     * @var int connected to peer
     */
    const Connected = 3;

    /**
     * @var int negotiating with peer
     */
    const Negotiating = 4;

    /**
     * @var int connection established
     */
    const Established = 5;

    /**
     * @var int connection closed
     */
    const Closed = 6;
}
