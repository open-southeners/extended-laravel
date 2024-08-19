---
description: >-
  Added commands on top of the replacements did on top of the functionality of
  the Laravel ones.
---

# Commands

### Make commands

All make commands from the framework are replaced to use the `OpensGeneratedFiles` trait which contains a method called `openGeneratedAfter` to open the generated file with an IDE configured.

{% hint style="info" %}
**Compatible IDEs are the following:**

`sublime`, `textmate`, `emacs`, `macvim`, `phpstorm`, `idea`, `vscode`, `vscode-insiders`, `vscode-remote`, `vscode-insiders-remote`, `atom`, `nova`, `netbeans`, `zed`
{% endhint %}

### Make builder

Make custom query builder class (Eloquent) for a specified model name.

```bash
php artisan make:builder User
```

### Horizon flush

Flush horizon database (normally Redis) with records of past jobs.

```bash
php artisan horizon:flush
```

### Queue batches

List all queued jobs sent as batches.

```bash
php artisan queue:batches
```

### Clear atomic locks

{% hint style="warning" %}
Use carefully on production environments, anyway it should ask to confirmation when running on production.
{% endhint %}

Clear all cache locks, [atomic locks](https://laravel.com/docs/11.x/cache#atomic-locks) from Laravel.

```bash
php artisan cache:clearLocks
```

### Check vendor dependencies

Check and list all the config and publishable group that is outdated on your app.

```bash
php artisan vendor:check
```
