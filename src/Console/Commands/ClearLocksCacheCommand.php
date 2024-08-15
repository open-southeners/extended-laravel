<?php

namespace OpenSoutheners\ExtendedLaravel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\Cache;

class ClearLocksCacheCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clearLocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear cache atomic locks from the database used.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        Cache::lockConnection()->flushDb();

        $this->info('Cache locks cleared successfully!');

        return 0;
    }
}
