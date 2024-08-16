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
            // Laravel replacements to get them the FileGeneratorCommand class
            Commands\CastMakeCommand::class,
            Commands\ChannelMakeCommand::class,
            Commands\ClassMakeCommand::class,
            Commands\ConsoleMakeCommand::class,
            Commands\ControllerMakeCommand::class,
            Commands\EnumMakeCommand::class,
            Commands\EventMakeCommand::class,
            Commands\ExceptionMakeCommand::class,
            Commands\FactoryMakeCommand::class,
            Commands\InterfaceMakeCommand::class,
            Commands\JobMakeCommand::class,
            Commands\ListenerMakeCommand::class,
            Commands\MailMakeCommand::class,
            Commands\MiddlewareMakeCommand::class,
            Commands\MigrateMakeCommand::class,
            Commands\ModelMakeCommand::class,
            Commands\NotificationMakeCommand::class,
            Commands\ObserverMakeCommand::class,
            Commands\PolicyMakeCommand::class,
            Commands\ProviderMakeCommand::class,
            Commands\RequestMakeCommand::class,
            Commands\ResourceMakeCommand::class,
            Commands\RuleMakeCommand::class,
            Commands\ScopeMakeCommand::class,
            Commands\SeederMakeCommand::class,
            Commands\TestMakeCommand::class,
            Commands\TraitMakeCommand::class,
            Commands\ViewMakeCommand::class,

            Commands\BatchesQueueCommand::class,
            Commands\BuilderMakeCommand::class,
            Commands\CheckVendorCommand::class,
            Commands\ClearLocksCacheCommand::class,
            Commands\FlushHorizonCommand::class,
        ]);
    }
}
