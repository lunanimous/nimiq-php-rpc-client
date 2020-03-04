<?php

namespace Lunanimous\Rpc\Constants;

class AccountType
{
    /**
     * @var int basic account
     */
    const Basic = 0;

    /**
     * @var int vesting account
     */
    const Vesting = 1;

    /**
     * @var int htlc account
     */
    const Htlc = 2;
}
