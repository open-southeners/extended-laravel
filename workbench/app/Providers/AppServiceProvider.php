<?php

namespace Workbench\App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Workbench\App\Events;
use Workbench\App\Listeners;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Event::listen(Events\FileCreating::class, Listeners\MeasureFileSize::class);
        Event::listen(Events\FileUpdating::class, Listeners\MeasureFileSize::class);
    }
}
