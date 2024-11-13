<?php

namespace mouhammadKL\ApiHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeApiRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api-request {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new api FormRequest';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $namespace = "App\\Http\\Requests\\Api";
        $className = $name;

        // Define paths
        $commandsDir = __DIR__;
        $stubPath = $commandsDir . '/../stubs/api/custom-request.api.stub';
        
        $targetPath = app_path("Http/Requests/Api/{$className}.php");

        // Ensure the target directory exists
        File::ensureDirectoryExists(app_path('Http/Requests/Api'));

        // Check if file already exists
        if (File::exists($targetPath)) {
            if (!$this->confirm("The {$className} api request already exists. Do you want to overwrite it?")) {
                $this->info("Operation cancelled. {$className} was not modified.");
                return Command::SUCCESS;
            }
        }

        // Load the stub file and replace placeholders
        $stubContent = File::get($stubPath);
        $stubContent = $this->replacePlaceholders($stubContent, $namespace, $className);

        // Save the final content to the target path
        File::put($targetPath, $stubContent);

        $this->info("{$className} api request created at: {$targetPath}");

        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the stub content with provided values.
     *
     * @param string $stubContent
     * @param string $namespace
     * @param string $className
     * @return string
     */
    protected function replacePlaceholders(string $stubContent, string $namespace, string $className): string
    {
        return str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ rootNamespace }}'],
            [$namespace, $className, 'App\\'],
            $stubContent
        );
    }
}
