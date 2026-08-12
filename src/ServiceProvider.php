<?php

namespace OpenSoutheners\ExtendedLaravel;

use Illuminate\Config\Repository;
use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Events\PublishingStubs;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Validation\Rule;
use OpenSoutheners\ExtendedLaravel\Console\Commands;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * @var array<string, class-string<GeneratorCommand>>
     */
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
            PublishingStubs::class,
            Listeners\RegisterStubs::class
        );

        Event::listen(
            Events\CommandFileGenerated::class,
            Listeners\OpenUserPreferredEditor::class
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Repository::mixin(new Config\Repository);
        Arr::mixin(new Support\Arr);
        Str::mixin(new Support\Str);
        Stringable::mixin(new Support\Stringable);
        Number::mixin(new Support\Number);
        Storage::mixin(new Support\Storage);
        Collection::mixin(new Support\Collection);
        Dispatcher::mixin(new Events\Dispatcher);
        Rule::mixin(new Validation\Rule);

        // Laravel replacements to get the modified with OpensGeneratedFiles trait
        $this->app->booted(function () {
            Artisan::starting(function (Artisan $artisan) {
                // Laravel (and packages like Nova, which registers its own `nova:policy` etc.
                // commands) resolve generator commands through the base Laravel class itself as
                // the container abstract, so rebinding that abstract to our override fights any
                // other package doing the same thing for that class and can leave Artisan with
                // conflicting command registrations. Instead, we add our replacement command
                // directly to the console application under its existing name (e.g. `make:policy`),
                // which cleanly replaces Laravel's own registration without touching the shared
                // class binding that other packages depend on.
                $artisan->add(new Commands\MigrateMakeCommand(
                    $this->app->make('migration.creator'),
                    $this->app->make('composer')
                ));

                foreach ($this->overrides as $abstract => $override) {
                    $this->app->singleton($abstract, $override);

                    $artisan->add($this->resolveOverrideCommand($override));
                }
            });
        });

        $this->commands([
            Commands\BatchesQueueCommand::class,
            Commands\BuilderMakeCommand::class,
            Commands\CheckVendorCommand::class,
            Commands\ClearLocksCacheCommand::class,
            Commands\FlushHorizonCommand::class,
            Commands\LicensesVendorCommand::class,
        ]);
    }

    /**
     * @param  class-string<GeneratorCommand>  $override
     */
    private function resolveOverrideCommand(string $override): Command
    {
        $command = $this->app->make($override);

        if (! $command instanceof Command) {
            throw new \RuntimeException("Expected [{$override}] to resolve to an Artisan command instance.");
        }

        return $command;
    }
}
