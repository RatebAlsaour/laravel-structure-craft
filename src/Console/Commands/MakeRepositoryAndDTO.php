<?php

namespace rateb\structure\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepositoryAndDTO extends Command
{
    // Command signature
    protected $signature = 'make:repo-dto {model} {--action=all : Specify "dto", "repo", or "all"}';

    // Command description
    protected $description = 'Create a new Repository and/or DTO for a given Model';

    // Path to the stubs directory
    protected $stubPath = __DIR__ . '/stubs/';

    // Execute the command
    public function handle()
    {
        // Get the model name from the command argument
        $modelName = $this->argument('model');

        // Ensure the model name is provided
        if (!$modelName) {
            $this->error('Please provide a model name.');
            return 1;
        }

        // Get the action from the command options
        $action = $this->option('action');

        switch ($action) {
            case 'dto':
                $this->createDTO($modelName);
                $this->info("DTO created successfully!");
                break;

            case 'repo':
                $this->createRepository($modelName);
                $this->info("Repository created successfully!");
                break;

            case 'all':
            default:
                $this->createDTO($modelName);
                $this->createRepository($modelName);
                $this->info('Repository and DTO created successfully!');
                break;
        }

        return 0;
    }

    // Create the DTO file
    protected function createDTO($modelName)
    {
        $dtoStub = File::get($this->stubPath . 'dto.stub');

        // Replace placeholders with actual model name
        $dtoContent = str_replace('{{ modelName }}', $modelName, $dtoStub);
        $dtoContent = str_replace('{{ modelVariable }}', lcfirst($modelName), $dtoContent);

        // Define the path where the DTO will be created
        $dtoPath = app_path("Http/DTOs/{$modelName}Data.php");

        // Ensure directory exists
        File::ensureDirectoryExists(app_path('Http/DTOs'));

        // Write the DTO file
        File::put($dtoPath, $dtoContent);

        // Print the file path
        $this->info("DTO file created at: {$dtoPath}");
    }

    // Create the Repository file
    protected function createRepository($modelName)
    {
        $repoStub = File::get($this->stubPath . 'repository.stub');

        // Replace placeholders with actual model name
        $repoContent = str_replace('{{ modelName }}', $modelName, $repoStub);
        $repoContent = str_replace('{{ modelVariable }}', lcfirst($modelName), $repoContent);

        // Define the path where the Repository will be created
        $repoPath = app_path("Http/Repositories/{$modelName}Repo.php");

        // Ensure directory exists
        File::ensureDirectoryExists(app_path('Http/Repositories'));

        // Write the Repository file
        File::put($repoPath, $repoContent);

        // Print the file path
        $this->info("Repository file created at: {$repoPath}");
    }
}
