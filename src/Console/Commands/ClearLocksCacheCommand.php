<?php

namespace OpenSoutheners\ExtendedLaravel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

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

        $cacheDriver = config()->string('cache.default');

        if ($cacheDriver === 'redis') {
            Redis::connection(config()->string('cache.stores.redis.lock_connection', 'default'))->flushDb();
        } elseif ($cacheDriver === 'database') {
            $lockConnection = config('cache.stores.database.lock_connection');

            if (! is_string($lockConnection)) {
                $lockConnection = config('cache.stores.database.connection');
            }

            $lockTable = config('cache.stores.database.lock_table');

            if (! is_string($lockTable)) {
                $lockTable = 'cache_locks';
            }

            DB::connection(is_string($lockConnection) ? $lockConnection : null)
                ->table($lockTable)
                ->truncate();
        }

        $this->info('Cache locks cleared successfully!');

        return 0;
    }
}
