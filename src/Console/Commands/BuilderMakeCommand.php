<?php

namespace OpenSoutheners\ExtendedLaravel\Console\Commands;

use App\Support\Skore;
use Illuminate\Support\Str;
use OpenSoutheners\ExtendedLaravel\Console\FileGeneratorCommand;
use ReflectionClass;
use Symfony\Component\Console\Input\InputArgument;

class BuilderMakeCommand extends FileGeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:builder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent builder class for a model';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Builder';

    public function handle()
    {
        parent::handle();

        $this->writeModelBuilder();

        return true;
    }

    /**
     * Write new builder function instantiator to model file.
     *
     * @return void
     */
    protected function writeModelBuilder()
    {
        $model = $this->argument('name');

        $reflection = new ReflectionClass(Skore::getModelFrom($model));

        $modelFilePath = $reflection->getFileName();

        $modelContents = $this->files->get($modelFilePath);

        $modelContents = Str::replaceLast('}', '', $modelContents);

        $builderClassName = Str::studly($model).Str::ucfirst($this->type);

        $modelContents .= <<<EOT
    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  \$query
     * @return \App\Builders\\{$builderClassName}
     */
    public function newEloquentBuilder(\$query)
    {
        return new {$builderClassName}(\$query);
    }
}
EOT;

        $this->files->put($modelFilePath, $modelContents);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/builder.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        return $this->laravel->basePath().'/app/Builders/'.$name.'.php';
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        return $name.$this->type;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name or class of the model'],
        ];
    }
}
