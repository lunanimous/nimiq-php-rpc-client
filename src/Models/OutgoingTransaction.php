<?php

namespace Lunanimous\Rpc\Models;

use Lunanimous\Rpc\Constants\AccountType;

class OutgoingTransaction extends Model
{
    /**
     * @var string address the transaction is sent from
     */
    public $from;

    /**
     * @var int account type of the given address
     */
    public $fromType = AccountType::Basic;

    /**
     * @var string address the transaction is directed to
     */
    public $to;

    /**
     * @var int account type of the given address
     */
    public $toType = AccountType::Basic;

    /**
     * @var int value (in smallest unit) sent with this transaction
     */
    public $value;

    /**
     * @var int fee (in smallest unit) for this transaction
     */
    public $fee;

    /**
     * @var null|string hex-encoded contract parameters or a message
     */
    public $data;

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'from' => $this->from,
            'fromType' => $this->fromType,
            'to' => $this->to,
            'toType' => $this->toType,
            'value' => $this->value,
            'fee' => $this->fee,
            'data' => $this->data,
        ];
    }
}
