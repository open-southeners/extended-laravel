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

            $headers = $this->flatMap(fn ($item) => array_keys($item instanceof Arrayable ? $item->toArray() : (array) $item))->unique();

            $csvContent .= implode(',', $headers->toArray())."\n";

            $csvContent .= implode("\n", $this->map(function ($result) use ($headers): string {
                $resultAsArray = $result instanceof Arrayable ? $result->toArray() : (array) $result;

                return $headers->map(fn (string $header): string => $resultAsArray[$header] ?? '')->join(',');
            })->toArray());

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
