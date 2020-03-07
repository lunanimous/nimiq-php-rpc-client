<?php

namespace Lunanimous\Rpc\Constants;

class PeerStateCommand
{
    /**
     * @var string command to connect the peer
     */
    const Connect = 'connect';

    /**
     * @var string command disconnect the peer
     */
    const Disconnect = 'disconnect';

    /**
     * @var string command to ban the peer
     */
    const Ban = 'ban';

    /**
     * @var string command unban the peer
     */
    const Unban = 'unban';
}
