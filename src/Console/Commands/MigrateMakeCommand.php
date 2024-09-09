<?php

namespace OpenSoutheners\ExtendedLaravel\Console\Commands;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand as BaseCommand;
use OpenSoutheners\ExtendedLaravel\Console\Concerns\OpensGeneratedFiles;

class MigrateMakeCommand extends BaseCommand
{
    use OpensGeneratedFiles;

    protected ?string $migrationOutputPath = null;

    public function handle()
    {
        $this->openGeneratedAfter(function () {
            $this->creator->afterCreate(function (?string $table = null, ?string $path = null) {
                $this->migrationOutputPath = $path;
            });

            parent::handle();
        });
    }

    protected function getPath(string $input): string
    {
        return $input;
    }

    protected function qualifyClass(string $input): string
    {
        return $input;
    }

    protected function getNameInput(): string
    {
        return $this->migrationOutputPath;
    }
}
