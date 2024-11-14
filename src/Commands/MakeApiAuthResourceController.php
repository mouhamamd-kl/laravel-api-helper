<?php

namespace mouhammadKL\ApiHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeApiAuthResourceController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api-authController {model} {--namespace=App\Http\Controllers}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new api controller class';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get the model name and prepare paths
        $model = $this->argument('model');
        $namespace = $this->option('namespace');
        $namespacedModel = "App\\Models\\$model"; // Adjust this as necessary for your structure
        $commandsDir = __DIR__;
        $stubPath = $commandsDir . '/../stubs/api/custom-auth-controller.api.stub';
        $targetPath = app_path("Http/Controllers/{$model}Controller.php");

        // Check or create helpers

        $this->ensureHelperExists('ApiResponse', __DIR__ . '/../stubs/helpers/api-response.stub', app_path('Helpers/ApiResponse.php'));
        $this->ensureHelperExists('PaginationHelper', __DIR__ . '/../stubs/helpers/pagination-helper.stub', app_path('Helpers/PaginationHelper.php'));


        // Check if the controller already exists
        if (File::exists($targetPath)) {
            if (!$this->confirm("The controller {$model}Controller already exists. Do you want to replace it?")) {
                return Command::SUCCESS; // Exit if user doesn't want to overwrite
            }
        }

        // Ensure the target directory exists
        File::ensureDirectoryExists(app_path('Http/Controllers'));

        // Load the stub file and replace placeholders
        $stubContent = File::get($stubPath);
        $stubContent = $this->replacePlaceholders($stubContent, $model, $namespace, $namespacedModel);

        // Save the final content to the target path
        File::put($targetPath, $stubContent);

        $this->info("api Controller created at: {$targetPath}");

        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the stub content with dynamic values.
     *
     * @param string $stubContent
     * @param string $model
     * @param string $namespace
     * @param string $namespacedModel
     * @return string
     */
    protected function replacePlaceholders(string $stubContent, string $model, string $namespace, string $namespacedModel): string
    {
        return str_replace(
            ['{{ namespace }}', '{{ rootNamespace }}', '{{ model }}', '{{ namespacedModel }}', '{{ class }}'],
            [$namespace, 'App\\', $model, $namespacedModel, "{$model}Controller"],
            $stubContent
        );
    }
    /**
     * Check if a helper exists and create it if it doesn't.
     *
     * @param string $helperName
     * @param string $stubPath
     * @param string $targetPath
     * @return void
     */
    protected function ensureHelperExists(string $helperName, string $stubPath, string $targetPath): void
    {
        if (!File::exists($targetPath)) {
            File::ensureDirectoryExists(dirname($targetPath));
            File::copy($stubPath, $targetPath);
            $this->info("{$helperName} helper created at: {$targetPath}");
        }
    }
}
