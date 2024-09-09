<?php

namespace OpenSoutheners\ExtendedLaravel\Support;

use Closure;

use function OpenSoutheners\ExtendedPhp\Numbers\short_number;

/**
 * This is NOT supposed to be used alone, use the main from Laravel framework
 * instead as is the one this is extending with new methods.
 *
 * @mixin \Illuminate\Support\Number
 */
class Number
{
    public function toShort(): Closure
    {
        /**
         * Get shorter version of a big number if possible.
         */
        return fn (int|float $number): string => short_number($number);
    }
}
