<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakePaginationHelper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:pagination-helper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the PaginationHelper file from a stub';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Check if ApiResponse exists, if not, run its command
        if (!File::exists(app_path('Helpers/ApiResponse.php'))) {
            $this->warn("ApiResponse helper not found. Creating ApiResponse helper...");
            $this->call('make:api-response');
        }

        // Define paths
        $stubPath = base_path('stubs/helpers/pagination-helper.stub');
        $targetPath = app_path("Helpers/PaginationHelper.php");

        // Ensure the target directory exists
        File::ensureDirectoryExists(app_path('Helpers'));

        // Check if PaginationHelper already exists
        if (File::exists($targetPath)) {
            if (!$this->confirm("The PaginationHelper file already exists. Do you want to overwrite it?")) {
                $this->info("Operation cancelled. PaginationHelper was not modified.");
                return Command::SUCCESS;
            }
        }

        // Load the stub file and replace placeholders
        $stubContent = File::get($stubPath);
        $stubContent = $this->replacePlaceholders($stubContent);

        // Save the final content to the target path
        File::put($targetPath, $stubContent);

        $this->info("PaginationHelper created at: {$targetPath}");
        
        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the stub content with static values.
     *
     * @param string $stubContent
     * @return string
     */
    protected function replacePlaceholders(string $stubContent): string
    {
        return str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ rootNamespace }}'],
            ['App\Helpers', 'PaginationHelper', 'App\\'],
            $stubContent
        );
    }
}
