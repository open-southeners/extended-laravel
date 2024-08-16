<?php

namespace OpenSoutheners\ExtendedLaravel;

use Illuminate\Console\Application as Artisan;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use OpenSoutheners\ExtendedLaravel\Console\Commands;

class ServiceProvider extends BaseServiceProvider
{
    private array $overrides = [
        'command.cast.make' => Commands\CastMakeCommand::class,
        'command.channel.make' => Commands\ChannelMakeCommand::class,
        'command.class.make' => Commands\ClassMakeCommand::class,
        'command.console.make' => Commands\ConsoleMakeCommand::class,
        'command.controller.make' => Commands\ControllerMakeCommand::class,
        'command.enum.make' => Commands\EnumMakeCommand::class,
        'command.event.make' => Commands\EventMakeCommand::class,
        'command.exception.make' => Commands\ExceptionMakeCommand::class,
        'command.factory.make' => Commands\FactoryMakeCommand::class,
        'command.interface.make' => Commands\InterfaceMakeCommand::class,
        'command.job.make' => Commands\JobMakeCommand::class,
        'command.listener.make' => Commands\ListenerMakeCommand::class,
        'command.mail.make' => Commands\MailMakeCommand::class,
        'command.middleware.make' => Commands\MiddlewareMakeCommand::class,
        MigrateMakeCommand::class => Commands\MigrateMakeCommand::class,
        'command.model.make' => Commands\ModelMakeCommand::class,
        'command.notification.make' => Commands\NotificationMakeCommand::class,
        'command.observer.make' => Commands\ObserverMakeCommand::class,
        'command.policy.make' => Commands\PolicyMakeCommand::class,
        'command.provider.make' => Commands\ProviderMakeCommand::class,
        'command.request.make' => Commands\RequestMakeCommand::class,
        'command.resource.make' => Commands\ResourceMakeCommand::class,
        'command.rule.make' => Commands\RuleMakeCommand::class,
        'command.scope.make' => Commands\ScopeMakeCommand::class,
        'command.seeder.make' => Commands\SeederMakeCommand::class,
        'command.test.make' => Commands\TestMakeCommand::class,
        'command.trait.make' => Commands\TraitMakeCommand::class,
        'command.view.make' => Commands\ViewMakeCommand::class,
    ];

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

        $this->app->booted(function() {
			Artisan::starting(function() {
                // Laravel replacements to get them the OpensFile class
                foreach ($this->overrides as $abstract => $override) {
                    $this->app->singleton($abstract, $override);
                }
            });
        });

        $this->commands([
            Commands\BatchesQueueCommand::class,
            Commands\BuilderMakeCommand::class,
            Commands\CheckVendorCommand::class,
            Commands\ClearLocksCacheCommand::class,
            Commands\FlushHorizonCommand::class,
        ]);
    }
}
