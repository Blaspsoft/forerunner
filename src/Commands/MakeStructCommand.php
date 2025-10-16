<?php

namespace Blaspsoft\Forerunner\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeStructCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:struct';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new LLM structure class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Structure';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../stubs/struct.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Structures';
    }

    /**
     * Get the console command options.
     *
     * @return array<int, array<int, mixed>>
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the structure already exists'],
        ];
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);

        $structName = $this->getStructName($name);

        return str_replace('{{ structName }}', $structName, $stub);
    }

    /**
     * Get the struct name from the class name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getStructName($name)
    {
        $className = class_basename($name);

        // Convert PascalCase to snake_case
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className) ?? $className);
    }
}
