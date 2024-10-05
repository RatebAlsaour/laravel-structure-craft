<?php

namespace rateb\structure\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeDTO extends Command
{
    protected $signature = 'make:dto {name}';
    protected $description = 'Create a new Data Transfer Object (DTO) class';

    public function handle()
    {
        // Get the name from the argument
        $name = $this->argument('name');

        // Define the path to the DTOs folder
        $path = app_path('Http/DTOs');

        // Create the DTOs folder if it doesn't exist
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        // Define the DTO file path
        $filePath = $path . '/' . $name . 'Data.php';

        // Check if the file already exists
        if (File::exists($filePath)) {
            $this->error('DTO already exists!');
            return;
        }
        $stub = File::get(app_path('Console/stubs/dto.stub'));

      //  $stub = File::get(app_path('Console/stubs/repository.stub'));

        // Replace placeholders with actual values
        $stub = str_replace('{{ modelName }}', $name, $stub);
        $stub = str_replace('{{ modelVariable }}', lcfirst($name), $stub);

        // Write the content to the file
        File::put($filePath, $stub);

        // Success message
        $this->info("DTO {$name}Data created successfully.");
    }
}
