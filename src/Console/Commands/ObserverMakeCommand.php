<?php

namespace OpenSoutheners\ExtendedLaravel\Console\Commands;

use Illuminate\Foundation\Console\ObserverMakeCommand as BaseCommand;
use OpenSoutheners\ExtendedLaravel\Console\Concerns\OpensGeneratedFiles;

class ObserverMakeCommand extends BaseCommand
{
    use OpensGeneratedFiles;

    public function handle()
    {
        return $this->openGeneratedAfter(fn () => parent::handle());
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : dirname((new \ReflectionClass(BaseCommand::class))->getFileName()).$stub;
    }
}
