<?php

namespace OpenSoutheners\ExtendedLaravel;

use Illuminate\Cache\RedisStore;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use ReflectionClass;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Discover;

class Helpers
{
    /**
     * Get model from class or string (by name).
     *
     * @param array<string>|null $paths
     * @return \Illuminate\Database\Eloquent\Model|class-string<\Illuminate\Database\Eloquent\Model>|null
     */
    public static function modelFrom(string $value, bool $asClass = true, ?array $paths = null)
    {
        $paths ??= [app_path('Models')];

        /** @var array<class-string<\Illuminate\Database\Eloquent\Model>> */
        $discoveredClasses = Discover::in(...$paths)
            ->classes()
            ->custom(fn(DiscoveredClass $structure) => mb_strtolower($structure->name) === mb_strtolower($value))
            ->get();

        $firstFoundClass = $discoveredClasses[0] ?? null;

        if (!$firstFoundClass || ($firstFoundClass && (new ReflectionClass($firstFoundClass))->isAbstract())) {
            return null;
        }

        if (!$asClass) {
            return new $firstFoundClass;
        }

        return $firstFoundClass;
    }

    /**
     * Check if object or class string is a valid Laravel model.
     */
    public static function isModel(mixed $class): bool
    {
        if (! is_object($class) && ! is_string($class)) {
            return false;
        }

        if ($class === '' || (is_string($class) && ! class_exists($class))) {
            return false;
        }

        $classReflection = new ReflectionClass($class);

        return $classReflection->isInstantiable()
            && $classReflection->isSubclassOf('Illuminate\Database\Eloquent\Model');
    }

    /**
     * Get model instance from a mix-typed parameter.
     *
     * @param  \Illuminate\Database\Eloquent\Model|int|null  $key
     * @param  class-string<\Illuminate\Database\Eloquent\Model>|string  $class
     * @param  list<string>  $columns
     * @param  list<string>  $with
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static function instanceFrom(mixed $key, string $class, array $columns = ['*'], array $with = [], bool $enforce = false)
    {
        $modelClass = static::resolveModelClass($class);

        if (is_object($key) && ! static::isModel($key)) {
            throw new ModelNotFoundException;
        }

        if ($key instanceof $modelClass && $enforce) {
            return $key->loadMissing($with);
        }

        $model = static::newModelInstance($modelClass);

        return $model->newQuery()->with($with)->whereKey($key)->first($columns);
    }

    /**
     * Get key (id) from a mix-typed parameter.
     *
     */
    public static function keyFrom(mixed $model): mixed
    {
        if (is_numeric($model)) {
            return (int) $model;
        }

        if ($model instanceof Model) {
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
     * @param  \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>|class-string<\Illuminate\Database\Eloquent\Model>|object|string  $model
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>|false
     */
    public static function queryFrom(mixed $model): Builder|false
    {
        if ($model instanceof Builder) {
            return $model->newModelInstance()->newQuery();
        }

        if ($model instanceof Model) {
            return $model->newQuery();
        }

        if (is_string($model) && static::isModelClass($model)) {
            return (new $model)->newQuery();
        }

        return false;
    }

    /**
     * Get lock owner by key or false if lock not existing.
     *
     * @return array<string>|string|false
     */
    public static function getCacheLockOwner(string $key = '*'): array|string|false
    {
        $store = Cache::getStore();

        if (! $store instanceof RedisStore) {
            return false;
        }

        $lockClient = $store->lockConnection()->client();

        if (! is_object($lockClient) || ! method_exists($lockClient, 'keys') || ! method_exists($lockClient, 'get')) {
            return false;
        }

        $prefix = config()->string('database.redis.locks.prefix', '').Cache::getPrefix();

        if (Str::contains($key, '*')) {
            $keys = $lockClient->keys(Cache::getPrefix().$key);

            if (! is_iterable($keys)) {
                return false;
            }

            $owners = [];

            foreach ($keys as $lockKey) {
                if (is_string($lockKey) || is_int($lockKey) || is_float($lockKey)) {
                    $owners[] = Str::replace($prefix, '', (string) $lockKey);
                }
            }

            return $owners;
        }

        $owner = $lockClient->get(Cache::getPrefix().$key);

        if (is_string($owner) || $owner === false) {
            return $owner;
        }

        if (is_int($owner) || is_float($owner)) {
            return (string) $owner;
        }

        return false;
    }

    /**
     * @phpstan-assert-if-true class-string<Model> $class
     */
    protected static function isModelClass(string $class): bool
    {
        return class_exists($class) && static::isModel($class);
    }

    /**
     * @param  class-string<Model>|string  $class
     * @return class-string<Model>
     */
    protected static function resolveModelClass(string $class): string
    {
        if (! static::isModelClass($class)) {
            throw new ModelNotFoundException;
        }

        return $class;
    }

    /**
     * @template T of Model
     *
     * @param  class-string<T>  $class
     * @return T
     */
    protected static function newModelInstance(string $class): Model
    {
        return new $class;
    }
}
