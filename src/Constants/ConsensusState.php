<?php

namespace Lunanimous\Rpc\Constants;

class ConsensusState
{
    /**
     * @var string client is connecting to the network
     */
    const Connecting = 'connecting';

    /**
     * @var string client is syncing data from peers to reach consensus
     */
    const Syncing = 'syncing';

    /**
     * @var string client reached consensus with its peers
     */
    const Established = 'established';
}
