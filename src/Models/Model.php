<?php

namespace Lunanimous\Rpc\Models;

/**
 * @internal
 */
abstract class Model
{
    /**
     * Create a new model instance.
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
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
