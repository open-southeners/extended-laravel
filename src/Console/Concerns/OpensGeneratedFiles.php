<?php

namespace OpenSoutheners\ExtendedLaravel\Console\Concerns;

use Closure;
use OpenSoutheners\ExtendedLaravel\Events\CommandFileGenerated;

/**
 * @mixin \Illuminate\Console\GeneratorCommand
 */
trait OpensGeneratedFiles
{
    /**
     * Execute the console command.
     *
     * @template T
     * @param \Closure(): (T) $callback
     * @return T
     */
    public function openGeneratedAfter(Closure $callback)
    {
        return tap(call_user_func($callback), fn () => event(
            new CommandFileGenerated(
                $this->getPath($this->qualifyClass($this->getNameInput()))
            )
        ));
    }
}
