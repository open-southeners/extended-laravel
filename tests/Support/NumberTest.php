<?php

namespace OpenSoutheners\ExtendedLaravel\Tests\Support;

use Illuminate\Support\Number;
use OpenSoutheners\ByteUnitConverter\ByteUnitConverter;
use OpenSoutheners\ExtendedLaravel\Tests\TestCase;

class NumberTest extends TestCase
{
    public function test_number_to_short()
    {
        $this->assertEquals('1K', Number::toShort(1000));

        // FIXME: Round
        $this->assertEquals('1K', Number::toShort(1800));
        $this->assertEquals('1M', Number::toShort(1800000));
    }

    public function test_number_to_byte_unit()
    {
        $this->assertInstanceOf(ByteUnitConverter::class, Number::toByteUnit(1000));
        $this->assertEquals('1 KB', (string) Number::toByteUnit(1000)->nearestUnit());
    }
}
