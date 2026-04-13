<?php

namespace OpenSoutheners\ExtendedLaravel\Config;

use Closure;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * This is NOT supposed to be used alone, use the main from Laravel framework
 * instead as is the one this is extending with new methods.
 *
 * @mixin \Illuminate\Contracts\Config\Repository
 */
class Repository
{
    public function stringish(): Closure
    {
        /**
         * Get the specified string or null configuration value.
         *
         * @param  string  $key
         * @param  (\Closure():(string|null))|string|null  $default
         * @return null|string
         */
        return function (string $key, $default = null): ?string {
            $value = $this->get($key, $default);

            if ($value === null) {
                return null;
            }

            if (! is_string($value)) {
                throw new InvalidArgumentException(
                    sprintf('Configuration value for key [%s] must be a string, %s given.', $key, gettype($value))
                );
            }

            return $value;
        };
    }

    public function integerish(): Closure
    {
        /**
         * Get the specified integer or null configuration value.
         *
         * @param  string  $key
         * @param  (\Closure():(int|null))|int|null  $default
         * @return null|int
         */
        return function (string $key, $default = null): ?int {
            $value = $this->get($key, $default);

            if ($value === null) {
                return null;
            }

            if (! is_int($value)) {
                throw new InvalidArgumentException(
                    sprintf('Configuration value for key [%s] must be an integer, %s given.', $key, gettype($value))
                );
            }

            return $value;
        };
    }

    public function floatish(): Closure
    {
        /**
         * Get the specified float or null configuration value.
         *
         * @param  string  $key
         * @param  (\Closure():(float|null))|float|null  $default
         * @return null|float
         */
        return function (string $key, $default = null): ?float {
            $value = $this->get($key, $default);

            if ($value === null) {
                return null;
            }

            if (! is_float($value)) {
                throw new InvalidArgumentException(
                    sprintf('Configuration value for key [%s] must be a float, %s given.', $key, gettype($value))
                );
            }

            return $value;
        };
    }

    public function booleanish(): Closure
    {
        /**
         * Get the specified boolean or null configuration value.
         *
         * @param  string  $key
         * @param  (\Closure():(bool|null))|bool|null  $default
         * @return null|bool
         */
        return function (string $key, $default = null): ?bool {
            $value = $this->get($key, $default);

            if ($value === null) {
                return null;
            }

            if (! is_bool($value)) {
                throw new InvalidArgumentException(
                    sprintf('Configuration value for key [%s] must be a boolean, %s given.', $key, gettype($value))
                );
            }

            return $value;
        };
    }

    public function arrayish(): Closure
    {
        /**
         * Get the specified array or null configuration value.
         *
         * @param  string  $key
         * @param  (\Closure():(array<array-key, mixed>|null))|array<array-key, mixed>|null  $default
         * @return null|array<array-key, mixed>
         */
        return function (string $key, $default = null): ?array {
            $value = $this->get($key, $default);

            if ($value === null) {
                return null;
            }

            if (! is_array($value)) {
                throw new InvalidArgumentException(
                    sprintf('Configuration value for key [%s] must be an array, %s given.', $key, gettype($value))
                );
            }

            return $value;
        };
    }

    public function collectionish(): Closure
    {
        /**
         * Get the specified array configuration value as a collection.
         *
        * @param  string  $key
        * @param  (\Closure():(array<array-key, mixed>|null))|array<array-key, mixed>|null  $default
        * @return Collection<array-key, mixed>
         */
        return function (string $key, $default = null): Collection {
            $value = $this->get($key, $default);

            return new Collection(is_array($value) ? $value : []);
        };
    }

    public function numeric(): Closure
    {
        /**
         * Get the specified numeric or null configuration value.
         *
         * @param  string  $key
         * @param  (\Closure():(int|float|string|null))|int|float|string|null  $default
         * @return null|int|float|string
         */
        return function (string $key, $default = null): null|int|float|string {
            $value = $this->get($key, $default);

            if ($value === null) {
                return null;
            }

            if (! is_numeric($value)) {
                throw new InvalidArgumentException(
                    sprintf('Configuration value for key [%s] must be a numeric value, %s given.', $key, gettype($value))
                );
            }

            return $value;
        };
    }
}
