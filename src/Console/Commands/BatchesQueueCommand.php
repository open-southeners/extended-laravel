<?php

namespace OpenSoutheners\ExtendedLaravel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BatchesQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:batches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List queued job batches';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $columns = ['id', 'name', 'completion', 'total_jobs', 'pending_jobs', 'failed_job_ids', 'created_at', 'finished_at'];

        $this->table(
            Arr::exceptValues($columns, ['total_jobs', 'pending_jobs']),
            DB::connection(config('queue.batching.database'))
                ->table(config('queue.batching.table', 'job_batches'))
                ->get(Arr::exceptValues($columns, ['completion']))
                ->map(function (\stdClass $jobBatch) {
                    $jobBatch = (array) $jobBatch;

                    $jobBatch['total_jobs'] = sprintf('%d%%', $jobBatch['pending_jobs'] / $jobBatch['total_jobs'] * 100);

                    unset($jobBatch['pending_jobs']);

                    $jobBatch['created_at'] = Carbon::createFromTimestamp($jobBatch['created_at']);

                    if ($jobBatch['finished_at']) {
                        $jobBatch['finished_at'] = Carbon::createFromTimestamp($jobBatch['finished_at']);
                    }

                    return $jobBatch;
                })
                ->all(),
        );

        return 0;
    }
}
