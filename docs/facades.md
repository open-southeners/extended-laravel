---
description: This package extends Laravel's framework facades with new useful methods.
---

# Facades

### Arr

#### exceptValues

Filter array shorthand by just excluding the values passed as second argument.

```php
Arr::exceptValues([
    "hello" => "world",
    "foo" => "bar"
], "bar");

// ["hello" => "world"]
```

#### onlyValues

Filter array shorthand by just including the values passed as second argument.

```php
Arr::onlyValues([
    "hello" => "world",
    "foo" => "bar"
], "world");

// ["hello" => "world"]
```

#### query

Write a URL query string from an array.

```php
Arr::query([
    "q" => "this",
    "param2" => "value"
], "world");

// "?q=this&param2=value"
```

### Collection

#### toCsv

Converts collection of items to CSV with its headers.

```php
collect([
    [
        "name" => "Ruben Robles",
        "role" => "Admin"
    ],
    [
        "name" => "Taylor Otwell",
        "role" => "Admin"
    ]
])->toCsv(); // "name,role\nRuben Robles,Admin\nTaylor Otwell,Admin"
```

#### templateJoin

Join items of the collection using a template substitution.

```php
collect([
    [
        "name" => "Ruben Robles",
        "email" => "ruben.robles@example.com"
    ],
    [
        "name" => "Taylor Otwell",
        "email" => "taylor.otwell@laravel.com"
    ]
])->templateJoin(":name (:email)"); // "Ruben Robles (ruben.robles@example.com), Taylor Otwell (taylor.otwell@laravel.com)"
```

### Number

#### toShort

Get shorter version of a big number if possible.

```php
Number::toShort(1000); // "1K"
Number::toShort(2200); // "2,2K"
```

#### toByteUnit

Get byte unit version of a number.

```php
Number::toByteUnit(1000)->toKB(); // "1.00 KB"
```

## Storage

#### humanSize

Get human readable file size at given path.

```php
use OpenSoutheners\ByteUnitConverter\MetricSystem;

Storage::humanSize("path/file.png"); // "124.00 KB"
Storage::humanSize("path/file.png", MetricSystem::KiB); // "121.09 KiB"
```

### Str

#### parseQuery

Parse a URL query string into an array.

```php
Str::parseQuery("?q=search&param1=value"); // ["q" => "search", "param1" => "value"]

Str::of("?q=search&param1=value")->parseQuery();
```

#### isJsonStructure

Check if string contains a valid JSON structure.

```php
Str::isJsonStructure('{"foo": "bar"}'); // true

Str::of('"bar"')->isJsonStructure(); // false
```

#### emailDomain

Get domain part from email address.

```php
Str::emailDomain("ruben@example.com"); // "example.com"

Str::of("taylor.otwell@laravel.com")->emailDomain(); // "laravel.com"
```

