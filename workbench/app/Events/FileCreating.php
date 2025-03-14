<?php

namespace Workbench\App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workbench\App\Models\File;

class FileCreating
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public File $model)
    {
        //
    }
}
