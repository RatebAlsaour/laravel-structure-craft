<?php

namespace RatebSa\Structure\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeFilter extends Command
{
    // Command signature
    protected $signature = 'make:filter {filterName}';

    // Command description
    protected $description = 'Create a new filter class';

    // Path to the stubs directory
    protected $stubPath = __DIR__ . '/stubs/';

    // Execute the command
    public function handle()
    {
        // Get the filter name from the command argument
        $filterName = $this->argument('filterName');

        // Check if the filter already exists
        if ($this->filterExists($filterName)) {
            $this->error("Filter '{$filterName}' already exists!");
            return 1;
        }

        // Create the filter
        $this->createFilter($filterName);
        $this->info("Filter '{$filterName}' created successfully!");

        return 0;
    }

    // Check if the filter already exists
    protected function filterExists($filterName)
    {
        $filterPath = app_path("Filters/{$filterName}.php");
        return File::exists($filterPath);
    }

    // Create the Filter file
    protected function createFilter($filterName)
    {
        // Get the stub file content
        $filterStub = File::get($this->stubPath . 'filter.stub');

        // Replace placeholders in the filter stub
        $className = basename($filterName); // Get the class name (last part after the last slash)
        $namespace = 'App\Filters\\' . dirname($filterName); // Construct the namespace from the filterName

        // Replace the namespace and class placeholders
        $filterContent = str_replace('{{ class }}', $className, $filterStub);
        $filterContent = str_replace('{{ namespace }}', $namespace, $filterContent);

        // Define the path where the Filter will be created
        $filterPath = app_path("Filters/{$filterName}.php");
        $filterDir = dirname($filterPath); // Get the directory of the filter

        // Ensure directory exists
        File::ensureDirectoryExists($filterDir);

        // Write the Filter file
        File::put($filterPath, $filterContent);

        // Print the file path
        $this->info("Filter file created at: {$filterPath}");
    }
}
