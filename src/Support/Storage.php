<?php

namespace OpenSoutheners\ExtendedLaravel\Support;

use Closure;
use OpenSoutheners\ByteUnitConverter\ByteUnit;
use OpenSoutheners\ByteUnitConverter\ByteUnitConverter;
use OpenSoutheners\ByteUnitConverter\MetricSystem;

/**
 * This is NOT supposed to be used alone, use the main from Laravel framework
 * instead as is the one this is extending with new methods.
 *
 * @mixin \Illuminate\Support\Facades\Storage
 */
class Storage
{
    public function humanSize(): Closure
    {
        /**
         * Get human readable file size at given path.
         */
        return function (string $path, ?MetricSystem $metric = null, ?ByteUnit $stoppingAt = null): ByteUnitConverter {
            return ByteUnitConverter::new($this->size($path))
                ->nearestUnit($metric, $stoppingAt);
        };
    }
}
