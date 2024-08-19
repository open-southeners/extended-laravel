---
description: List of functions that works for Laravel Eloquent models.
---

# Helpers

This is the list of functions that you can import and use on your Laravel app through the static methods of the `OpenSoutheners\ExtendedLaravel\Helpers` class.

## modelFrom

Gets model fully qualified class or instanced object from given string.

```php
Helpers::modelFrom('post');
// 'App\Models\Post'

Helpers::modelFrom('post', false);
// object instance for App\Models\Post

Helpers::modelFrom('post', true, 'Domain\Posts');
// 'Domain\Posts\Post'
```

## isModel

Checks if object or class is a Eloquent model.

```php
Helpers::isModel(App\Models\Post::class);
// true

Helpers::isModel(new App\Models\Post());
// true
```

## instanceFrom

Creates a model instance from given key (generally its ID or specified primary key).

```php
Helpers::instanceFrom(1, App\Models\Post::class);
// object instance for App\Models\Post with ID 1

Helpers::instanceFrom(1, App\Models\Post::class, ['id', 'title']);
// object instance for App\Models\Post with ID 1 and only selecting id and title columns

Helpers::instanceFrom(1, App\Models\Post::class, ['*'], ['author']);
// object instance for App\Models\Post with ID 1 and with author relationship loaded
```

## keyFrom

Tries to get Eloquent model key from given variable (can be of multiple types).

```php
Helpers::keyFrom('1');
// 1

Helpers::keyFrom('80b6dc25-8773-4639-abcf-ed1f157deea1');
// '80b6dc25-8773-4639-abcf-ed1f157deea1'

Helpers::keyFrom(App\Models\Post::find(1));
// 1
```

## queryFrom

Gets always a new Eloquent query builder instance from given model.

```php
Helpers::queryFrom(App\Models\Post::class);
// object instance for \Illuminate\Database\Eloquent\Builder

Helpers::queryFrom(App\Models\Post::query()->where('title', 'hello'));
// new object instance for \Illuminate\Database\Eloquent\Builder
```

## getCacheLockOwner

Get cache atomic locks owner or false otherwise if no lock found.

```php
Cache::lock('podcasts.1.upload')->get();

Helpers::getCacheLockOwner('podcasts.1.upload');
// 432afra2pjna

Cache::lock('podcasts.2.upload')->get();

Helpers::getCacheLockOwner('podcasts.*');
// ['podcasts.1.upload', 'podcasts.2.upload']
```
