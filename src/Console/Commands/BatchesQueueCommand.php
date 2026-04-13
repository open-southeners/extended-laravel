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
        /** @var list<string> $columns */
        $columns = ['id', 'name', 'completion', 'total_jobs', 'pending_jobs', 'failed_job_ids', 'created_at', 'finished_at'];
        /** @var list<string> $tableColumns */
        $tableColumns = array_values(Arr::exceptValues($columns, ['total_jobs', 'pending_jobs']));
        /** @var list<string> $queryColumns */
        $queryColumns = array_values(Arr::exceptValues($columns, ['completion']));

        $this->table(
            $tableColumns,
            DB::connection(config()->string('queue.batching.database', 'sqlite'))
                ->table(config()->string('queue.batching.table', 'job_batches'))
                ->get($queryColumns)
                ->map(function (\stdClass $jobBatch): array {
                    $totalJobs = $this->normalizeInteger($jobBatch->total_jobs);
                    $pendingJobs = $this->normalizeInteger($jobBatch->pending_jobs);
                    $completion = $totalJobs > 0
                        ? sprintf('%d%%', (int) floor((($totalJobs - $pendingJobs) / $totalJobs) * 100))
                        : '0%';

                    return [
                        'id' => $jobBatch->id,
                        'name' => $jobBatch->name,
                        'completion' => $completion,
                        'failed_job_ids' => $jobBatch->failed_job_ids,
                        'created_at' => Carbon::createFromTimestamp($this->normalizeTimestamp($jobBatch->created_at)),
                        'finished_at' => $jobBatch->finished_at !== null
                            ? Carbon::createFromTimestamp($this->normalizeTimestamp($jobBatch->finished_at))
                            : '',
                    ];
                })
                ->all(),
        );

        return 0;
    }

    protected function normalizeTimestamp(mixed $timestamp): int|float|string
    {
        if (is_int($timestamp) || is_float($timestamp) || is_string($timestamp)) {
            return $timestamp;
        }

        return 0;
    }

    protected function normalizeInteger(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_float($value) || is_string($value)) {
            return (int) $value;
        }

        return 0;
    }
}
