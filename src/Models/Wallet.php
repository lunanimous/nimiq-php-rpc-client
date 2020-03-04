<?php

namespace Lunanimous\Rpc\Models;

class Wallet extends Model
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
     * @var string hex-encoded 32 byte Ed25519 public key
     */
    public $publicKey;

    /**
     * @var string hex-encoded 32 byte Ed25519 private key
     */
    public $privateKey;
}
