<?php

namespace OpenSoutheners\ExtendedLaravel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use OpenSoutheners\ExtendedLaravel\Http\GeneratedClient;
use Symfony\Component\Finder\Finder;

class HttpClientDocblockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'http:clientDocblock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Docblock for clients that extends the GeneratedClient class';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(private Filesystem $files)
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
        foreach (Finder::create()->in(app_path('Services'))->files()->depth(0) as $file) {
            $reflection = new \ReflectionClass('App\\Services\\'.$file->getFilenameWithoutExtension());

            if ($reflection->getDocComment() || ! $reflection->getExtensionName() === GeneratedClient::class) {
                continue;
            }

            $instance = $reflection->newInstance();
            $docBlock = "/**\n";

            foreach ($instance::FUNCTION_MAP as $function => $method) {
                $docBlock .= " * @method \Illuminate\Http\Client\Response ".Str::camel($function)."(array|null \$data)\n";
            }

            $docBlock .= " */\n";

            $this->files->put($file->getPathname(), str_replace(
                "final class {$file->getFilenameWithoutExtension()} extends GeneratedClient",
                "{$docBlock}final class {$file->getFilenameWithoutExtension()} extends GeneratedClient",
                $this->files->get($file->getPathname()),
            ));
        }

        return 0;
    }
}
