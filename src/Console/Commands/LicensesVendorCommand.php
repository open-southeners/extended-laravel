<?php

namespace OpenSoutheners\ExtendedLaravel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Process;

class LicensesVendorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendor:licenses
                            {--prod : List only production dependencies (default list all)}
                            {--format= : Format to output the licenses (can be markdown, json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all dependencies licenses from Composer (PHP) and/or NPM (NodeJS)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(protected Composer $composer)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $headings = ['Component', 'License'];

        $data = Collection::make([])
            ->merge($this->fetchComposerDependencies())
            ->merge($this->fetchNodeDependencies());

        match ($this->option('format')) {
            'markdown' => $this->displayAsMarkdown($headings, $data),
            default => $this->table($headings, $data),
        };

        return 0;
    }

    protected function displayAsMarkdown(array $headings, Collection $data): void
    {
        $formattedHeadings = array_map(fn (string $heading) => Str::wrap($heading, " "), $headings);

        $markdown = Str::wrap(Arr::join($formattedHeadings, '|'), '|')."\n";

        $markdown .= Str::wrap(
          Collection::make()
              ->range(0, count($formattedHeadings)-1)
              ->map(fn (int $n) => Str::wrap(str_repeat('-', strlen($formattedHeadings[$n])), ' '))
              ->join('|'),
          '|',
          '|'
        )."\n";

        $data->each(function (array $item) use (&$markdown) {
            $markdown .= Str::wrap(Arr::join($item, '|'), '|')."\n";
        });

        $this->line($markdown);
    }

    protected function fetchNodeDependencies(): Collection
    {
        $command = ['npx', 'license-report'];

        if ($this->option('prod')) {
            $command[] = '--only=prod';
        }

        $result = Process::run(implode(' ', $command));

        return Collection::make(json_decode($result->output(), true))
            ->map(fn(array $dependency) => ['Component' => $dependency['name'], 'License' => $dependency['licenseType']])
            ->values();
    }

    protected function fetchComposerDependencies(): Collection
    {
        $command = $this->composer->findComposer();

        $command[] = 'licenses';
        $command[] = '--format=json';

        if ($this->option('prod')) {
            $command[] = '--no-dev';
        }

        $result = Process::run(implode(' ', $command));

        return Collection::make(json_decode($result->output(), true)['dependencies'])
            ->map(fn(array $dependency, string $name) => ['Component' => $name, 'License' => implode(', ', $dependency['license'])])
            ->values();
    }
}
