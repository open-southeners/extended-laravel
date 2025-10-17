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
         * @template T
         * @param  array<T>  $values
         * @param  array<T>|T  $values
         * @return array<T>
         */
        return fn (array $array, $values): array => array_filter($array, fn ($value) => !in_array($value, static::wrap($values)));
    }

    public function onlyValues(): Closure
    {
        /**
         * Get a subset of the items from the given array.
         *
         * @template T
         * @param  array<T>  $values
         * @param  array<T>|T  $values
         * @return array<T>
         */
        return fn (array $array, $values): array => array_filter($array, fn ($value) => in_array($value, static::wrap($values)));
    }

    public function queryString(): Closure
    {
        /**
         * Convert the array into a query string.
         */
        return fn (array $array): string => build_http_query($array);
    }
}
