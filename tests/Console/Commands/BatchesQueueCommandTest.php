<?php

namespace OpenSoutheners\ExtendedLaravel\Tests\Console\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use OpenSoutheners\ExtendedLaravel\Tests\TestCase;
use Orchestra\Testbench\Attributes\WithMigration;
use Workbench\App\Jobs\ProcessImportedFile;

#[WithMigration('queue')]
class BatchesQueueCommandTest extends TestCase
{
    use RefreshDatabase;

    public function testQueueBatchesCommandGetTableWithResults()
    {
        Bus::batch([
            new ProcessImportedFile('hello world'),
            new ProcessImportedFile('hello another')
        ])->name('ProcessImportedFiles')->dispatch();

        $pending = $this->artisan('queue:batches');

        $pending->assertOk();

        $firstBatch = DB::connection(config('queue.batching.database'))
            ->table(config('queue.batching.table'))
            ->first();

        $pending->expectsTable(
            ['id', 'name', 'completion', 'failed_job_ids', 'created_at', 'finished_at'],
            [
                [
                    $firstBatch->id,
                    'ProcessImportedFiles',
                    '0%',
                    '[]',
                    Carbon::createFromTimestamp($firstBatch->created_at),
                    $firstBatch->finished_at ? Carbon::createFromTimestamp($firstBatch->finished_at) : '',
                ],
            ]
        );
    }
}
