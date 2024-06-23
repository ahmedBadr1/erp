<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class EnumMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:enum {name} {values*}';

    /**
     * The console command description.
     * usage  php artisan make:enum OrderStatus paid unpaid partials
     * @var string
     */
    protected $description = 'Create a new Enum';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Enum';


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/Stubs/enum.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Enums';
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the enum class.'],
            ['values', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'The values for the enum.'],
        ];
    }

    protected function replaceClass($stub, $name)
    {
        $nameArg = $this->argument('name');

        if (Str::contains($nameArg, '/')) {
            // The argument contains a folder name along with the class name
            $folder = Str::beforeLast($nameArg, '/');
            $className = Str::afterLast($nameArg, '/');

            $folderExists = File::exists(app_path($folder));

            if ($folderExists) {
                $namespace = "App\\$folder";
            } else {
                $namespace = "App";
            }
        } else {
            // The argument contains only the class name
            $namespace = "App";
            $className = $nameArg;
        }

        $stub = str_replace('{{namespace}}', $namespace, $stub);
        $stub = str_replace('{{className}}', $className, $stub);

        $valuesArg = $this->argument('values');
        $values = array_map(function ($value) {
            return '    case ' . strtoupper($value) . ' = "' . $value . '" ;';
        }, $valuesArg);
        $values = implode("\n", $values);
        $stub = str_replace('{{values}}', $values, $stub);

//        $map = array_map(function ($value) {
//            return '            static::' . strtoupper($value) . ' => "' . $value . '" ,';
//        }, $valuesArg);
//        $map = implode("\n", $map);
//        $stub = str_replace('{{map}}', $static, $stub);

        return parent::replaceClass($stub, $name);
    }


}
