<?php

namespace YourVendorName\YourPackageName;

use App\Console\Commands\MakeApiController;
use App\Console\Commands\MakeApiModel;
use App\Console\Commands\MakeApiRequest;
use App\Console\Commands\MakeApiResponse;
use App\Console\Commands\MakePaginationHelper;
use Illuminate\Support\ServiceProvider;

class ApiHelperProvider extends ServiceProvider
{
    public function register()
    {
        // Register the commands
        $this->commands([
            MakePaginationHelper::class,
            MakeApiResponse::class,
            MakeApiController::class,
            MakeApiModel::class,
            MakeApiRequest::class,
        ]);
    }

    public function boot()
    {
        // Publish stubs for customization
        $this->publishes([
            __DIR__ . '/Stubs/custom-request.stub' => base_path('stubs/api/custom-request.stub'),
            // Add other stubs here if needed
        ], 'stubs');
    }
}
