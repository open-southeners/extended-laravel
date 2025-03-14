<?php

namespace Workbench\App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessImportedFile implements ShouldQueue
{
    use Batchable;

    public function __construct(protected string $value)
    {
        //
    }

    public function handle()
    {
        return true;
    }
}
