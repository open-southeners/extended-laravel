# Getting started

## Why this package?

Laravel and PHP has some lacks, which is fine (they are not designed to be perfect fit for every possible case). Therefore this package serve to fill that gap.

The package is just a bunch of functions that we most often reuse in plenty of our own open source packages as well as our work.

## Key features

* All make commands available through the framework opens the generated file (`APP_IDE` environment variable must be configured).
* Helpers functions from our other package [**extended-php**](https://github.com/open-southeners/extended-php) are [available through this one](function-helpers.md) in multiple ways.
* Custom Eloquent attributes casts and validation rules
* Added useful commands like `horizon:flush`, `make:builder`, `queue:batches`, `cache:clearLocks`, `vendor:check`

## Installation

Grab this package using composer:

```bash
composer require open-southeners/laravel-helpers
```
