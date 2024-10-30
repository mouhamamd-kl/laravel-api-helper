<?php

namespace mouhammadKL\ApiHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeApiModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api-model {name} {--namespace=App\Models} {--controller} {--request} {--migration} {--seeder} {--factory} {--resource} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model  with options to create a full model';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Retrieve the name and other options
        $name = $this->argument('name');
        $namespace = $this->option('namespace');

        // Check or create helpers
        // $this->ensureHelperExists('api-response', base_path('stubs/helpers/api-response.stub'), app_path('Helpers/ApiResponse.php'));
        // $this->ensureHelperExists('pagination-helper', base_path('stubs/helpers/pagination-helper.stub'), app_path('Helpers/PaginationHelper.php'));

        // If --all is set, enable all options
        $createController = $this->option('controller') || $this->option('all');
        $createRequest = $this->option('request') || $this->option('all');
        $createMigration = $this->option('migration') || $this->option('all');
        $createSeeder = $this->option('seeder') || $this->option('all');
        $createFactory = $this->option('factory') || $this->option('all');
        $createResource = $this->option('resource') || $this->option('all');

        // Define paths
        $stubPath = base_path('stubs/api/custom-model.api.stub');
        $targetPath = app_path("Models/{$name}.php");

        // Check if the model already exists
        if (File::exists($targetPath)) {
            if (!$this->confirm("The api model {$name} already exists. Do you want to replace it?")) {
                return Command::SUCCESS;
            }
        }

        // Ensure the target directory exists
        File::ensureDirectoryExists(app_path('Models'));

        // Load the stub file and replace placeholders
        $stubContent = File::get($stubPath);
        $stubContent = $this->replacePlaceholders($stubContent, $name, $namespace);

        // Save the final content to the target path
        File::put($targetPath, $stubContent);

        $this->info("Model created at: {$targetPath}");

        // Conditionally create each component based on options
        if ($createController) {
            $this->call('make:api-controller', [
                'model' => $name,
            ]);
        }

        if ($createRequest) {
            $this->call('make:api-request', [
                'name' => "{$name}Request",
            ]);
        }

        if ($createMigration) {
            $this->call('make:migration', [
                'name' => "create_{$name}s_table",
                '--create' => strtolower($name) . 's',
            ]);
        }

        if ($createSeeder) {
            $this->call('make:seeder', [
                'name' => "{$name}Seeder",
            ]);
        }

        if ($createFactory) {
            $this->call('make:factory', [
                'name' => "{$name}Factory",
                '--model' => $name,
            ]);
        }

        if ($createResource) {
            $this->call('make:resource', [
                'name' => "{$name}Resource",
            ]);
        }

        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the stub content with dynamic values.
     *
     * @param string $stubContent
     * @param string $name
     * @param string $namespace
     * @return string
     */
    protected function replacePlaceholders(string $stubContent, string $name, string $namespace): string
    {
        $factoryImport = class_exists("Database\Factories\\{$name}Factory") ? "use Database\Factories\\{$name}Factory;" : "";

        return str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ factoryImport }}', '{{ factory }}'],
            [$namespace, $name, $factoryImport, $this->generateFactory()],
            $stubContent
        );
    }

    /**
     * Generate factory string for the model.
     *
     * @return string
     */
    protected function generateFactory(): string
    {
        return "protected static function newFactory(): Factory {\n    return new \\Database\\Factories\\{$this->argument('name')}Factory();\n}";
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
            $this->call('php artisan make:api-response');
        }
    }
}
