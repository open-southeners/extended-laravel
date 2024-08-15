<?php

namespace OpenSoutheners\ExtendedLaravel\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use OpenSoutheners\ByteUnitConverter\ByteUnitConverter;

/**
 * @implements \Illuminate\Contracts\Database\Eloquent\CastsAttributes<\OpenSoutheners\ByteUnitConverter\ByteUnitConverter, int>
 */
class ByteUnit implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return (string) ByteUnitConverter::new($value)->nearestUnit();
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }
}
