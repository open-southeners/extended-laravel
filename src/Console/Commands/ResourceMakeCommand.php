<?php

namespace OpenSoutheners\ExtendedLaravel\Console\Commands;

use Illuminate\Foundation\Console\ResourceMakeCommand as BaseCommand;
use OpenSoutheners\ExtendedLaravel\Console\Concerns\OpensGeneratedFiles;

class ResourceMakeCommand extends BaseCommand
{
    use OpensGeneratedFiles;

    public function handle()
    {
        $this->openGeneratedAfter(fn () => parent::handle());
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
