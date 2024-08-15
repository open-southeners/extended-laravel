<?php

namespace OpenSoutheners\ExtendedLaravel;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use OpenSoutheners\ExtendedLaravel\Console\Commands;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        AboutCommandIntegration::register();

        Event::listen(
            \Illuminate\Foundation\Events\PublishingStubs::class,
            \OpenSoutheners\ExtendedLaravel\Listeners\RegisterStubs::class
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        \Illuminate\Support\Arr::mixin(new \OpenSoutheners\ExtendedLaravel\Support\Arr);
        \Illuminate\Support\Str::mixin(new \OpenSoutheners\ExtendedLaravel\Support\Str);
        \Illuminate\Support\Stringable::mixin(new \OpenSoutheners\ExtendedLaravel\Support\Stringable);
        \Illuminate\Support\Facades\Storage::mixin(new \OpenSoutheners\ExtendedLaravel\Support\Storage);
        \Illuminate\Support\Collection::mixin(new \OpenSoutheners\ExtendedLaravel\Support\Collection);
        \Illuminate\Events\Dispatcher::mixin(new \OpenSoutheners\ExtendedLaravel\Events\Dispatcher);
        \Illuminate\Validation\Rule::mixin(new \OpenSoutheners\ExtendedLaravel\Validation\Rule);

        $this->commands([
            Commands\BatchesQueueCommand::class,
            Commands\BuilderMakeCommand::class,
            Commands\CheckVendorCommand::class,
            Commands\ClearLocksCacheCommand::class,
            Commands\FlushHorizonCommand::class,
            Commands\HttpClientDocblockCommand::class,
        ]);
    }
}
