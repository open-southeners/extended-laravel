<?php

namespace OpenSoutheners\ExtendedLaravel\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ReflectionClass;
use Throwable;

use function OpenSoutheners\ExtendedPhp\Classes\call;
use function OpenSoutheners\ExtendedPhp\Classes\class_from;

/**
 * Get model from class or string (by name).
 *
 * @return \Illuminate\Database\Eloquent\Model|class-string<\Illuminate\Database\Eloquent\Model>|null
 */
function model_from(string $value, bool $asClass = true, string $namespace = 'App\Models\\')
{
    $value = implode(
        array_map(fn ($word) => ucfirst($word), explode(' ', str_replace(['-', '_'], ' ', $value)))
    );

    $modelClass = $namespace.class_basename($value);

    $modelClass = \class_exists(class_from($modelClass)) ? $modelClass : null;

    if (! $asClass && $modelClass !== null) {
        return new $modelClass;
    }

    return $modelClass;
}

/**
 * Check if object or class string is a valid Laravel model.
 *
 * @param  \Illuminate\Database\Eloquent\Model|object|string  $class
 */
function is_model(mixed $class): bool
{
    if (! $class) {
        return false;
    }

    try {
        $classReflection = new ReflectionClass($class);
    } catch (Throwable $e) {
        return false;
    }

    return $classReflection->isInstantiable()
        && $classReflection->isSubclassOf('Illuminate\Database\Eloquent\Model');
}

/**
 * Get model instance from a mix-typed parameter.
 *
 * @template T of \Illuminate\Database\Eloquent\Model
 *
 * @param  T|int|null  $key
 * @param  class-string<T>|string  $class
 * @param  array<string>  $columns
 * @param  array<string>  $with
 * @return T|null
 */
function instance_from(mixed $key, string $class, array $columns = ['*'], array $with = [], bool $enforce = false)
{
    if (! \class_exists($class) || ! is_model($class) || (\is_object($key) && ! is_model($key))) {
        throw (new ModelNotFoundException)->setModel($class);
    }

    if (is_model($key) && $enforce) {
        /** @var T $key */
        return $key->loadMissing($with);
    }

    return query_from($class)->with($with)->whereKey($key)->first($columns);
}

/**
 * Get key (id) from a mix-typed parameter.
 *
 * @param  \Illuminate\Database\Eloquent\Model|string|int  $model
 */
function key_from($model): mixed
{
    if (is_numeric($model)) {
        return (int) $model;
    }

    if (is_object($model) && method_exists($model, 'getKey')) {
        return $model->getKey();
    }

    if (is_string($model)) {
        return $model;
    }

    return null;
}

/**
 * Get a new query instance from model or class string.
 *
 * @param  \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder|class-string|string|object  $model
 * @return \Illuminate\Database\Eloquent\Builder|false
 */
function query_from($model)
{
    if (\class_exists(class_from($model)) && \method_exists($model, 'newQuery')) {
        return call($model, 'newQuery');
    }

    if ($model instanceof Builder) {
        return call($model, 'newModelInstance.newQuery');
    }

    return false;
}
