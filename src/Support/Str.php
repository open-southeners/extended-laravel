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
 * @mixin \Illuminate\Support\Str
 */
class Str
{
    public function parseQuery(): Closure
    {
        /**
         * Parse a url query string into an array.
         */
        return fn (?string $query = null): array => parse_http_query($query);
    }

    public function isJsonStructure(): Closure
    {
        /**
         * Check if string contains a valid JSON structure.
         */
        return fn (string $value): bool => is_json_structure($value);
    }

    public function emailDomain(): Closure
    {
        /**
         * Get domain part from email address.
         */
        return fn (string $email): string => get_email_domain($email);
    }
}
