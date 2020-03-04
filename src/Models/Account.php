<?php

namespace Lunanimous\Rpc\Models;

class Account extends Model
{
    /**
     * @var string hex-encoded 20 byte address
     */
    public $id;

    /**
     * @var string user friendly address (NQ-address)
     */
    public $address;

    /**
     * @var int balance of the account (in smallest unit)
     */
    public $balance;

    /**
     * @var int account type associated with the account (AccountType::Basic, AccountType::Vesting, AccountType::Htlc)
     */
    public $type;
}
