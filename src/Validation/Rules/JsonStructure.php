<?php

namespace OpenSoutheners\ExtendedLaravel\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use function OpenSoutheners\ExtendedPhp\Strings\is_json_structure;

class JsonStructure implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_json_structure($value)) {
            $fail('The :attribute must be a valid JSON structure string.');
        }
    }
}
