<?php

namespace OpenSoutheners\ExtendedLaravel\Support;

use Closure;

use function OpenSoutheners\ExtendedPhp\Utils\build_http_query;

/**
 * This is NOT supposed to be used alone, use the main from Laravel framework
 * instead as is the one this is extending with new methods.
 *
 * @mixin \Illuminate\Support\Arr
 */
class Arr
{
    public function exceptValues(): Closure
    {
        /**
         * Get all of the given array except for a specified array of values.
         *
         * @param  array|string  $values
         */
        return fn (array $array, $values): array => array_flip(static::except(array_flip($array), $values));
    }

    public function onlyValues(): Closure
    {
        /**
         * Get a subset of the items from the given array.
         *
         * @param  array|string  $values
         */
        return fn (array $array, $values): array => array_flip(static::only(array_flip($array), $values));
    }

    public function query(): Closure
    {
        /**
         * Convert the array into a query string.
         */
        return fn (array $array): string => build_http_query($array);
    }
}
