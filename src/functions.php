<?php

namespace Lunanimous\Rpc;

if (!function_exists('toNim')) {
    /**
     * Converts from luna to nim.
     *
     * @param int $luna the value in luna (smallest unit)
     *
     * @return float the value in nim
     */
    function toNim($luna)
    {
        return $luna / 100000;
    }
}

if (!function_exists('toLuna')) {
    /**
     * Converts from nim to luna.
     *
     * @param float $nim the value in nim
     *
     * @return int the value in luna (smallest unit)
     */
    function toLuna($nim)
    {
        return $nim * 100000;
    }
}
