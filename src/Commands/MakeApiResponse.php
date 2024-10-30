<?php

namespace mouhammadKL\ApiHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeApiResponse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api-response';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the ApiResponse file from a stub';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Define paths
        $stubPath = base_path('stubs/helpers/api-response.stub');
        $targetPath = app_path("Helpers/ApiResponse.php");

        // Ensure the target directory exists
        File::ensureDirectoryExists(app_path('Helpers'));

        // Check if the ApiResponse file already exists
        if (File::exists($targetPath)) {
            if (!$this->confirm("The ApiResponse file already exists. Do you want to overwrite it?")) {
                $this->info("Operation cancelled. ApiResponse was not modified.");
                return Command::SUCCESS;
            }
        }

        // Load the stub file and replace placeholders
        $stubContent = File::get($stubPath);
        $stubContent = $this->replacePlaceholders($stubContent);

        // Save the final content to the target path
        File::put($targetPath, $stubContent);

        $this->info("ApiResponse created at: {$targetPath}");
        
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
            ['{{ namespace }}', '{{ class }}'],
            ['App\Helpers', 'ApiResponse'],
            $stubContent
        );
    }
}
