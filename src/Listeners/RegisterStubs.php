<?php

namespace OpenSoutheners\ExtendedLaravel\Listeners;

use Illuminate\Foundation\Events\PublishingStubs;

class RegisterStubs
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(PublishingStubs $event)
    {
        $event->add(__DIR__.'/../Console/Commands/stubs/builder.stub', 'builder');
    }
}
