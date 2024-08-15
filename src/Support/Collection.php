<?php

namespace OpenSoutheners\ExtendedLaravel\Support;

use Closure;

/**
 * This is NOT supposed to be used alone, use the main from Laravel framework
 * instead as is the one this is extending with new methods.
 *
 * @mixin \Illuminate\Support\Collection
 */
class Collection
{
    public function toCsv(): Closure
    {
        /**
         * Convert collection items to CSV.
         */
        return function (): string {
            $csvContent = '';

            $csvContent .= implode(',', array_keys((array) $this->first()))."\n";

            $csvContent .= implode("\n", $this->map(fn ($result) => implode(',', array_values((array) $result)))->toArray());

            return $csvContent;
        };
    }
}
