<?php

namespace OpenSoutheners\ExtendedLaravel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Output\OutputInterface;

class CheckVendorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendor:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check vendor dependencies in search of local outdated dependencies';

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->filesystem = Storage::createLocalDriver(['root' => base_path()]);
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        /** @var array<string, string> $packagesArr */
        $packagesArr = ServiceProvider::pathsToPublish();
        /** @var list<array{from: string, from_path: string, fullpath: string, path: string, last_updated: string}> $publishablesArr */
        $publishablesArr = [];

        foreach ($packagesArr as $origin => $destination) {
            if (is_dir($origin)) {
                $originPath = $this->getPathForStorage($origin);
                $originFiles = $this->filesystem->allFiles($originPath);

                foreach ($originFiles as $file) {
                    $relativeFilePath = Str::after($file, $originPath.'/');
                    $publishable = $this->getFileDiff(
                        base_path($file),
                        rtrim($destination, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$relativeFilePath,
                    );

                    if ($publishable !== []) {
                        $publishablesArr[] = $publishable;
                    }
                }
            } else {
                $publishable = $this->getFileDiff($origin, $destination);

                if ($publishable !== []) {
                    $publishablesArr[] = $publishable;
                }
            }
        }

        $headers = ['from_path', 'path', 'last_updated'];

        $this->table(
            array_map('strtoupper', $headers),
            array_map(fn (array $publishable): array => Arr::only($publishable, $headers), $publishablesArr)
        );

        return 0;
    }

    /**
     * @return array{from: string, from_path: string, fullpath: string, path: string, last_updated: string}|array{}
     */
    protected function getFileDiff(string $origin, string $destination): array
    {
        $originPath = $this->getPathForStorage($origin);
        $destinationPath = $this->getPathForStorage($destination);

        if (! file_exists($destination)) {
            $this->info("{$originPath} is not present in your app, you may want to customise or not (optional)", OutputInterface::VERBOSITY_VERBOSE);

            return [];
        }

        $originHash = sha1_file($origin);
        $destinationHash = sha1_file($destination);

        if ($originHash !== $destinationHash) {
            $this->info("{$originHash} compared to {$destinationHash}", OutputInterface::VERBOSITY_VERBOSE);
            $this->warn("{$destinationPath} is outdated!", OutputInterface::VERBOSITY_VERBOSE);

            return [
                'from' => $origin,
                'from_path' => $originPath,
                'fullpath' => $destination,
                'path' => $destinationPath,
                'last_updated' => Carbon::createFromTimestamp($this->filesystem->lastModified($this->getPathForStorage($origin)))->diffForHumans(
                    Carbon::createFromTimestamp($this->filesystem->lastModified($this->getPathForStorage($destination))),
                ),
            ];
        }

        return [];
    }

    protected function getPathForStorage(string $path): string
    {
        return ltrim(Str::after($path, base_path()), DIRECTORY_SEPARATOR);
    }
}
