<?php

namespace OpenSoutheners\ExtendedLaravel\Validation;

use Closure;

/**
 * This is NOT supposed to be used alone, use the main from Laravel framework
 * instead as is the one this is extending with new methods.
 *
 * @mixin \Illuminate\Validation\Rule
 */
class Rule
{
    public function jsonStructure(): Closure
    {
        /**
         * Checks if value is a valid JSON structure string.
         */
        return fn (): Rules\JsonStructure => new Rules\JsonStructure;
    }
}
