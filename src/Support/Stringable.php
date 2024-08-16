<?php

namespace OpenSoutheners\ExtendedLaravel\Support;

use Closure;

use function OpenSoutheners\ExtendedPhp\Strings\get_email_domain;
use function OpenSoutheners\ExtendedPhp\Strings\is_json_structure;
use function OpenSoutheners\ExtendedPhp\Utils\parse_http_query;

/**
 * This is NOT supposed to be used alone, use the main from Laravel framework
 * instead as is the one this is extending with new methods.
 *
 * @mixin \Illuminate\Support\Stringable
 */
class Stringable
{
    public function parseQuery(): Closure
    {
        /**
         * Parse a url query string into an array.
         */
        return fn (): array => parse_http_query($this->value);
    }

    public function isJsonStructure(): Closure
    {
        /**
         * Check if string contains a valid JSON structure.
         */
        return fn (): bool => is_json_structure($this->value);
    }

    public function emailDomain(): Closure
    {
        /**
         * Get domain part from email address.
         */
        return function (): self {
            $this->value = get_email_domain($this->value);

            return $this;
        };
    }
}
