<?php

namespace OpenSoutheners\ExtendedLaravel\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use OpenSoutheners\ByteUnitConverter\ByteUnitConverter;

/**
 * @implements \Illuminate\Contracts\Database\Eloquent\CastsAttributes<string, string>
 */
class ByteUnit implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  int|string|null  $value
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return (string) ByteUnitConverter::new(!$value ? 0 : $value)->nearestUnit();
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return (string) $value;
    }
}
