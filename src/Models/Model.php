<?php

namespace Lunanimous\Rpc\Models;

use InvalidArgumentException;

abstract class Model
{
    protected $required = [];

    /**
     * Create a new model instance.
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        $diff = array_diff_key($this->required, $attributes);

        if (count($diff) > 0) {
            throw new InvalidArgumentException('Missing attributes');
        }

        $this->fill($attributes);
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function fill($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists(static::class, $key)) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }
}
