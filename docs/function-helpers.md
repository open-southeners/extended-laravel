---
description: List of functions that works for Laravel Eloquent models.
---

# Function helpers

This is the list of namespaced functions that you can import and use on your Laravel app.

## model\_from

Gets model fully qualified class or instanced object from given string.

```php
\OpenSoutheners\ExtendedPhp\Models\model_from('post');
// 'App\Models\Post'

\OpenSoutheners\ExtendedPhp\Models\model_from('post', false);
// object instance for App\Models\Post

\OpenSoutheners\ExtendedPhp\Models\model_from('post', true, 'Domain\Posts');
// 'Domain\Posts\Post'
```

## is\_model

Checks if object or class is a Eloquent model.

```php
\OpenSoutheners\ExtendedPhp\Models\is_model(App\Models\Post::class);
// true

\OpenSoutheners\ExtendedPhp\Models\is_model(new App\Models\Post());
// true
```

## instance\_from

Creates a model instance from given key (generally its ID or specified primary key).

```php
\OpenSoutheners\ExtendedPhp\Models\instance_from(1, App\Models\Post::class);
// object instance for App\Models\Post with ID 1

\OpenSoutheners\ExtendedPhp\Models\instance_from(1, App\Models\Post::class, ['id', 'title']);
// object instance for App\Models\Post with ID 1 and only selecting id and title columns

\OpenSoutheners\ExtendedPhp\Models\instance_from(1, App\Models\Post::class, ['*'], ['author']);
// object instance for App\Models\Post with ID 1 and with author relationship loaded
```

## key\_from

Tries to get Eloquent model key from given variable (can be of multiple types).

```php
\OpenSoutheners\ExtendedPhp\Models\instance_from('1');
// 1

\OpenSoutheners\ExtendedPhp\Models\instance_from('80b6dc25-8773-4639-abcf-ed1f157deea1');
// '80b6dc25-8773-4639-abcf-ed1f157deea1'

\OpenSoutheners\ExtendedPhp\Models\instance_from(App\Models\Post::find(1));
// 1
```

## query\_from

Gets always a new Eloquent query builder instance from given model.

```php
\OpenSoutheners\ExtendedPhp\Models\query_from(App\Models\Post::class);
// object instance for \Illuminate\Database\Eloquent\Builder

\OpenSoutheners\ExtendedPhp\Models\query_from(App\Models\Post::query()->where('title', 'hello'));
// new object instance for \Illuminate\Database\Eloquent\Builder
```
