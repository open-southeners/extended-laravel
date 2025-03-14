<?php

namespace Workbench\App\Listeners;

use Workbench\App\Events\FileCreating;
use Workbench\App\Events\FileUpdating;

class MeasureFileSize
{
    /**
     * Handle the event.
     */
    public function handle(FileCreating|FileUpdating $event): void
    {
        //
    }
}
