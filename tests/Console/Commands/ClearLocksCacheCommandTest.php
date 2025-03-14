<?php

namespace OpenSoutheners\ExtendedLaravel\Tests\Console\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use OpenSoutheners\ExtendedLaravel\Tests\TestCase;
use Orchestra\Testbench\Attributes\WithMigration;

#[WithMigration('cache')]
class ClearLocksCacheCommandTest extends TestCase
{
    use RefreshDatabase;

    public function testClearLocksGetsEverythingClear()
    {
        config(['cache.default' => 'database']);

        Cache::lock('test', 9)->get();

        $this->assertDatabaseCount('cache_locks', 1);

        $command = $this->artisan('cache:clearLocks');

        $command->assertOk();

        $command->expectsOutput('Cache locks cleared successfully!');

        $command->run();

        $this->assertDatabaseCount('cache_locks', 0);
    }
}
