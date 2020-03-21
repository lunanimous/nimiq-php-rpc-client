<?php

namespace Lunanimous\Rpc\Constants;

class AddressState
{
    /**
     * @var int new connection
     */
    const New = 1;

    /**
     * @var int connection is established
     */
    const Established = 2;

    /**
     * @var int connection tried
     */
    const Tried = 3;

    /**
     * @var int connection failed
     */
    const Failed = 4;

    /**
     * @var int address is banned
     */
    const Banned = 5;
}
