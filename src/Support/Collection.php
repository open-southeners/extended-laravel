<?php

namespace OpenSoutheners\ExtendedLaravel\Support;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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

    public function templateJoin()
    {
        /**
         * Join items of the collection using a template substitution.
         */
        return function (string $template, string $glue, string $finalGlue = ''): string {
            $items = $this->items;

            if (Arr::isAssoc($items)) {
                $items = [$items];
            }

            $items = Arr::map($items, function ($item) use ($template): string {
                $sanitizedItem = array_filter(
                    $item instanceof Arrayable ? $item->toArray() : $item,
                    fn ($value): bool => ! is_array($value) && ! is_object($value) && ! is_callable($value)
                );

                $replacements = Arr::map(array_keys($sanitizedItem), fn (string $key): string => ":{$key}");

                return Str::replace($replacements, array_values($sanitizedItem), $template);
            });

            if ($finalGlue === '') {
                return implode($glue, $items);
            }

            return Arr::join($items, $glue, $finalGlue);
        };
    }
}
