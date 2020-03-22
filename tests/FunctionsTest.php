<?php

use function Lunanimous\Rpc\toLuna;
use function Lunanimous\Rpc\toNim;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \Lunanimous\Rpc\toLuna
 * @covers \Lunanimous\Rpc\toNim
 */
class FunctionsTest extends TestCase
{
    public function testToNim()
    {
        $nim = toNim(1200000);

        $this->assertEquals(12, $nim);
    }

    public function testToNimWithSmallAmount()
    {
        $nim = toNim(12000);

        $this->assertEquals(0.12, $nim);
    }

    public function testToNimWithZero()
    {
        $nim = toNim(0);

        $this->assertEquals(0, $nim);
    }

    public function testToLuna()
    {
        $luna = toLuna(12);

        $this->assertEquals(1200000, $luna);
    }

    public function testToLunaWithSmallAmount()
    {
        $luna = toLuna(0.12);

        $this->assertEquals(12000, $luna);
    }

    public function testToLunaWithZero()
    {
        $luna = toLuna(0);

        $this->assertEquals(0, $luna);
    }
}
